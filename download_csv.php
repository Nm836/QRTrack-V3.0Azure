<?php
session_start();
include '7_StaffClass.php';

if (isset($_POST['download_csv'])) {
    $StaffView = new Staff();
    $studentData = $StaffView->getAttendanceDataForCSV(); // Fetch student data for CSV

    // Define the headers for CSV download
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="student_data.csv"');

    // Create a file pointer connected to the output stream
    $output = fopen('php://output', 'w');

    // Output column headings
    fputcsv($output, array('Student ID', 'Full Name', 'Attendance Percentage'));

    // Output student data rows
    foreach ($studentData as $row) {
        fputcsv($output, $row);
    }

    fclose($output);
    exit();
}
?>
