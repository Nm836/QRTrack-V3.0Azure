<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Generated</title>
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
        img {
            display: block;
            margin: 20px auto;
            max-width: 100%;
            height: auto;
        }
        p {
            text-align: center;
            font-size: 18px;
        }
        a {
            text-decoration: none;
            color: #3498db;
            display: block;
            text-align: center;
            margin-top: 20px;
        }
        a:hover {
            color: #2980b9;
        }
        .button {
            display: block;
            text-align: center;
            padding: 10px 20px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            width: 200px;
            margin: 20px auto;
        }
        .button:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <h1>QR Code Generated Successfully!</h1>
    
    <p>This QR code is valid for 30 minutes.</p>
    <p>Use this QR code to mark attendance:</p>

<?php
// Include the QRcode library
include 'phpqrcode/qrlib.php';

// Function to generate QR Code with time-bound and location-based parameters
function generateQRCode($attendanceWeek, $subjectCode, $validityInMinutes = 30, $latitude = 0, $longitude = 0, $outputFile = 'qrcode.png') {
    // Base URL (directing to www.example.com with query parameters)
    $baseURL = 'https://qr-track.azurewebsites.net/SubmitAttendance.php';

    // Get the current timestamp and calculate the expiry time
    $currentTimestamp = time();
    $expiryTimestamp = $currentTimestamp + ($validityInMinutes * 60); // Validity in minutes

    // Combine attendanceWeek, subjectCode, expiryTimestamp, and location as query parameters
   $content = $baseURL . '?week=' . urlencode($attendanceWeek) . '&subject_code=' . urlencode($subjectCode) . '&validity=' . $expiryTimestamp . '&latitude=' . $latitude . '&longitude=' . $longitude;



    // Generate the QR code with the constructed URL
    QRcode::png($content, $outputFile, QR_ECLEVEL_L, 10, 2);

    // Return the file path or success message
    return $outputFile;
}

// Parameters (can be provided dynamically, e.g., via POST request)
/*
$attendanceWeek = isset($_POST['week']);
$subjectCode = isset($_POST['subject_code']);
$validityInMinutes = isset($_POST['validity']) ? $_POST['validity'] : 30; // Default validity 30 minutes
$outputFile = isset($_POST['outputFile']) ? $_POST['outputFile'] : 'qrcode.png';

$latitude = isset($_POST['latitude']) ? $_POST['latitude'] : 0; // Latitude of allowed location
$longitude = isset($_POST['longitude']) ? $_POST['longitude'] : 0; // Longitude of allowed location

$outputFile = isset($_POST['outputFile']) ? $_POST['outputFile'] : 'qrcode.png';
*/
if (isset($_POST['week']) &&  isset($_POST['subject_code']) && isset($_POST['validity'])  && isset($_POST['latitude']) && isset($_POST['longitude'])
 	&& isset($_POST['outputFile'])? $_POST['outputFile'] : 'qrcode.png'){
		
$attendanceWeek = $_POST['week'];
$subjectCode = $_POST['subject_code'];
$validityInMinutes = $_POST['validity'] ? $_POST['validity'] : 30; // Default validity 30 minutes


$latitude = $_POST['latitude'] ? $_POST['latitude'] : 0; // Latitude of allowed location
$longitude = $_POST['longitude'] ? $_POST['longitude'] : 0; // Longitude of allowed location

$outputFile = isset($_POST['outputFile']) ? $_POST['outputFile'] : 'qrcode.png';	

// Call the function to generate the QR code
$qrCodeFile = generateQRCode($attendanceWeek, $subjectCode, $validityInMinutes, $latitude, $longitude, $outputFile);

// Output the generated QR code
echo "QR Code has been generated successfully! <br>";
echo "<img src='$qrCodeFile' alt='QR Code'>";
		
	}


?>
    

    <a href="" class="button">Download QR Code</a>
    <a href="/" class="button">Back to Home</a>
</body>
</html>
