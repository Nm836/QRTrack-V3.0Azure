<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LogIn/Registration</title>
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
            padding: 20px;
        }

        h1, h2 {
            text-align: center;
            color: #0073e6;
        }

        h3 {
            margin-bottom: 15px;
            color: #005bb5;
        }

        .container {
            background-color: white;
            max-width: 600px;
            padding: 30px;
            margin: 20px auto;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        p {
            margin-bottom: 10px;
            color: #666;
        }

        input[type="text"],
        input[type="password"],
        input[type="submit"],
        input[type="reset"],
        input[type="radio"] {
            display: block;
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 16px;
        }

        input[type="radio"] {
            width: auto;
            margin-right: 10px;
        }

        input[type="submit"],
        input[type="reset"] {
            background-color: #0073e6;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
            font-weight: bold;
        }

        input[type="submit"]:hover,
        input[type="reset"]:hover {
            background-color: #005bb5;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            margin-bottom: 5px;
            font-weight: bold;
            display: block;
        }

        .error-messages {
            color: red;
            margin-bottom: 20px;
        }

        hr {
            margin: 30px 0;
            border: none;
            border-top: 1px solid #ddd;
        }

        .message {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
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
        <h1>QR Track</h1>
        <h2>New User Registration</h2>

        <?php
        session_start();

        $message = "";
        $errorcount = 0;
        $email = "";

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (empty($_POST['first']) || empty($_POST['last'])) {
                $message .= "<p>Enter First and Last name</p>\n"; 
                ++$errorcount;
            } else {
                $fname = stripslashes(trim(strtolower($_POST['first'])));
                $lname = stripslashes(trim(strtolower($_POST['last'])));
            }

            if (empty($_POST['id'])) {
                ++$errorcount;
                $message .= "<p>Enter Student/Staff ID</p>"; 
            } else {
                $id = stripslashes(trim($_POST['id']));
                if (preg_match("/^([0-9]{6})$/", $id) == 0) {
                    ++$errorcount;
                    $message .= "<p>Enter valid Student/Staff ID</p>"; 
                }
                try {
                    // Include the connection script
                    include 'ConnectionCheck.php';

                    // Prepared statement to check if the ID already exists
                    $idCheckQuery = "SELECT COUNT(*) FROM Login_Record WHERE Student_StaffId = :id";
                    $stmt = $conn->prepare($idCheckQuery);
                    $stmt->bindParam(':id', $id);
                    $stmt->execute();
                    $row = $stmt->fetchColumn();

                    if ($row > 0) {
                        ++$errorcount;
                        $message .= "<p>Student/Staff ID already exists</p>";
                    }
                } catch (PDOException $e) {
                    die("Error: " . $e->getMessage());
                    ++$errorcount;
                }
            }

            if (empty($_POST['email'])) {
                ++$errorcount;
                $message .= "<p>Enter email address</p>"; 
            } else {
                $email = stripslashes(trim(strtolower($_POST['email'])));
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    ++$errorcount;
                    $message .= "<p>Enter valid email id</p>";
                } else {
                    try {
                        // Check if email already exists
                        $emailCheckQuery = "SELECT COUNT(*) FROM Login_Record WHERE Email = :email";
                        $stmt = $conn->prepare($emailCheckQuery);
                        $stmt->bindParam(':email', $email);
                        $stmt->execute();
                        $row = $stmt->fetchColumn();

                        if ($row > 0) {
                            ++$errorcount;
                            $message .= "<p>Email already exists</p>"; 
                        }
                    } catch (PDOException $e) {
                        die("Error: " . $e->getMessage());
                        ++$errorcount;
                    }
                }
            }

            if ($errorcount > 0) {
                echo "<div class='error-messages'><h3>Check the details to be corrected:</h3>\n" . $message . "</div>";
            } else {
                try {
                    $type = stripslashes($_POST['type']);
                    $phone = stripslashes(trim($_POST['phone']));
                    $password1 = stripslashes(trim($_POST['password1']));
                    
                    // Prepared statement to insert data
                    $addDataQuery = "INSERT INTO Login_Record (Student_StaffId, FirstName, LastName, Phone, Email, Type, Password) 
                                    VALUES (:id, :fname, :lname, :phone, :email, :type, :password)";
                    $stmt = $conn->prepare($addDataQuery);
                    $stmt->bindParam(':id', $id);
                    $stmt->bindParam(':fname', $fname);
                    $stmt->bindParam(':lname', $lname);
                    $stmt->bindParam(':phone', $phone);
                    $stmt->bindParam(':email', $email);
                    $stmt->bindParam(':type', $type);
                    $stmt->bindParam(':password', $password1); // Hash password using md5

                    $stmt->execute();

                    $_SESSION['userid'] = $id;
                    echo "<div class='message'><h3>" . ucfirst($fname) . " " . ucfirst($lname) . ", Your data has been saved.<br/>Your Student/Staff id is " . $_SESSION['userid'] . "</h3></div>";
                } catch (PDOException $e) {
                    die("Error adding data: " . $e->getMessage());
                }
            }
        }
        ?>

        <form action="index.php" method="POST">
            <input type="submit" name="logout" value="Go Back To Login Page">
        </form>
    </div>

</body>
</html>
