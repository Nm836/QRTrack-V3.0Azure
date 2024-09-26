<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject_code = $_POST['subject_code'];
    $week = $_POST['week'];
    $validity_interval = $_POST['validity_interval'];

    // Make a POST request to Flask
    $data = array(
        'subject_code' => $subject_code,
        'week' => $week,
        'validity_interval' => $validity_interval
    );

    $ch = curl_init('http://localhost:5000/generate_qr');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);

    $response_data = json_decode($response, true);
    if (isset($response_data['qr_code'])) {
        echo "<img src='data:image/png;base64," . $response_data['qr_code'] . "' alt='QR Code'>";
    } else {
        echo "Failed to generate QR code";
    }
}
?>
