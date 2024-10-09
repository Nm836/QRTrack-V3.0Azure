<?php
session_start();
require("email.php");
include 'ConnectionCheck.php';
echo "
<header>
    <form action='6_StaffPage.php' method='POST'>
        <input type='submit' name='back' value='Back' class='header-btn'>
    </form>
    <form action='index.php' method='POST'>
        <input type='submit' name='logout' value='Log Out' class='header-btn'>
    </form>
</header>
";

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
                

            echo "<p>Email sent successfully and record updated.</p>";
        } catch (PDOException $e) {
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
    <style>
        /* Global Styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #343a40;
            padding: 10px;
            text-align: right;
        }

        header form {
            display: inline;
        }

        .header-btn {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 5px;
            font-size: 14px;
            margin: 0 5px;
        }

        .header-btn:hover {
            background-color: #0056b3;
        }

        main {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #343a40;
        }

        form {
            margin-top: 20px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 8px;
        }

        input[type="email"],
        input[type="text"],
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 16px;
        }

        textarea {
            height: 150px;
        }

        button[type="submit"] {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            display: inline-block;
        }

        button[type="submit"]:hover {
            background-color: #218838;
        }

        .response-message {
            font-size: 16px;
            text-align: center;
            color: #dc3545;
        }

        .success-message {
            color: #28a745;
        }
    </style>
</head>
<body>

<main>
    <h1>Send Email - QR Track</h1>

    <?php
    $userID = $_SESSION['userid']; // User ID
    include '7_StaffClass.php'; // Admin Class
    $StaffView = new Staff();
    $StaffView->nameHeader($userID);
    ?>

    <form action="" method="POST" enctype="multipart/form-data">
        <label for="email">Student's Email-ID:</label>
        <input type="email" name="email" placeholder="Enter student's email" value="">

        <label for="subject">Subject:</label>
        <input type="text" name="subject" placeholder="Enter subject" value="">

        <label for="message">Message:</label>
        <textarea name="message" placeholder="Enter your message here"></textarea>

        <button type="submit" name="submit">Submit</button>
    </form>
</main>

</body>
</html>
