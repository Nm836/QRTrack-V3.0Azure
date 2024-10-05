<!DOCTYPE html>
<html>
<head>
    <title>QR Code Scanner Validation</title>
</head>
<body>
    <h1>QR Code Location Validation</h1>

    <p id="message">Checking your location, please wait...</p>

    <script>
        // Function to get user's location and validate QR code
        function validateQRCode() {
            // Check if the browser supports Geolocation API
            if (navigator.geolocation) {
                // Get the user's current location
                navigator.geolocation.getCurrentPosition(function (position) {
                    const userLatitude = position.coords.latitude;
                    const userLongitude = position.coords.longitude;

                    // Extract the parameters from the QR code URL
                    const params = new URLSearchParams(window.location.search);

                    // Prepare the validation URL with the user's location data
                    const baseURL = 'https://qr-track.azurewebsites.net/SubmitAttendance.php/';
                    const fullURL = `${baseURL}?week=${params.get('week')}&subject=${params.get('subject')}&expiry=${params.get('expiry')}&lat=${params.get('lat')}&lon=${params.get('lon')}&userLat=${userLatitude}&userLon=${userLongitude}`;

                    // Redirect to the validation URL on the server
                    window.location.href = fullURL;
                }, function (error) {
                    // Handle location access errors
                    document.getElementById("message").innerText = "Error: Unable to access your location. Please enable location services.";
                });
            } else {
                // Handle unsupported browser error
                document.getElementById("message").innerText = "Geolocation is not supported by this browser.";
            }
        }

        // Call the function to validate the QR code when the page loads
        window.onload = validateQRCode;
    </script>
	
	<?php
// Get the parameters from the URL
$attendanceWeek = isset($_GET['week']) ? $_GET['week'] : null;
$subjectCode = isset($_GET['subject']) ? $_GET['subject'] : null;
$expiryTimestamp = isset($_GET['expiry']) ? $_GET['expiry'] : null;
$allowedLatitude = isset($_GET['lat']) ? $_GET['lat'] : null;
$allowedLongitude = isset($_GET['lon']) ? $_GET['lon'] : null;
$userLatitude = isset($_GET['userLat']) ? $_GET['userLat'] : null;
$userLongitude = isset($_GET['userLon']) ? $_GET['userLon'] : null;

// Function to calculate the distance between two lat/lon points (Haversine formula)
function haversineDistance($lat1, $lon1, $lat2, $lon2) {
    $earthRadius = 6371; // Earth radius in kilometers

    $latDelta = deg2rad($lat2 - $lat1);
    $lonDelta = deg2rad($lon2 - $lon1);

    $a = sin($latDelta / 2) * sin($latDelta / 2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($lonDelta / 2) * sin($lonDelta / 2);
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

    return $earthRadius * $c; // Distance in kilometers
}

// Validate that all parameters are present
if (!$attendanceWeek || !$subjectCode || !$expiryTimestamp || !$allowedLatitude || !$allowedLongitude || !$userLatitude || !$userLongitude) {
    echo "Invalid QR Code!";
    exit();
}

// Get the current timestamp
$currentTimestamp = time();

// Check if the QR code is expired
if ($currentTimestamp > $expiryTimestamp) {
    echo "QR Code is expired!";
    exit();
}

// Define the allowed distance (e.g., 0.5 km radius)
$allowedDistance = 0.5; // in kilometers

// Calculate the distance between the user's location and the allowed location
$distance = haversineDistance($allowedLatitude, $allowedLongitude, $userLatitude, $userLongitude);

// Check if the user is within the allowed distance
if ($distance > $allowedDistance) {
    echo "QR Code is invalid: Outside of the allowed location!";
} else {
    echo "QR Code is valid for Week: $attendanceWeek, Subject: $subjectCode";
}
?>

</body>
</html>


