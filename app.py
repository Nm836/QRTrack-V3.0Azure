from flask import Flask, request, jsonify, render_template, send_file # type: ignore
import qrcode # type: ignore
import io
import random
import time
import base64
from geopy.distance import geodesic # type: ignore

app = Flask(__name__)

attendance = {}  # To store attendance records
student_records = {}  # To track which students have marked attendance for each session
student_ips = {}  # To track which IPs have submitted attendance for a session

# Set teacher's location
teacher_location = (-34.414056, 150.884317)  # Teacher's latitude and longitude

@app.route('/')
def index():
    return render_template('index.html')

@app.route('/generate_qr', methods=['POST'])
def generate_qr():
    subject_code = request.form['subject_code']
    week = request.form['week']
    session_id = random.randint(1000, 9999)
    session_url = f"http://localhost:5000/submit_attendance?session_id={session_id}"

    qr = qrcode.make(session_url)
    img_io = io.BytesIO()
    qr.save(img_io, 'PNG')
    img_io.seek(0)

    img_base64 = base64.b64encode(img_io.getvalue()).decode('utf-8')

    attendance[session_id] = {
        'status': 'valid',
        'time': time.time(),
        'session_id': session_id,
        'subject_code': subject_code,
        'week': week,
        'location': teacher_location
    }

    student_records[session_id] = []  # Reset student records for new session
    student_ips[session_id] = []  # Reset IP tracking for new session

    return jsonify({
        'qr_code': img_base64,
        'session_id': session_id
    })

@app.route('/download_qr/<session_id>')
def download_qr(session_id):
    if int(session_id) in attendance:
        session_info = attendance[int(session_id)]
        session_url = f"http://localhost:5000/submit_attendance?session_id={session_info['session_id']}"
        
        qr = qrcode.make(session_url)

        img_io = io.BytesIO()
        qr.save(img_io, 'PNG')
        img_io.seek(0)

        return send_file(img_io, mimetype='image/png', as_attachment=True, download_name=f'qr_code_{session_id}.png')
    else:
        return "Invalid session ID", 404

@app.route('/submit_attendance', methods=['GET', 'POST'])
def submit_attendance():
    session_id = request.args.get('session_id')
    if request.method == 'POST':
        student_name = request.form['student_name']
        student_number = request.form['student_number']
        subject_code = request.form['subject_code']
        student_lat = float(request.form['lat'])
        student_lon = float(request.form['lon'])
        student_ip = request.remote_addr  # Get the IP address of the student

        if session_id and int(session_id) in attendance:
            session_info = attendance[int(session_id)]
            time_diff = time.time() - session_info['time']

            if time_diff > 300:
                return render_template('attendance_result.html', result='QR code expired')

            if student_ip in student_ips[int(session_id)]:
                return render_template('attendance_result.html', result='Attendance already submitted from this device/IP.')

            if any(record['student_number'] == student_number for record in student_records[int(session_id)]):
                return render_template('attendance_result.html', result='You have already marked attendance for this session.')

            if subject_code != session_info['subject_code']:
                return render_template('attendance_result.html', result='Subject code does not match.')

            student_location = (student_lat, student_lon)
            teacher_location = session_info['location']
            distance = geodesic(student_location, teacher_location).meters

            if distance <= 200:
                session_info['status'] = 'present'
                student_records[int(session_id)].append({
                    'student_name': student_name,
                    'student_number': student_number,
                    'subject_code': subject_code,  # Store subject code with student record
                    'session_id': session_id  # Store session ID with student record
                })
                student_ips[int(session_id)].append(student_ip)
                return render_template('attendance_result.html', result='Attendance marked successfully')
            else:
                return render_template('attendance_result.html', result='You are too far from the teacher')

        return render_template('attendance_result.html', result='Invalid session')
    
    return render_template('submit_attendance.html', session_id=session_id)

@app.route('/teacher_dashboard')
def teacher_dashboard():
    return render_template('teacher_dashboard.html', student_records=student_records)

if __name__ == '__main__':
    app.run(debug=True)