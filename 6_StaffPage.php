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
      
<div class='container'>
    <header>
        <h1>Staff Dashboard - QR Track </h1>
        
        <form action='index.php' method='POST' class="logout-form">
                <input type='submit' name='logout' value='Log Out' class='logout-button'>
            </form>

    </header>
    <main>
        <?php
        $userID = $_SESSION['userid']; //User id

        include '7_StaffClass.php'; //Admin Class
        $StaffView = new Staff();



        $StaffView->nameHeader($userID);
        ?>

        <h3>Search & Manage Attendance</h3>

        <form action="6_StaffPage.php" method="POST" class="search-form">
            <label for="keywords">Search Student :</label>
            <div class="search-bar">
                <input type='text' name="keywords" placeholder="Enter student name or ID..." class="search-input">
                <input type='submit' name='keywordsearch' value='Search' class="search-button">
            </div>
            </form>
    <div class="buttons-container">
        <form action="6_StaffPage.php" method="POST">
        
            <input type='submit' name='listAll' value='Student Record' class="buttons">
            <input type='submit' name='NewSubject' value='Add Subject' class="buttons">
           
        </form>


        <button onclick="window.location.href='/All record.php'" style="display:flex ">All Record</button>
<!--QR Code File path
<button onclick="window.location.href='/QR_Generator_Info.php'" style="display:flex ">Generate QR Code</button>
-->
<form action="QR_Generator_Info.php" method="POST" style="display:flex; margin-right:auto;">
   
    <input type="submit" name="QRGenerator" class="buttons" value="Generate QR Code">
</form>
</div>

        <?php
        // Display all student data or search result
if (isset($_POST['listAll'])) {
    $StaffView->selectSubject();
    
    }
        
if (isset($_POST['ShowStudentList'])) {
    $selectedSubject = $_POST['SelectSubject'];  
    $selectedSubject = trim($selectedSubject);
    $CurrentWeek = $_POST['CurrentWeek'];  
    $CurrentWeek=trim($CurrentWeek);
    
    $Display_Student_Record = $StaffView->DisplayStudentRecordFunction($selectedSubject, $CurrentWeek);
    
}

        // Keyword search logic
        if (isset($_POST['keywordsearch'])) {
            if (!empty($_POST['keywords'])) {
                $keywords = $_POST['keywords'];
                $keywords = stripcslashes($keywords);
                $keywords = trim($keywords);
                
                if ($keywords == "") {
                    $StaffView->DisplayStudentRecordFunction();
                } else {
                    $StaffView->searchFunction($keywords);
                }
            } else {
                echo "<p>Please enter valid search keywords.</p>";
            }
        }
        if (isset($_POST['back'])){
            header("Location : StaffPage.php");
            exit();
        }

        

        if (isset($_POST['NewSubject'])) {
            
            echo "<form action='' method='POST'>
                <div class='select-sub'>
                <input type='text' name='SubjectCode' placeholder='Subject Code' class='input-subcode' required />
                <input type='text' name='SubjectName' placeholder='Subject Name' class='input-subname' required />
                <input type='submit' name='addSubject' value='Add' class='add-button' />
                </div>
            </form>";

                
        }
        if (isset($_POST['addSubject'])) {
        
        
            $NewSubName = trim(strtolower($_POST['SubjectName']));
            $NewSubCode = trim($_POST['SubjectCode']);
            
            // Call the function to add a new subject
            $StaffView->AddNewSubject($NewSubCode, $NewSubName);
        }
        

        ?>
		
		
		

    </main>
    </div>
</body>
</html>
