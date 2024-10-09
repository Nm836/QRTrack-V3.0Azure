<?php
session_start();
require("email.php");

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
    // Ensure all fields are filled
    if (empty($_POST['email']) || empty($_POST['subject']) || empty($_POST['message'])) {
        $response = "All fields are required";
    } else {
        // 1. Check the student's email from the Login_Record table
        $studentID = $_POST['EmailID'];
        $subjectCode = $_POST['subject_code']; // Assuming subject_code is posted

        // Include the connection to the database
        include 'ConnectionCheck.php'; 

        try {
            // Query to get the student's email from the Login_Record table
            $queryEmail = "SELECT Email FROM Login_Record WHERE Student_StaffId = :studentID";
            $stmtEmail = $conn->prepare($queryEmail);
            $stmtEmail->bindParam(':studentID', $studentID);
            $stmtEmail->execute();
            $studentEmail = $stmtEmail->fetchColumn();

            // If email is found, proceed to send the email
            if ($studentEmail) {
                // 2. Send email
                $response = sendMail($studentEmail, $_POST['subject'], $_POST['message']);
                
                // If the email was successfully sent, update the LastEmailSent field
                if ($response == "success") {
                    // 3. Update LastEmailSent in the Student_Attendance_Record table
                    $updateQuery = "
                        UPDATE Student_Attendance_Record 
                        SET LastEmailSent = GETDATE()
                        WHERE StudentId = :studentID 
                        AND SubCode = :subjectCode 
                        AND LectureWeek = (
                            SELECT MAX(LectureWeek) FROM Student_Attendance_Record
                            WHERE StudentId = :studentID 
                            AND SubCode = :subjectCode
                        )";
                    
                    $stmtUpdate = $conn->prepare($updateQuery);
                    $stmtUpdate->bindParam(':studentID', $studentID);
                    $stmtUpdate->bindParam(':subjectCode', $subjectCode);
                    $stmtUpdate->execute();

                    echo "<p>Email sent successfully and record updated.</p>";
                } else {
                    echo "<p>Failed to send email: $response</p>";
                }
            } else {
                echo "<p>No email found for the student ID: $studentID</p>";
            }

        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    if (isset($_POST['select'])) {
        $emailID = $_POST['EmailID'];
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
        /* Add your existing CSS styling here */
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
        <input type="email" name="email" placeholder="Enter student's email" value="<?php echo isset($emailID) ? htmlspecialchars($emailID) : ''; ?>">

        <label for="subject">Subject:</label>
        <input type="text" name="subject" placeholder="Enter subject" value="">

        <label for="message">Message:</label>
        <textarea name="message" placeholder="Enter your message here"></textarea>

        <button type="submit" name="submit">Submit</button>

        <?php if (isset($response)): ?>
            <p class="response-message <?php echo $response == 'success' ? 'success-message' : ''; ?>">
                <?php echo $response == 'success' ? 'Email was sent successfully' : $response; ?>
            </p>
        <?php endif; ?>
    </form>
</main>

</body>
</html>
