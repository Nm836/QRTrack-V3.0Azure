<?php
// Include the connection script
include 'ConnectionCheck.php';
//include 'Inc_Connect.php';// change database connection to the above created in Azure

try {
    // Ensure $conn is a valid PDO object
    
        $loginRecordQuery = "CREATE TABLE Subject_Record (
            SubCode INT PRIMARY KEY,
            SubName VARCHAR(255) NOT NULL   
        )";

        // Execute the query using the PDO connection
        $conn->query($loginRecordQuery);
        echo "Table 'Subject_Record' created successfully<br/>";

		
} catch (Exception $e) {
    // Handle any errors
    die("Error: " . $e->getMessage());
}
?>
