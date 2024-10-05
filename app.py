from datetime import datetime, timedelta
from flask import Flask, request, jsonify, render_template, send_file  # type: ignore
import qrcode  # type: ignore
import io
import random
import base64
from geopy.distance import geodesic  # type: ignore
import mysql.connector  # type: ignore
from mysql.connector import Error  # type: ignore

import pyodbc

import logging

app = Flask(__name__)

# Configure logging
logging.basicConfig(level=logging.DEBUG)

# MySQL connection function ### chnagethis to connection string in used for python
def get_db_connection():
    try:
     #   connection = mysql.connector.connect(
      #      host="localhost",  # Change to your DB host
       #     user="root",  # MySQL username
        #    password="yourpassword",  # MySQL password
         #   database="attendance_system"  # Name of the database
       # )
     #  return connection
  #  except Error as e:
      #  logging.error(f"Error connecting to MySQL: {e}")
        #return None

        connection = pyodbc.connect(
            'DRIVER={ODBC Driver 17 for SQL Server};'  # Ensure the driver is installed
            'SERVER=qrtrack-server.database.windows.net;'  # Your server name without "tcp:"
            'PORT=1433;'  # Default Azure SQL port
            'DATABASE=qrtrack_sample;'  # Your database name
            'UID=Nm836;'  # Your Azure SQL username
            'PWD=Capstone@123;'  # Your password
        )

        
     # If connection is successful
        return connection

    except pyodbc.Error as e:
        logging.error(f"Error connecting to Azure SQL: {e}")
        return None

# Teacher's location
teacher_location = ('lat', 'lon')

@app.route('/')
def index():
    return render_template('QR_Generator_Info.html')

@app.route('/generate_qr', methods=['GET', 'POST'])
def generate_qr():
    subject_code = request.form['subject_code']
    week = request.form['week']
    session_id = random.randint(1000, 9999)
    session_url = f"https://qr-track.azurewebsites.net/submit_attendance?session_id={session_id}"

    # Generate QR code
    qr = qrcode.make(session_url)
    img_io = io.BytesIO()
    qr.save(img_io, 'PNG')
    img_io.seek(0)

    img_base64 = base64.b64encode(img_io.getvalue()).decode('utf-8')

    # Store the timestamp when the QR code is generated
    created_at = datetime.now()

    # Insert the new session into the attendance_sessions table
    connection = get_db_connection()
    if connection:
        cursor = connection.cursor()

        query = """
        INSERT INTO attendance_sessions (session_id, subject_code, week, location_lat, location_lon, created_at)
        VALUES (%s, %s, %s, %s, %s, %s)
        """
        cursor.execute(query, (session_id, subject_code, week, teacher_location[0], teacher_location[1], created_at))
        connection.commit()
        cursor.close()
        connection.close()

    return render_template('qr_generated.html', qr_code=img_base64, session_id=session_id, session_url=session_url)

@app.route('/download_qr/<session_id>')
def download_qr(session_id):
    connection = get_db_connection()
    if connection:
        cursor = connection.cursor()
        query = "SELECT subject_code FROM attendance_sessions WHERE session_id = %s"
        cursor.execute(query, (session_id,))
        result = cursor.fetchone()

        if result:
            subject_code = result[0]
            session_url = f"https://qr-track.azurewebsites.net/submit_attendance?session_id={session_id}"

            # Generate the QR code again for download
            qr = qrcode.make(session_url)
            img_io = io.BytesIO()
            qr.save(img_io, 'PNG')
            img_io.seek(0)

            cursor.close()
            connection.close()

            return send_file(img_io, mimetype='image/png', as_attachment=True, download_name=f'qr_code_{subject_code}_{session_id}.png')
        else:
            return "Invalid session ID", 404

@app.route('/submit_attendance', methods=['GET', 'POST'])
def submit_attendance():
    session_id = request.args.get('session_id')
    if not session_id:
        return render_template('attendance_result.html', result='Invalid session.', success=False, session_id=session_id)
    
    if request.method == 'POST':
        student_name = request.form['student_name']
        student_number = request.form['student_number']
        week = request.form['week']
        subject_code = request.form['subject_code']  # Capture the subject code
        student_lat = float(request.form['lat'])
        student_lon = float(request.form['lon'])
        student_ip = request.remote_addr

        # Debugging: Print the captured values
        logging.debug("Received data for attendance submission:")
        logging.debug(f"Student Name: {student_name}")
        logging.debug(f"Student Number: {student_number}")
        logging.debug(f"Week: {week}")
        logging.debug(f"Subject Code: {subject_code}")
        logging.debug(f"Session ID: {session_id}")
        logging.debug(f"Latitude: {student_lat}, Longitude: {student_lon}")
        logging.debug(f"IP Address: {student_ip}")

        # Location of the student
        student_location = (student_lat, student_lon)

        connection = get_db_connection()
        if connection:
            cursor = connection.cursor()

            # Check if the session ID exists and is valid
            query = "SELECT subject_code, created_at FROM attendance_sessions WHERE session_id = %s"
            cursor.execute(query, (session_id,))
            session_info = cursor.fetchone()

            if not session_info:
                cursor.close()
                connection.close()
                return render_template('attendance_result.html', result='Invalid session ID.', success=False, session_id=session_id)

            subject_code_db, created_at = session_info

            # Check if the QR code has expired (30 minutes)
            current_time = datetime.now()
            if (current_time - created_at) > timedelta(minutes=30):
                cursor.close()
                connection.close()
                return render_template('attendance_result.html', result='QR code expired.', success=False, session_id=session_id)

            # Validate subject code
            if subject_code != subject_code_db:
                cursor.close()
                connection.close()
                return render_template('attendance_result.html', result='Subject code does not match.', success=False, session_id=session_id)

            # Check if student is within 200 meters of the teacher's location
            distance = geodesic(student_location, teacher_location).meters

            if distance > 200:
                cursor.close()
                connection.close()
                return render_template('attendance_result.html', result='You are too far from the teacher to submit attendance.', success=False, session_id=session_id)

            # Check if the student has already submitted within the last 30 minutes
            time_limit = datetime.now() - timedelta(minutes=30)
            query = """
            SELECT COUNT(*)
            FROM attendance_records
            WHERE student_number = %s AND session_id = %s AND timestamp > %s
            """
            cursor.execute(query, (student_number, session_id, time_limit))
            already_submitted = cursor.fetchone()[0]

            if already_submitted > 0:
                cursor.close()
                connection.close()
                return render_template('attendance_result.html', result='You have already submitted attendance.', success=False, session_id=session_id)

            # Check if attendance has been submitted from this IP address
            query = """
            SELECT COUNT(*)
            FROM attendance_records
            WHERE ip_address = %s AND session_id = %s
            """
            cursor.execute(query, (student_ip, session_id))
            same_ip_submission = cursor.fetchone()[0]

            if same_ip_submission > 0:
                cursor.close()
                connection.close()
                return render_template('attendance_result.html', result='You cannot submit attendance from this device again.', success=False, session_id=session_id)

            # Insert attendance record if everything is valid
            query = """
            INSERT INTO attendance_records (student_name, student_number, session_id, week, subject_code, latitude, longitude, ip_address, timestamp)
            VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)
            """
            cursor.execute(query, (student_name, student_number, session_id, week, subject_code, student_lat, student_lon, student_ip, datetime.now()))
            connection.commit()

            # Insert attendance record into Student_Attendance_Record
            query = """
            INSERT INTO Student_Attendance_Record (StudentId, Name, SubCode, LectureWeek, AttendanceNum, LastEmailSent)
            VALUES (%s, %s, %s, %s, 'Present', NULL)
            """
            cursor.execute(query, (student_number, student_name, subject_code, week))
            connection.commit()

            cursor.close()
            connection.close()

            return render_template('attendance_result.html', result='Attendance marked successfully!', success=True, session_id=session_id)
    
    return render_template('submit_attendance.html', session_id=session_id)

@app.route('/teacher_dashboard')
def teacher_dashboard():
    connection = get_db_connection()
    if connection:
        cursor = connection.cursor()
        # Fetch all records from the attendance_records table
        query = """SELECT StudentId, Name, LectureWeek, SubCode, AttendanceNum, LastEmailSent FROM Student_Attendance_Record ORDER BY timestamp ASC  -- Sort by timestamp in ascending order"""
        cursor.execute(query)
        records = cursor.fetchall()  # Fetch all rows from the query result
        cursor.close()
        connection.close()

        # Render the teacher dashboard page, passing the records
        return render_template('teacher_dashboard.html', records=records)
    return "Error connecting to the database"

#if __name__ == '__main__':
 #   app.run(debug=True)

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=8000)
