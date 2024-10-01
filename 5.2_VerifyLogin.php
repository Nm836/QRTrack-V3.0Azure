<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification Page</title>
    <style>
        /* Reset CSS */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7f8;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            background-color: white;
            max-width: 500px;
            padding: 30px;
            margin: 20px auto;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2, h3 {
            text-align: center;
            color: #0073e6;
        }

        p {
            text-align: center;
            margin-bottom: 20px;
            color: #666;
        }

        form {
            text-align: center;
        }

        input[type="submit"] {
            background-color: #0073e6;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #005bb5;
        }

        .error-message {
            color: red;
            margin-bottom: 20px;
        }

        .success-message {
            color: green;
            margin-bottom: 20px;
        }

        @media (max-width: 600px) {
            .container {
                padding: 20px;
            }
        }
    </style>
</head>

<body>

<div class="container">
    <h2>QR Track</h2>
    <h3>Verification Page</h3>

    <?php
    try {
        // Include the connection script
include 'ConnectionCheck.php';
		//include 'Inc_Connect.php';
        // Change database to azure database
        
        $Student_Staff_ID = stripslashes($_POST['Student_Staff_ID']);
        $passwordLogin = stripslashes($_POST['passwordLogin']);
        
        $loginCheckQuery = "SELECT * FROM Login_Record WHERE Student_StaffId='$Student_Staff_ID' AND Password='$passwordLogin'";
        $LoginCheck = $conn->query($loginCheckQuery);
        $row = $LoginCheck->fetch_assoc();
        
        if ($row != 0) {
            $userID = $row['Student_StaffId'];
            $_SESSION['userid'] = $userID;
            
            echo "<p class='success-message'>Your credentials have been verified. Please click on 'Proceed'.</p>";
            
            if ($row['Type'] === 'Student') {
                // Direct to student login page
                echo "<form action='StudentPage.php?" . SID . "' method='POST'>
                      <input type='submit' name='UserParking' value='Proceed'>
                      </form>";
            } elseif ($row['Type'] === 'Staff') {
                // Direct to staff page
                echo "<form action='6_StaffPage.php?" . SID . "' method='POST'>
                      <input type='submit' name='UserParking' value='Proceed'>
                      </form>";
            }
        } else {
            echo "<p class='error-message'>Your credentials do not match. Please go back to the login page.</p>";
            echo "<form action='index.php' method='POST'>
                  <input type='submit' name='logout' value='Go Back'>
                  </form>";
        }
        $conn->close();
    } catch (mysqli_sql_exception $e) {
        die("Error in Login: " . $e->getMessage());
    }
    ?>
</div>

</body>
</html>
