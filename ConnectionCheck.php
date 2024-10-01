<?php
// PHP Data Objects(PDO) Sample Code:
try {
    $conn = new PDO("sqlsrv:server = tcp:qrtrack-server.database.windows.net,1433; Database = qrtrack_sample", "Nm836", "Capstone@123");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connectiocessful";
}
catch (PDOException $e) {
    print("Error connecting to SQL Server.");
    die(print_r($e));
}

// SQL Server Extension Sample Code:
$connectionInfo = array("UID" => "Nm836", "pwd" => "Capstone@123", "Database" => "qrtrack_sample", "LoginTimeout" => 30, "Encrypt" => 1, "TrustServerCertificate" => 0);
$serverName = "tcp:qrtrack-server.database.windows.net,1433";
$conn = sqlsrv_connect($serverName, $connectionInfo);
?>
