<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard - QR Track</title>
	
	<link rel="stylesheet" href="stylecss.css">
</head>
<body>
    <header>
        <h1>Staff Dashboard - QR Track 
            <form action='4.LoginPage.php' method='POST' style="display:inline;">
                <input type='submit' name='logout' value='Log Out' class="logout-button">
            </form>
        </h1>
    </header>

    <main>
        <?php
        $userID = $_SESSION['userid']; //User id

        include '7_StaffClass.php'; //Admin Class
        $StaffView = new Staff();
        $StaffView->nameHeader($userID);
        ?>

        <h3>Search & Manage Attendance</h3>
        <form action="6_StaffPage.php" method="POST">
            <label for="keywords">Search Student by keywords:</label>
            <input type='text' name="keywords" placeholder="Enter student name or ID...">
            <input type='submit' name='keywordsearch' value='Search'>
            <br /><br />

            <h3>Enrolled Student Data</h3>
            <input type='submit' name='listAll' value='View All Students'>
            <input type='submit' name='QRCodeGenerator' value='Generate QR Code'>
        </form>
<?php
        if (isset($_POST['QRCodeGenerator'])) {
            
			header('Location: QR Generator main file.html');
			exit;
			
        }

?>
        <?php
        // Display all student data or search result
        if (isset($_POST['listAll']) || isset($_POST['back'])) {
            $PercentageDisplay = $StaffView->AttendancePercentage();
            $StaffView->displayAttendancePercentage($PercentageDisplay);
        }

        // Keyword search logic
        if (isset($_POST['keywordsearch'])) {
            if (!empty($_POST['keywords'])) {
                $keywords = $_POST['keywords'];
                $keywords = stripcslashes($keywords);
                $keywords = trim($keywords);
                
                if ($keywords == "") {
                    $StaffView->AttendancePercentage();
                } else {
                    $StaffView->searchfunction($keywords);
                }
            } else {
                echo "<p class='alert'>Please enter valid search keywords.</p>";
            }
        }
        ?>
		
		
		<!-- CSV Download Button -->
<form action="download_csv.php" method="POST">
    <input type="submit" name="download_csv" value="Download Student Data as CSV" class="csv-button">
</form>

    </main>
</body>
</html>
