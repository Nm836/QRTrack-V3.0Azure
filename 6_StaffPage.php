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
            $Display_Student_Record=$StaffView->DisplayStudentRecordFunction();

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

        

        if (isset($_POST['NewSubject']) || isset($_POST['addSubject'])) {
            try{echo "Step 1";
            $addSubQuery = "ALTER TABLE Subject_Record MODIFY COLUMN SubCode VARCHAR(255)";
            $addSub = $this->conn->prepare($addSubQuery);
            
            // Bind the parameters
            //$addSub->bindParam(':SubCode', $NewSubCode);
            //$addSub->bindParam(':SubName', $NewSubName);
            
            // Execute the query
            $addSub->execute();
            
            // Success message
            echo "updated";
        } catch (PDOException $e) {
            die("Error adding data: " . $e->getMessage());
        }
            /*
            echo "<form action='' method='POST'>
                <input type='text' name='SubjectName' placeholder='Subject Name' required />
                <input type='text' name='SubjectCode' placeholder='Subject Code' required />
                <input type='submit' name='addSubject' value='Add' />
            </form>";

        

          // Check if the form is submitted
            if (isset($_POST['addSubject'])) {
        
        
                $NewSubName = trim(strtolower($_POST['SubjectName']));
                $NewSubCode = trim($_POST['SubjectCode']);
                
                // Call the function to add a new subject
                $StaffView->AddNewSubject($NewSubCode, $NewSubName);
            }
                */
        }
        

        ?>
		
		
		

    </main>
</body>
</html>
