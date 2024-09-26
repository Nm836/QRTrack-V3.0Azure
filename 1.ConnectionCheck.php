<?php
// PHP Data Objects(PDO) Sample Code:
try {
    $conn = new PDO("sqlsrv:server = tcp:qrtrack.database.windows.net,1433; Database = qrtrack_sample", "qrtrack-server", "Capstone@123");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch (PDOException $e) {
    print("Error connecting to SQL Server.");
    die(print_r($e));
}

// SQL Server Extension Sample Code:
//$connectionInfo = array("UID" => "qrtrack_server", "pwd" => "{your_password_here}", "Database" => "QRtrack", "LoginTimeout" => 30, "Encrypt" => 1, "TrustServerCertificate" => 0);
//$serverName = "tcp:qrtrack.database.windows.net,1433";
//$conn = sqlsrv_connect($serverName, $connectionInfo);
?>
