<?php
session_start();
require("email.php");

echo "
<header>
    <form action='6_StaffPage.php' method='POST' class='back-form'>
        <input type='submit' name='back' value='Back' class='back-button'>
    </form>
    <form action='4.LoginPage.php' method='POST' class='logout-form'>
        <input type='submit' name='logout' value='Log Out' class='logout-button'>
    </form>
    
</header>
";

if (isset($_POST['submit'])) {
    if (empty($_POST['email']) || empty($_POST['subject']) || empty($_POST['message'])) {
        $response = "All fields are required";
    } else {
        $response = sendMail($_POST['email'], $_POST['subject'], $_POST['message']);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Email - QR Track</title>
    <link rel="stylesheet" href="EmailCSSFile.css">

    <style>


    </style>
</head>
<body>
<div class='container'>

<h1>Send Email - QR Track</h1>    

    <?php
    $userID = $_SESSION['userid']; // User ID
    include '7_StaffClass.php'; // Admin Class
    $StaffView = new Staff();
    $StaffView->nameHeader($userID);
    ?>

    <form action="" method="POST" enctype="multipart/form-data">
        <label for="email">Student's Email-ID:</label>
        <input type="email" name="email" placeholder="Enter student's email" value="" class="input-text">

        <label for="subject">Subject:</label>
        <input type="text" name="subject" placeholder="Enter subject" value="" class="input-text">

        <label for="message">Message:</label>
        <textarea name="message" placeholder="Enter your message here" class="input-text"></textarea>
<br /><br />
        <button type="submit" name="submit" class="buttons">Send</button>

        <?php if (isset($response)): ?>
            <p class="response-message <?php echo $response == 'Success' ? 'success-message' : ''; ?>">
                <?php echo $response == 'Success' ? 'Email was sent successfully' : $response; 
                try {		
                    $StudenId=$_GET['StudentId'];
                    $SubCode=$_GET['SubCode'];
                
                $updateQuery = "UPDATE student_attendance_record 
                SET LastEmailSent = CURRENT_TIMESTAMP 
                WHERE StudentId = :studentId , SubCode = :Subcode
                AND LectureWeek = (
                    SELECT MAX(LectureWeek) 
                    FROM student_attendance_record 
                    WHERE StudentId = :studentId
                )";

                    // Prepare the statement
                    $stmt = $conn->prepare($updateQuery);

                    // Bind the student ID
				$stmt->bindParam(':studentId', $StudenId, PDO::PARAM_STR);
    				$stmt->bindParam(':Subcode', $SubCode, PDO::PARAM_STR);

            
        // Execute the query
                    $stmt->execute();

                    echo "Record updated successfully.";
                
                
            
            
            
        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
        }
            
                
                
                
                ?>
            </p>
        <?php endif; ?>
    </form>

        </div>
</body>
</html>
