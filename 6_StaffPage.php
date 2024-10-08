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
    <header>
        <h1>Staff Dashboard - QR Track 
            <form action='index.php' method='POST' style="display:inline;">
                <input type='submit' name='logout' value='Log Out' class="logout-button">
            </form>
        </h1>
    </header>

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
            <input type='submit' name='keywordsearch' value='Search'>
            <br /><br />

            
            <input type='submit' name='listAll' value='View All Students'>
            

            <input type='submit' name='NewSubject' value='New Subject'>
            
        </form>

<!--QR Code File path-->
<button onclick="window.location.href='/QR_Generator_Info.html'" style="display:flex ">Generate QR Code</button>
<!--QR Code File path-->


        <!-- CSV Download Button -->
<form action="download_csv.php" method="POST" style="display:flex">
   
    <input type="submit" name="download_csv" value="Download Student Data as CSV" class="csv-button">
</form>

        <?php
        // Display all student data or search result
if (isset($_POST['listAll'])) {
        echo "Stage 1";
    
                $selectSubQuery = "SELECT * FROM Subject_Record";
                $selectSub = $this->conn->prepare($selectSubQuery);
                $selectSub->execute();
        
                $subInfo = $selectSub->fetchAll(PDO::FETCH_ASSOC);
        
                if (empty($subInfo)) {
                    echo "No subjects found in the database.";
                } else {
        /*        echo "<form action='' method='POST'>
                    <select name='SelectSubject' required>";
                    echo "Stage 2";
                foreach ($subInfo as $rows) {
                    echo "<option value='".$rows['SubCode']."'>".ucwords($rows['SubName'])."</option>"; 
                }
        
                echo "</select>
                    <input type='submit' name='ShowStudentList' value='Show'>
                    </form>";
                    */
                    echo "Stage 3";
            }
 
}
        
if (isset($_POST['ShowStudentList'])) {
    $selectedSubject = $_POST['SelectSubject'];  
    $Display_Studentx_Record=$StaffView->DisplayStudentRecordFunction();
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
                echo "<p class='alert'>Please enter valid search keywords.</p>";
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
                <input type='submit' name='addSubject' value='Add' />
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
</body>
</html>
