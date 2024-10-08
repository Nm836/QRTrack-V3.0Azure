<?php
// Include the connection script
include 'ConnectionCheck.php';
//include 'Inc_Connect.php';// change database connection to the above created in Azure

try {
    // Ensure $conn is a valid PDO object
    
        $loginRecordQuery = "CREATE TABLE Login_Record (
            Student_StaffId INT PRIMARY KEY,
            FirstName VARCHAR(255) NOT NULL,
            LastName VARCHAR(255) NOT NULL,
            Phone BIGINT NOT NULL,
            Email VARCHAR(255) NOT NULL,
            Type ENUM('Student', 'Staff') NOT NULL,
            Password VARCHAR(255) NOT NULL
        )";

        // Execute the query using the PDO connection
        $conn->query($loginRecordQuery);
        echo "Table 'LoginRecord' created successfully<br/>";

		
		$StudentAttendanceQuery= "CREATE TABLE Student_Attendance_Record (
			StudentId INT not null,
			Name VARCHAR(255) NOT NULL,
            SubCode VARCHAR(255) NOT NULL,
			LectureWeek INT NOT NULL,
			AttendanceNum ENUM('Present','New'),
			LastEmailSent DATETIME null,
    FOREIGN KEY (StudentId) REFERENCES Login_Record(Student_StaffId)
			)";
		$conn->query($StudentAttendanceQuery);
        echo "Table 'Student Attendance Record' created successfully<br/>";


        
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
