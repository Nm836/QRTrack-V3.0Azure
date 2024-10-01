<?php
// PHP Data Objects (PDO) Sample Code:
try {
    // Set up the PDO connection
    $conn = new PDO("sqlsrv:server=tcp:qrtrack-server.database.windows.net,1433;Database=qrtrack_sample", "Nm836", "Capstone@123");
    
    // Set PDO error mode to exception for better error handling
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Uncomment the line below if you want to see a success message
    // echo "Connection successful!";
    
} catch (PDOException $e) {
    // Handle the error by printing a message
    die("Error connecting to SQL Server: " . $e->getMessage());
}
?>
