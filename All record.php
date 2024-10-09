<?php
// Connection settings
$serverName = "tcp:qrtrack-server.database.windows.net,1433"; // Your server name
$database = "qrtrack_sample"; // Your database name
$username = "Nm836"; // Your database username
$password = "Capstone@123"; // Your database password

try {
    // Establishing connection to Azure SQL Database
    $conn = new PDO("sqlsrv:server=$serverName;Database=$database", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // --- First Query: Fetch all records from Login_Record ---
    $loginQuery = "SELECT * FROM Login_Record";
    
    // Prepare and execute the query
    $stmtLogin = $conn->query($loginQuery);
    
    // Fetch the data from Login_Record
    $loginResults = $stmtLogin->fetchAll(PDO::FETCH_ASSOC);
    
    // Display the Login_Record data in a table format
    if ($loginResults) {
        echo "<h2>Login_Record Data:</h2>";
        echo "<table border='1'>";
        echo "<tr>";
        // Display the table headers based on column names
        foreach ($loginResults[0] as $key => $value) {
            echo "<th>" . htmlspecialchars($key) . "</th>";
        }
        echo "</tr>";

        // Display the table data
        foreach ($loginResults as $row) {
            echo "<tr>";
            foreach ($row as $column) {
                echo "<td>" . htmlspecialchars($column) . "</td>";
            }
            echo "</tr>";
        }
        echo "</table><br>";
    } else {
        echo "No data found in Login_Record.";
    }

    // --- Second Query: Fetch all records from Student_Attendance_Record ---
    $attendanceQuery = "SELECT * FROM Student_Attendance_Record";
    
    // Prepare and execute the query
    $stmtAttendance = $conn->prepare($attendanceQuery);
    $stmtAttendance->execute();
    
    // Fetch all results from Student_Attendance_Record
    $attendanceRecords = $stmtAttendance->fetchAll(PDO::FETCH_ASSOC);

    // Display the Student_Attendance_Record data
    if ($attendanceRecords) {
        echo "<h2>Student_Attendance_Record Data:</h2>";
        foreach ($attendanceRecords as $record) {
            echo "Student ID: " . htmlspecialchars($record['StudentId']) . "<br>";
            echo "Name: " . htmlspecialchars($record['Name']) . "<br>";
            echo "Subject Code: " . htmlspecialchars($record['SubCode']) . "<br>";
            echo "Lecture Week: " . htmlspecialchars($record['LectureWeek']) . "<br>";
            echo "Attendance Number: " . htmlspecialchars($record['AttendanceNum']) . "<br>";
            echo "Last Email Sent: " . htmlspecialchars($record['LastEmailSent']) . "<br><br>";
        }
    } else {
        echo "No data found in Student_Attendance_Record.";
    }

} catch (PDOException $e) {
    // Error handling
    echo "Error connecting to SQL Server: " . $e->getMessage();
}
?>
