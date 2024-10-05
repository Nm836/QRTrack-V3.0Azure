<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Location Validation</title>
</head>
<body>
    <h1>QR Code Location Validation</h1>
    <p id="message">Checking your location, please wait...</p>

    <!-- Hidden form fields to store latitude and longitude -->
    <form id="locationForm">
        <input type="hidden" id="lat" name="lat">
        <input type="hidden" id="lon" name="lon">
    </form>

    <script>
        // Geolocation script to populate latitude and longitude
        window.onload = function() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    document.getElementById('lat').value = position.coords.latitude;
                    document.getElementById('lon').value = position.coords.longitude;
                }, function(error) {
                    alert('Unable to retrieve your location. Please ensure location services are enabled and try again.');
                });
            } else {
                alert('Geolocation is not supported by this browser. Please use a different browser or device.');
            }
        };

        // Existing QR Code validation script
        const baseURL = 'https://qr-track.azurewebsites.net/SubmitAttendance.php';
        let hasValidated = false;

        function validateQRCode() {
            if (hasValidated) return; // Exit if already validated

            if (navigator.geolocation) {
                hasValidated = true; // Set the flag to true before the request

                navigator.geolocation.getCurrentPosition(function (position) {
                    const userLatitude = position.coords.latitude;
                    const userLongitude = position.coords.longitude;

                    // Extract URL parameters
                    const params = new URLSearchParams(window.location.search);
                    const fullURL = `${baseURL}?week=${params.get('week')}&subject_code=${params.get('subject_code')}&validity=${params.get('validity')}&latitude=${params.get('latitude')}&longitude=${params.get('longitude')}&userLat=${userLatitude}&userLon=${userLongitude}`;

                    // Redirect to the validation URL
                    window.location.href = fullURL;
                }, function (error) {
                    document.getElementById("message").innerText = "Error: Unable to access your location. Please enable location services.";
                });
            } else {
                document.getElementById("message").innerText = "Geolocation is not supported by this browser.";
            }
        }

        window.onload = validateQRCode; // Call the function on page load
    </script>

    <?php
    // Get the parameters from the URL
    $attendanceWeek = isset($_GET['week']) ? $_GET['week'] : null;
    $subjectCode = isset($_GET['subject_code']) ? $_GET['subject_code'] : null;
    $expiryTimestamp = isset($_GET['validity']) ? $_GET['validity'] : null;
    $allowedLatitude = isset($_GET['latitude']) ? $_GET['latitude'] : null;
    $allowedLongitude = isset($_GET['longitude']) ? $_GET['longitude'] : null;
    $userLatitude = isset($_GET['userLat']) ? $_GET['userLat'] : null;
    $userLongitude = isset($_GET['userLon']) ? $_GET['userLon'] : null;

    // Debugging Output
    echo "Attendance Week: " . htmlspecialchars($attendanceWeek) . "<br>";
    echo "Subject Code: " . htmlspecialchars($subjectCode) . "<br>";

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
    }
    ?>
</body>
</html>
