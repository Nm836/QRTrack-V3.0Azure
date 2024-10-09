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
    
    // Query to fetch all records from Student_record table
    $query = "SELECT * FROM Login_Record";
    
    // Prepare and execute the query
    $stmt = $conn->query($query);
    
    // Fetch the data
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Display the data in a table format
    if ($results) {
        echo "<table border='1'>";
        echo "<tr>";
        // Display the table headers based on column names
        foreach ($results[0] as $key => $value) {
            echo "<th>" . htmlspecialchars($key) . "</th>";
        }
        echo "</tr>";

        // Display the table data
        foreach ($results as $row) {
            echo "<tr>";
            foreach ($row as $column) {
                echo "<td>" . htmlspecialchars($column) . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "No data found.";
    }
    
} catch (PDOException $e) {
    // Error handling
    echo "Error connecting to SQL Server: " . $e->getMessage();
}
<?php
try {
    // Assuming $conn is your PDO connection
    $query = "SELECT * FROM Student_Attendance_Record";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    
    // Fetch all results
    $attendanceRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Loop through the records and display them (for testing purposes)
    foreach ($attendanceRecords as $record) {
        echo "Student ID: " . $record['StudentId'] . "<br>";
        echo "Name: " . $record['Name'] . "<br>";
        echo "Subject Code: " . $record['SubCode'] . "<br>";
        echo "Lecture Week: " . $record['LectureWeek'] . "<br>";
        echo "Attendance Number: " . $record['AttendanceNum'] . "<br>";
        echo "Last Email Sent: " . $record['LastEmailSent'] . "<br><br>";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>


?>
