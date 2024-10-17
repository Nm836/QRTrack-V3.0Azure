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
    include 'ConnectionCheck.php';
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
                            // Fetch current date and time using GETDATE()
                            $updateQuery = "SELECT GETDATE() AS timedate";
                            $stmt = $conn->prepare($updateQuery);
                            $stmt->execute();
                        
                            // Fetch the result (only one row expected from GETDATE())
                            $result = $stmt->fetch(PDO::FETCH_ASSOC);
                            if ($result) {
                                $timedate = $result['timedate'];
                                //echo $timedate; // Debug: print the current timestamp
                
                                // Get student ID and subcode from URL parameters
                                $StudentId = $_GET['StudentId'] ?? null;
                                $SubCode = $_GET['SubCode'] ?? null;
                
                                // Debugging prints
                                //echo $StudentId; // Debug: print StudentId
                                //echo $SubCode;   // Debug: print SubCode
                
                                // Validate if parameters are set
                                if (!$StudentId || !$SubCode) {
                                    throw new Exception("Missing StudentId or SubCode");
                                }
                
                                // Prepare the UPDATE query
                                $updateQuery1 = "UPDATE student_attendance_record 
                                                 SET LastEmailSent = ? 
                                                 WHERE StudentId = ? AND SubCode = ? 
                                                 AND LectureWeek = (
                                                     SELECT MAX(LectureWeek) 
                                                     FROM student_attendance_record 
                                                     WHERE StudentId = ? AND SubCode = ?
                                                 )";
                            
                                // Prepare the statement
                                $update = $conn->prepare($updateQuery1);
                            
                                // Bind the parameters and execute the query
                                if ($update->execute([$timedate, $StudentId, $SubCode, $StudentId, $SubCode])) {
                                    echo "<p>Record Update successful! </p>";
                                } else {
                                    echo "Error updating record.";
                                    print_r($update->errorInfo()); // Print error details
                                }
                            } else {
                                echo "Failed to fetch GETDATE() result.";
                            }
                        } catch (Exception $e) {
                            echo "Error: " . $e->getMessage();
                        }
                        ?>
                            </p>
        <?php endif; ?>
    </form>

        </div>
</body>
</html>
