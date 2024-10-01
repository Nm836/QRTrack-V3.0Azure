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
        // Get the user ID from session
        $userID = $_SESSION['userid'];

        // Include the class for Staff operations
        include '7_StaffClass.php'; 

        // Initialize the Staff object
        $StaffView = new Staff();

        // Display the staff name header
        $StaffView->nameHeader($userID);
        ?>

        <h3>Search & Manage Attendance</h3>
        <form action="StaffPage.php" method="POST">
            <label for="keywords">Search Student by keywords:</label>
            <input type='text' name="keywords" placeholder="Enter student name or ID...">
            <input type='submit' name='keywordsearch' value='Search'>
            <br /><br />

            <h3>Enrolled Student Data</h3>
            <input type='submit' name='listAll' value='View All Students'>
            <input type='submit' name='QRCodeGenerator' value='Generate QR Code'>
        </form>

        <?php
        // Include database connection file
        include 'ConnectionCheck.php';

        // Display all students data logic
        if (isset($_POST['listAll']) || isset($_POST['back'])) {
            try {
                // Query to fetch all students
 /*               $sql = "SELECT Student_StaffId, FirstName, LastName, Attendance FROM Students";
                $stmt = $conn->query($sql);
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
*/$PercentageDisplay = $StaffView->AttendancePercentage();
$StaffView->displayAttendancePercentage($PercentageDisplay);
                // Function to display student data and attendance percentage
                //$StaffView->displayAttendancePercentage($results);

            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
        }

        // Keyword search logic
        if (isset($_POST['keywordsearch'])) {
            if (!empty($_POST['keywords'])) {
                $keywords = $_POST['keywords'];
                $keywords = stripcslashes($keywords);
                $keywords = trim($keywords);

                if ($keywords == "") {
                    echo "<p class='alert'>Please enter a valid search term.</p>";
                } else {
                    try {
                        // Use prepared statements for search functionality
                        $searchQuery = "SELECT Student_StaffId, FirstName, LastName, Attendance 
                                        FROM Students 
                                        WHERE FirstName LIKE :keywords OR LastName LIKE :keywords OR Student_StaffId LIKE :keywords";

                        $stmt = $conn->prepare($searchQuery);
                        $keywordParam = "%" . $keywords . "%";
                        $stmt->bindParam(':keywords', $keywordParam, PDO::PARAM_STR);
                        $stmt->execute();

                        $searchResults = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        // Display the search results
                        if ($searchResults) {
                            $StaffView->displayAttendancePercentage($searchResults);
                        } else {
                            echo "<p class='alert'>No results found for '$keywords'.</p>";
                        }

                    } catch (PDOException $e) {
                        echo "Error: " . $e->getMessage();
                    }
                }
            } else {
                echo "<p class='alert'>Please enter valid search keywords.</p>";
            }
        }
        ?>

        <!-- CSV Download Button -->
        <form action="download_csv.php" method="POST">
            <input type="submit" name="download_csv" value="Download Student Data as CSV" class="csv-button">
        </form>

    </main>
</body>
</html>
