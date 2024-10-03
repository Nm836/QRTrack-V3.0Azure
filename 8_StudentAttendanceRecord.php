<?php
session_start();
include '7_StaffClass.php'; //Admin Class
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard - QR Track</title>
    
    <link rel="stylesheet" href="StudentRecordstylecss.css"> <!-- Link to the CSS file -->
</head>
<body>
    <div class="container">
        <header>
            <h1>Student Attendance Display</h1>
        </header>

        <div class="content">
            <?php
            echo "<form action='6_StaffPage.php?<?php echo SID;?>' method='POST'>
                <input type='submit' name='back' value='Back' class='btn-back'>
            </form>";
            echo "<form action='index.php' method='POST' style="display:inline;">
                <input type='submit' name='logout' value='Log Out' class="logout-button">
            </form>";
            
            $StudentRecord = new Staff();
            if (isset($_GET['StudentSessionID'])) {
                $StudentSessionID = $_GET['StudentSessionID'];
                $StudentRecord->IndividualStudentRecord($StudentSessionID); // Display student record
            } else {
                die("<p class='error-message'>StudentSessionID not provided in the URL</p>");
            }
            ?>
        </div>
    </div>
</body>
</html>
