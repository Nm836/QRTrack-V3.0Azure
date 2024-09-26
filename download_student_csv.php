<?php
session_start();
include '7_StaffClass.php';

if (isset($_POST['download_student_csv'])) {
    $StudentSessionID = $_POST['StudentSessionID'];
    
    $StaffView = new Staff();
    $studentData = $StaffView->getAttendanceDataForStudentCSV($StudentSessionID); // Fetch data for CSV
    
    // Define the headers for CSV download
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="student_attendance_'.$StudentSessionID.'.csv"');
    
    // Open output stream to output CSV
    $output = fopen('php://output', 'w');
    
    // Output CSV column headers
    fputcsv($output, array('Student ID', 'Full Name', 'Sub Code', 'Lecture Week', 'Attendance'));
    
    // Output student data rows
    foreach ($studentData as $row) {
        fputcsv($output, $row);
    }
    
    // Close output stream
    fclose($output);
    exit();
}
?>
