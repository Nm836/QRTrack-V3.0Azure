<?php
session_start();
require("email.php");

echo "
<header>
    <form action='6_StaffPage.php' method='POST'>
        <input type='submit' name='back' value='Back' class='header-btn'>
    </form>
    <form action='4.LoginPage.php' method='POST'>
        <input type='submit' name='logout' value='Log Out' class='header-btn'>
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
    <style>
        /* Global Styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #343a40;
            padding: 10px;
            text-align: right;
        }

        header form {
            display: inline;
        }

        .header-btn {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 5px;
            font-size: 14px;
            margin: 0 5px;
        }

        .header-btn:hover {
            background-color: #0056b3;
        }

        main {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #343a40;
        }

        form {
            margin-top: 20px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 8px;
        }

        input[type="email"],
        input[type="text"],
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 16px;
        }

        textarea {
            height: 150px;
        }

        button[type="submit"] {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            display: inline-block;
        }

        button[type="submit"]:hover {
            background-color: #218838;
        }

        .response-message {
            font-size: 16px;
            text-align: center;
            color: #dc3545;
        }

        .success-message {
            color: #28a745;
        }
    </style>
</head>
<body>

<main>
    <h1>Send Email - QR Track</h1>

    <?php
    $userID = $_SESSION['userid']; // User ID
    include '7_StaffClass.php'; // Admin Class
    $StaffView = new Staff();
    $StaffView->nameHeader($userID);
    ?>

    <form action="" method="POST" enctype="multipart/form-data">
        <label for="email">Student's Email-ID:</label>
        <input type="email" name="email" placeholder="Enter student's email" value="">

        <label for="subject">Subject:</label>
        <input type="text" name="subject" placeholder="Enter subject" value="">

        <label for="message">Message:</label>
        <textarea name="message" placeholder="Enter your message here"></textarea>

        <button type="submit" name="submit">Submit</button>

        <?php if (isset($response)): ?>
            <p class="response-message <?php echo $response == 'success' ? 'success-message' : ''; ?>">
                <?php echo $response == 'success' ? 'Email was sent successfully' : $response; ?>
            </p>
        <?php endif; ?>
    </form>
</main>

</body>
</html>
