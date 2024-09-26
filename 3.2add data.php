<?php
// Include the connection script
include 'ConnectionCheck.php';
//include 'Inc_Connect.php';// change database connection to the above created in Azure
try {
//sample data of student and Staff

$addDataQuery= "INSERT INTO Login_Record (Student_StaffId, FirstName, LastName, Phone, Email, Type, Password)
VALUES ('123456', 'John', 'Doe', '1234567890', 'qrtrack001@yahoo.com', 'Student', '".md5('password123')."'),
    ('234567', 'Jane', 'Smith', '2345678901', 'janesmith@example.com', 'Student', '".md5('password123')."'),
    ('345678', 'Robert', 'Johnson', '3456789012', 'robertj@example.com', 'Student', '".md5('password123')."'),
    ('456789', 'Emily', 'Brown', '4567890123', 'emilyb@example.com', 'Staff', '".md5('password123')."'),
    ('567890', 'Michael', 'Davis', '5678901234', 'michael.d@example.com', 'Staff', '".md5('password123')."')";
	
	if ($conn->query($addDataQuery)==true){
		echo "Data added in LogIn Record table";
	} else echo "Error adding data in LogIn Record table";

//sample data for attendance 

$userdataQuery=" INSERT INTO Student_Attendance_Record (StudentId,Name,SubCode, LectureWeek, AttendanceNum) 
VALUES (123456,'John Doe', 5001, 1,'Present'),
    (123456,'John Doe', 5001, 2,'Present'),
    (123456,'John Doe', 5001, 3,'Present'),
	(345678, 'Robert Johnson',5001, 'Null','New')
    ";

if ($conn->query($userdataQuery)==True){
	echo "<br />Data added to Student Attendance Record";
	
} else echo "Error adding data in Student Attendance Record table";
	
}
catch (mysqli_sql_exception $e){
die("error : ".$e->getMessage());}




?>