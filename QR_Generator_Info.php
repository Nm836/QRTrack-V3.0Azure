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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        h1 {
            color: #2c3e50;
            text-align: center;
        }
        .container {
            max-width: 600px;
            margin: auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .button {
            background-color: #3498db;
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            margin: 10px 0;
        }
        .button:hover {
            background-color: #2980b9;
        }
        form {
            margin-top: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome to the QR Attendance System</h1>
        
        <form action='6_StaffPage.php?<?php echo SID;?>' method='POST' style = "display:flex;">
            <input type='submit' name='back' value='Back'  class="button">
        </form>
        
        <form action='index.php' method='POST' style='display:flex;'>
            <input type='submit' name='logout' value='Log Out'  class="button">
        </form>
        

        <form action ="qr_generated.php" method="POST">
            <label for="subject_code">Subject Code:</label>
            <input type="text" id="subject_code" name="subject_code" required>

            <label for="week">Week:</label>
            <input type="text" id="week" name="week" required>
			
			<label for="validity">Validity in Minutes:</label>
            <input type="text" id="validity" name="validity" required>
			
			<label for="latitude">Latitude:</label>
			<input type="number" step="any" name="latitude" id="latitude" placeholder="Enter Latitude" required><br><br>

			<label for="longitude">Longitude:</label>
			<input type="number" step="any" name="longitude" id="longitude" placeholder="Enter Longitude" required><br><br>

       

            <button type="submit">Generate QR Code</button>
        </form>

        
    </div>
</body>
</html>
