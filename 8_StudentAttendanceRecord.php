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
    
	<link rel="stylesheet" href="stylecss.css">

    <!-- Link to the CSS file StudentRecordstylecss.css-->
</head>
<body>
    <div class='container'>
        <header>
        <h1>Student Attendance Display</h1>
        <form action='6_StaffPage.php?<?php echo SID;?>' method='POST' class='back-form'>
                <input type='submit' name='back' value='Back' class='back-button'>
            </form>
            <form action='index.php' method='POST' class='logout-form'>
                <input type='submit' name='logout' value='Log Out' class='logout-button'>
            </form>
            
        </header>
        
            <?php
            
            
            $StudentRecord = new Staff();
            if (isset($_GET['StudentSessionID']) && isset($_GET['SubCode'])) {
                $StudentSessionID = htmlspecialchars($_GET['StudentSessionID']);
                $selectedSubject = htmlspecialchars($_GET['SubCode']);
                $StudentRecord->IndividualStudentRecord($StudentSessionID, $selectedSubject); // Display student record
            } else {
                die("<p class='error-message'>StudentSessionID not provided in the URL</p>");
            }
            ?>
        
    </div>
</body>
</html>
