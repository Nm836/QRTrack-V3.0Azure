<?php
session_start();
require("email.php");

echo "
<header>
    <form action='6_StaffPage.php' method='POST' class='back-form'>
        <input type='submit' name='back' value='Back' class='back-button'>
    </form>
    <form action='4.LoginPage.php' method='POST' class='logout-form'>
        <input type='submit' name='logout' value='Log Out' class='logout-button'>
    </form>
    <h1>Send Email - QR Track</h1>
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


.container {
    background: rgba(255, 255, 255, 0.85); /* Slight transparency */
    padding: 40px;
    max-width: 600px;
    width: 100%;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}


.select-subQR {
    padding:10px;
    
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-direction: column; /* Ensures inputs and their labels/description stack vertically */
 
}


.input-textQR{
    width: 100%;
    padding: 10px;
    
    border: 1px solid #ccc;
    border-radius: 25px;
    font-size: 14px;
    min-height: 14px;
    background-color:white; /* Light background */
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Subtle shadow */
    
    color: #333;
    outline: none;
    transition: background-color 0.3s ease;

}

.input-textQR::placeholder{
    padding-left: 20px;
}
label {
    display :inline-block;
    color: #333;
    min-width: 100px;
    padding: 10px;

}

.buttons-QR{
    padding:10px;
    display: flex;
    gap: 10px;
    justify-content: flex-end;
    cursor: pointer;
    transition: background-color 0.3s ease;}



p i {
    font-size: 14px;
    font-style: italic;
    display: block;
    margin-top: 5px;
    margin-bottom: 5px;
    color: #2a5a89;
    padding :20px;
    text-align: center;
}



.buttons-QR{ padding: 10px;
    padding-top:30px;
    background-color: #007bff;
    color: white;
    border-radius: 25px;
    border: none;
    cursor: pointer;
    transition: background-color 0.3s ease;}

.buttons-QR:hover {
        background-color: #005bb5;
    }


.logout-form {
    position: absolute;
    top: 20px;
    right: 20px;
}
.back-form {
    position: absolute;
    top: 20px;
    left: 20px;
}

.logout-button{
    padding: 8px 16px;
    background-color: #d13f4e;
    color: white;
    border-radius: 25px;
    border: none;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.back-button {
    padding: 8px 16px;
    background-color: #d13f4e;
    color: white;
    border-radius: 25px;
    border: none;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.logout-button:hover, .back-button:hover  {
    background-color: #c82333;
}


.option-selectQR{
    
    border: 1px solid #ccc;
    min-height: 12px;
    cursor: pointer;
    width: 100%;
    
    padding: 10px;
    
    border-radius: 50px; /* Fully rounded */
    background-color:white; /* Light background */
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Subtle shadow */
    font-size: 12px;
    color: #333;
    outline: none;
    transition: background-color 0.3s ease;

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



    </style>
</head>
<body>
<div class='container'>
<main>
    

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
        </div>
</body>
</html>
