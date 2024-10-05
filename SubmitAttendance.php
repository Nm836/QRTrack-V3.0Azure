<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Submit Attendance</title>
<style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        h1 {
            color: #2c3e50;
            text-align: center;
        }
        .container {
            max-width: 400px;
            margin: auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        label {
            margin-top: 1em;
            display: block;
        }
        input[type="text"], input[type="number"] {
            width: 100%;
            padding: 0.5em;
            margin-top: 0.2em;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            margin-top: 1em;
            padding: 0.7em;
            border: none;
            border-radius: 4px;
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
            width: 100%;
        }
        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <h1>QR Code Location Validation</h1>
    <p id="message">Click the button below to check your location and validate the QR code.</p>

    <!-- Button to trigger location check -->
    <button id="checkLocationBtn">Allow Location Access</button>

    <!-- Hidden form fields to store latitude and longitude -->
    <form id="locationForm">
        <input type="hidden" id="lat" name="lat">
        <input type="hidden" id="lon" name="lon">
    </form>

    <script>
        // Geolocation script to populate latitude and longitude on button click
        document.getElementById('checkLocationBtn').onclick = function() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    document.getElementById('lat').value = position.coords.latitude;
                    document.getElementById('lon').value = position.coords.longitude;

                    // Proceed with QR code validation after getting the location
                    validateQRCode();
                }, function(error) {
                    alert('Unable to retrieve your location. Please ensure location services are enabled and try again.');
                });
            } else {
                alert('Geolocation is not supported by this browser. Please use a different browser or device.');
            }
        };

        // QR Code validation script
        const baseURL = 'https://qr-track.azurewebsites.net/SubmitAttendance.php';
        let hasValidated = false;

        function validateQRCode() {
            if (hasValidated) return; // Exit if already validated

            const userLatitude = document.getElementById('lat').value;
            const userLongitude = document.getElementById('lon').value;

            // Extract URL parameters
            const params = new URLSearchParams(window.location.search);
            const fullURL = `${baseURL}?week=${params.get('week')}&subject_code=${params.get('subject_code')}&validity=${params.get('validity')}&latitude=${params.get('latitude')}&longitude=${params.get('longitude')}&userLat=${userLatitude}&userLon=${userLongitude}`;

            // Redirect to the validation URL
            window.location.href = fullURL;
        }
    </script>

    <?php
    session_start();
    // Get the parameters from the URL
    $attendanceWeek = isset($_GET['week']) ? $_GET['week'] : null;
    $subjectCode = isset($_GET['subject_code']) ? $_GET['subject_code'] : null;
    $expiryTimestamp = isset($_GET['validity']) ? $_GET['validity'] : null;
    $allowedLatitude = isset($_GET['latitude']) ? $_GET['latitude'] : null;
    $allowedLongitude = isset($_GET['longitude']) ? $_GET['longitude'] : null;
    $userLatitude = isset($_GET['userLat']) ? $_GET['userLat'] : null;
    $userLongitude = isset($_GET['userLon']) ? $_GET['userLon'] : null;

    

    // Function to calculate distance using Haversine formula
    function haversineDistance($lat1, $lon1, $lat2, $lon2) {
        $earthRadius = 6371; // Earth radius in kilometers
        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);
        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($lonDelta / 2) * sin($lonDelta / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c; // Distance in kilometers
    }

    // Validate parameters
    if (!$attendanceWeek || !$subjectCode || !$expiryTimestamp || !$allowedLatitude || !$allowedLongitude || !$userLatitude || !$userLongitude) {
        echo "Invalid QR Code!";
        exit();
    }

    // Check QR code expiration
    if (time() > $expiryTimestamp) {
        echo "QR Code is expired!";
        exit();
    }

    // Define the allowed distance (e.g., 0.5 km radius)
    $allowedDistance = 0.5; // in kilometers

    // Calculate distance
    $distance = haversineDistance($allowedLatitude, $allowedLongitude, $userLatitude, $userLongitude);

    // Validate location
    if ($distance > $allowedDistance) {
        echo "QR Code is invalid: Outside of the allowed location!";
    } else {
        echo "QR Code is valid for Week: $attendanceWeek, Subject: $subjectCode";
        // Debugging Output
        $mark_Week = htmlspecialchars($attendanceWeek);
        $mark_SubCode = htmlspecialchars($subjectCode);


    
    ?>
<div class="container">
        <h1>Submit Attendance</h1>
        <form method="POST">
            <label for="student_name">Student Name:</label>
            <input type="text" id="student_name" name="student_name" required>
            
            <label for="student_number">Student Number:</label>
            <input type="text" id="student_number" name="student_number" required>
     
    <input type="hidden" name="week" value="<?php echo htmlspecialchars($attendanceWeek); ?>">
    <input type="hidden" name="subject_code" value= "<?php echo htmlspecialchars($subjectCode); ?>">
    

            <input type="submit" name="Mark_Attendance" value ="Submit Attendance" />
        </form>
    </div>
    
<?php

/*        if (isset($_POST['Mark_Attendance'])) {
            if (isset($_POST['student_name']) || isset($_POST['student_number'])) {
                
                $student_name = stripslashes(trim(strtolower($_POST['student_name'])));
                $student_number = stripslashes(trim($_POST['student_number']));
                $week = $_POST['week'];
                $subject_code = $_POST['subject_code'];
             
                try {
                    // Include the connection script
                    include 'ConnectionCheck.php';

                    // Prepared statement to check if the ID already exists
                    $AddDataQuery = "INSERT INTO Student_Attendance_Record (StudentId, Name, SubCode, LectureWeek, AttendanceNum, LastEmailSent)
                    VALUES (:StudentID,:StudentName ,:SubCode,:LectWeek , 'Present', NULL)";
                    
                    $stmt = $conn->prepare($AddDataQuery);
                    $stmt->bindParam(':StudentID', $student_number);
                    $stmt->bindParam(':StudentName', $student_name);
                    $stmt->bindParam(':SubCode', $subject_code);
                    $stmt->bindParam(':LectWeek', $week);
                    $stmt->execute();
                    //$row = $stmt->fetchColumn();
                    echo ucfirst{$student_name}. "your Attendance has been Marked.";
                    
                } catch (PDOException $e) {
                    die("Error: " . $e->getMessage());
                    ++$errorcount;
                }
            }
            }*/
            }

?>

</body>
</html>
