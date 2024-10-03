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
        // Include the connection script to Azure database
        include 'ConnectionCheck.php';

        // Retrieve and sanitize user input
        $Student_Staff_ID = htmlspecialchars($_POST['Student_Staff_ID']);
        $passwordLogin = htmlspecialchars($_POST['passwordLogin']);
        
        $loginCheckQuery = "SELECT * FROM Login_Record WHERE Student_StaffId='$Student_Staff_ID' AND Password='$passwordLogin'";
        $LoginCheck = $conn->query($loginCheckQuery);
        $row = $LoginCheck->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
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
            echo "<form action='4.LoginPage.php' method='POST'>
                  <input type='submit' name='logout' value='Go Back'>
                  </form>";
        /*
        $Student_Staff_ID = htmlspecialchars($_POST['Student_Staff_ID']);
        $passwordLogin = htmlspecialchars($_POST['passwordLogin']);

        // Prepare the SQL query using prepared statements to prevent SQL injection
        $loginCheckQuery = "SELECT * FROM Login_Record WHERE Student_StaffId = :Student_Staff_ID AND Password = :passwordLogin";
        $stmt = $conn->prepare($loginCheckQuery);
        $stmt->bindParam(':Student_Staff_ID', $Student_Staff_ID);
        $stmt->bindParam(':passwordLogin', $passwordLogin);

        // Execute the query
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if any row is returned (i.e., valid credentials)
        if ($row) {
            $userID = $row['Student_StaffId'];
            $_SESSION['userid'] = $userID;

            // Display success message
            echo "<p class='success-message'>Your credentials have been verified. Please click on 'Proceed'.</p>";

            // Redirect based on user type
            if ($row['Type'] === 'Student') {
                // Direct to student page
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
            // Display error message for invalid credentials
            echo "<p class='error-message'>Your credentials do not match. Please go back to the login page.</p>";
            echo "<form action='index.php' method='POST'>
                  <input type='submit' name='logout' value='Go Back'>
                  </form>";
       */
       }
    } catch (PDOException $e) {
        // Display error message if there's an issue with the query or connection
        die("<p class='error-message'>Error in Login: " . $e->getMessage() . "</p>");
    }
    ?>
</div>

</body>
</html>
