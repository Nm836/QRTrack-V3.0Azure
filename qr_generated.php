<?php
session_start();

$userID = $_SESSION['userid']; //User id

        include '7_StaffClass.php'; //Admin Class
        $StaffView = new Staff();

        
?>
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
            color: white;
        }
    </style>
</head>
<body>
    
    <h1>QR Code Generated Successfully!</h1>
    
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
        
//Back and Logout button
echo "<form action='6_StaffPage.php?";
echo SID;
echo "' method='POST' style ='display:flex;'>
            <input type='submit' name='back' value='Back'  class='button'>
        </form>
        
        <form action='index.php' method='POST' style='display:flex;'>
            <input type='submit' name='logout' value='Log Out'  class='button'>
        </form>";

// Output the generated QR code
        
    echo "<p>This QR code is valid for {$validityInMinutes} minutes.</p>";
    echo "<p>Use this QR code to mark attendance:</p> ";

    echo "<img src='$qrCodeFile' alt='QR Code'>";
		
	}

echo "<a href='$qrCodeFile' download='QR_Code_{$subjectCode}_Week_{$attendanceWeek}.png' class='button'>Download QR Code</a>";
?>


</body>
</html>
