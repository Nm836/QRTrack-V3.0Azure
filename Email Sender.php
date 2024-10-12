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
       @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;900&display=swap');

/* Basic Reset */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Poppins', sans-serif;
    background: url('StaffPage.jpg') no-repeat center center/cover;
    color: #333;
    line-height: 1.6;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    padding: 20px;
    }
    h1, h2 {
    text-align: center;
    color: #0073e6;
    font-weight: 900;
    margin-bottom: 20px;
}

h3 {
    text-align: center;
    margin-bottom: 15px;
    color: #005bb5;
    font-weight: 700;
}

hr {
    margin: 30px 0;
    border: none;
    border-top: 1px solid #ddd;
}

.container {
    background: rgba(255, 255, 255, 0.85); /* Slight transparency */
    padding: 40px;
    max-width: 600px;
    width: 100%;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.form-container {
    display: none;

}

.form-container.active {
    display: block;
}

.form-group {
    margin-bottom: 15px;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-direction: column; /* Ensures inputs and their labels/description stack vertically */
 
}


.form-row {
    display: flex;
    width: 100%;
    
    justify-content: space-between;
    gap: 20px; 
    align-items: flex-start;
    align-self: center;
    
    }

.form-group .form-row {

    flex-direction: row;
    align-items :start;
    justify-content: start;
    
}

.form-row  .form-group{

    flex-direction: row;
    align-items :start;
    justify-content: space-between;
    width : 100%;

    
}

.form-row  .form-group, input[type="text"]{
    width : 100%;

    
}

input[name="phone"], input[type="email"] {
        width : 50%;
        gap: 20px;
        }
        
input[name="last"], input[type="first"] {
            width : 50%;
            gap: 20px;
}
        
input[type="radio"]{
    
    flex-direction: row;
    margin-top: 10px;
    
}

input[type="text"], input[type="password"] {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 25px;
    font-size: 14px;
}

input[type="submit"], input[type="reset"] {
    background-color: #0073e6;
    color: white;
    cursor: pointer;
    font-weight: bold;
    font-size: 14px;
    padding: 10px;
    border: none;
    border-radius: 25px;
    transition: background-color 0.3s ease;
}

input[type="submit"]:hover, input[type="reset"]:hover {
    background-color: #005bb5;
}

label {
    display :inline-block;
    color: #333;
    min-width: 100px;
    padding-bottom: 10px;

}

.buttons {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
}

p, a {
    margin-top: 15px;
    text-align: center;
    color: #0073e6;
    font-size: 14px;
}

p a:hover {
    color: #005bb5;
}
p em {
    font-size: 11px;
    font-style: italic;
    display: block;
    margin-top: 5px;
    color: #5d6369;
}

/* Responsive Design */
@media (max-width: 600px) {
    .container {
        padding: 20px;
    }

    .form-row {
        flex-direction: column;
    }

    input[type="text"], input[type="password"] {
        width: 100%;
    }

    .buttons {
        justify-content: center;
        width: 100%;
    }
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
