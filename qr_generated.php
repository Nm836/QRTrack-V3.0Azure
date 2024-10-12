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
    <link rel="stylesheet" href="EmailCSSFile.css">
</head>
<body>
<div class='container'>
<header>
        <h1>QR Code Generated Successfully!</h1>    
        <form action='6_StaffPage.php?<?php echo SID;?>' method='POST' class='back-form'>
            <input type='submit' name='back' value='Back'  class='back-button'>
        </form>
        
        <form action='index.php' method='POST' class='logout-form'>
            <input type='submit' name='logout' value='Log Out'  class='logout-button'>
        </form>
    </header>

    
    
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
        

// Output the generated QR code
        
    echo "<p>This QR code is valid for {$validityInMinutes} minutes.</p>";
    echo "<p>Use this QR code to mark attendance:</p> ";

    echo "<img src='$qrCodeFile' alt='QR Code'>";
		
	}

echo "<a href='$qrCodeFile' download='QR_Code_{$subjectCode}_Week_{$attendanceWeek}.png' class='buttons'>Download QR Code</a>";
?>

</div>
</body>
</html>
