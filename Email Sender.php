<?php
session_start();
require("email.php");
include 'ConnectionCheck.php';

if (isset($_POST['submit'])) {
    if (empty($_POST['email']) || empty($_POST['subject']) || empty($_POST['message'])) {
        $response = "All fields are required";
    } else {

$StudentID = $_GET['StudentId'];
$subjectCode = $_GET['SubCode'];

try {
    // Fetch the student's email from Login_Record
    $queryEmail = "SELECT Email FROM Login_Record WHERE Student_StaffId = :studentID";
    $stmtEmail = $conn->prepare($queryEmail);
    $stmtEmail->bindParam(':studentID', $StudentID);
    $stmtEmail->execute();
    $studentEmail = $stmtEmail->fetchColumn();

    // If email is found, proceed to send the email
    if ($studentEmail) {
        // Send email using the provided data
        $response = sendMail($studentEmail, $_POST['subject'], $_POST['message']);

        if ($response) {
            // Update the LastEmailSent field in Student_Attendance_Record
            try {
                $lectureWeekQuery = "
                SELECT MAX(LectureWeek) AS MaxLectureWeek 
                FROM Student_Attendance_Record 
                WHERE StudentId = :studentID 
                AND SubCode = :subjectCode";
            
            // Prepare and execute the query
            $stmtLectureWeek = $conn->prepare($lectureWeekQuery);
            $stmtLectureWeek->bindParam(':studentID', $StudentID);
            $stmtLectureWeek->bindParam(':subjectCode', $subjectCode);
            $stmtLectureWeek->execute();
            
            // Fetch the result
            $maxLectureWeek = $stmtLectureWeek->fetchColumn();
            
            if ($maxLectureWeek !== false) {
                // Step 2: Update the LastEmailSent field using the fetched MaxLectureWeek
                $updateQuery = "
                    UPDATE Student_Attendance_Record 
                    SET LastEmailSent = GETDATE() 
                    WHERE StudentId = :studentID 
                    AND SubCode = :subjectCode 
                    AND LectureWeek = :maxLectureWeek";
                
                // Prepare and execute the update query
                $stmtUpdate = $conn->prepare($updateQuery);
                $stmtUpdate->bindParam(':studentID', $StudentID);
                $stmtUpdate->bindParam(':subjectCode', $subjectCode);
                $stmtUpdate->bindParam(':maxLectureWeek', $maxLectureWeek);
                $stmtUpdate->execute();


            echo "<p><em>Email sent successfully and record updated.</em></p>";
        }} catch (PDOException $e) {
            // Catch and display any PDO errors
            echo "<p>Failed to update the record: " . $e->getMessage() . "</p>";
        }


        } else {
            echo "<p>Failed to send email: $response</p>";
        }
    } else {
        echo "<p>No email found for the student ID: $StudentID</p>";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Email - QR Track</title>
    <link rel="stylesheet" href="QR EMAIL.css">
</head>
<body>
<header>
    <div class='container'>
    <h1>Send Email - QR Track</h1>    
    <form action='6_StaffPage.php' method='POST' class='back-form'>
        <input type='submit' name='back' value='Back' class='back-button'>
    </form>
    <form action='index.php' method='POST' class='logout-form'>
        <input type='submit' name='logout' value='Log Out' class='logout-button'>
    </form>
</header>
<main>
    

    <?php
    $userID = $_SESSION['userid']; // User ID
    include '7_StaffClass.php'; // Admin Class
    $StaffView = new Staff();
    $StaffView->nameHeader($userID);
    ?>

    <form action="" method="POST" enctype="multipart/form-data">
        <label for="email">Student's Email-ID:</label>
        <input type="email" name="email" placeholder="Enter student's email" value="" class='input-textQR' >

        <label for="subject">Subject:</label>
        <input type="text" name="subject" placeholder="Enter subject" value="" class='input-textQR'>

        <label for="message">Message:</label>
        <textarea name="message" placeholder="Enter your message here" class='input-textQR'></textarea>

        <button type="submit" name="submit" class='buttons-QR'>Submit</button>
    </form>
</main>
</div>
</body>
</html>
