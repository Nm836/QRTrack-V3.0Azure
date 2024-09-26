from flask import Flask, request, jsonify, render_template
import qrcode
import io
import time
import base64
import mysql.connector  # MySQL integration
from geopy.distance import geodesic

app = Flask(__name__)

# Set teacher's location (latitude, longitude)
teacher_location = (-34.414056, 150.884317)

# Connect to MySQL database
db = mysql.connector.connect(
    host="localhost",
    user="root",
    password="your_password",
    database="your_database"
)

cursor = db.cursor()

# Store attendance session data
attendance_sessions = {}

@app.route('/')
def index():
    return render_template('index.html')

# Generate QR Code based on subject, week, and validity
@app.route('/generate_qr', methods=['POST'])
def generate_qr():
    subject_code = request.form['subject_code']
    week = request.form['week']
    validity_interval = int(request.form['validity_interval'])  # minutes
    session_id = random.randint(1000, 9999)
    session_url = f"http://localhost:5000/submit_attendance?session_id={session_id}"

    # Generate QR code
    qr = qrcode.make(session_url)
    img_io = io.BytesIO()
    qr.save(img_io, 'PNG')
    img_io.seek(0)
    img_base64 = base64.b64encode(img_io.getvalue()).decode('utf-8')

    # Save session to dict with timestamp
    attendance_sessions[session_id] = {
        'subject_code': subject_code,
        'week': week,
        'generated_time': time.time(),
        'validity_interval': validity_interval,
        'location': teacher_location
    }

    return jsonify({
        'qr_code': img_base64,
        'session_id': session_id
    })

# Submit attendance
@app.route('/submit_attendance', methods=['POST'])
def submit_attendance():
    session_id = int(request.form['session_id'])
    student_number = request.form['student_number']
    student_name = request.form['student_name']
    student_lat = float(request.form['lat'])
    student_lon = float(request.form['lon'])

    # Check if session exists and is still valid
    if session_id in attendance_sessions:
        session_info = attendance_sessions[session_id]
        elapsed_time = (time.time() - session_info['generated_time']) / 60  # in minutes

        # Check validity
        if elapsed_time > session_info['validity_interval']:
            return "QR Code expired"

        # Calculate distance between student and teacher
        student_location = (student_lat, student_lon)
        distance = geodesic(teacher_location, student_location).meters

        if distance > 200:  # Assuming a 200-meter proximity limit
            return "Too far from the classroom"

        # Insert attendance record into MySQL
        cursor.execute("SELECT student_id FROM students WHERE student_number = %s", (student_number,))
        result = cursor.fetchone()
        
        if result:
            student_id = result[0]
        else:
            # If student doesn't exist, insert them
            cursor.execute("INSERT INTO students (student_name, student_number, subject_code) VALUES (%s, %s, %s)",
                           (student_name, student_number, session_info['subject_code']))
            db.commit()
            student_id = cursor.lastrowid
        
        # Record attendance
        cursor.execute("INSERT INTO attendance (student_id, subject_code, session_id, week, attendance_time, status) "
                       "VALUES (%s, %s, %s, %s, NOW(), %s)", 
                       (student_id, session_info['subject_code'], session_id, session_info['week'], 'present'))
        db.commit()

        return "Attendance recorded successfully"
    else:
        return "Invalid session"

if __name__ == '__main__':
    app.run(debug=True)
