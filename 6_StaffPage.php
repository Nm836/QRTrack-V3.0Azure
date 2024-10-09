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
        <h1>Staff Dashboard - QR Track 
            
        </h1>
    </header>
    <form action='index.php' method='POST' class = 'logout-button'>
                <input type='submit' name='logout' value='Log Out' class="buttons">
            </form>
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
            <input type='submit' name='keywordsearch' value='Search' class="buttons">
            <br /><br />

            
            <input type='submit' name='listAll' value='Student Record' class="buttons">
            

            <input type='submit' name='NewSubject' value='Add Subject' class="buttons">
            
        </form>



<!--QR Code File path
<button onclick="window.location.href='/QR_Generator_Info.php'" style="display:flex ">Generate QR Code</button>
-->
<form action="QR_Generator_Info.php" method="POST" style="display:flex">
   
    <input type="submit" name="QRGenerator" class="buttons" value="Generate QR Code">
</form>


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
                <input type='text' name='SubjectCode' placeholder='Subject Code' required />
                <input type='text' name='SubjectName' placeholder='Subject Name' required />
                <input type='submit' name='addSubject' value='Add' class='buttons' />
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
