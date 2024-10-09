<?php
session_start();

$userID = $_SESSION['userid']; //User id

        include '7_StaffClass.php'; //Admin Class
        $StaffView = new Staff();

        
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Attendance System</title>
    <link rel="stylesheet" href="QR EMAIL.css">
</head>
<body>
<div class='container'>
    <header>
        <h1>QR Track- Generate QR Code</h1>
        <form action='6_StaffPage.php?<?php echo SID;?>' method='POST' class='back-form'>
            <input type='submit' name='back' value='Back'  class='back-button'>
        </form>
        
        <form action='index.php' method='POST' class='logout-form'>
            <input type='submit' name='logout' value='Log Out' class='logout-button'>
        </form>
    </header>
    
        
        <p><i>Please fill details below to generate QR CODE</i></p>
        
        
<?php
$StaffView->selectSubjectQRCode();
?>
        

        
    </div>
</body>
</html>
