
<?php
$servername = "localhost";
$username = "root";
$password = "";


// Create connection
try {
    $conn = new mysqli($servername, $username, $password);
		
}
catch (mysqli_sql_exception $e){
    die("Connection failed: " . $e->getCode(). ": " . $e->getMessage());
}

?>

