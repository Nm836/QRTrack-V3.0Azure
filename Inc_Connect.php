<?php
$servername="localhost";
$username="root";
$password="";
$Databasename="qrtrack_sample";
try{
$conn= new mysqli($servername,$username,$password,$Databasename);
}
catch (mysqli_sql_exception $e) {
die("Connection Error : ".$e->getMessage());
}
?>