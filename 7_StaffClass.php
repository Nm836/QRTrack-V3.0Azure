<?php
class Staff {

    private $conn;
    private $UID = "";
    private $StudentListDisplay = "";
    private $keyword = "";
    private $StudentSessionID = "";

    function __construct() {
        // Include the connection script for Azure
        include 'ConnectionCheck.php'; // This file contains the Azure PDO connection
        $this->conn = $conn;
    }

    // Display the name of the staff member
    public function nameHeader($UID) {
        if ($this->UID != $UID) {
            $this->UID = $UID;
            try {
                $sql = "SELECT * FROM Login_Record WHERE Student_StaffId = :UID";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute(['UID' => $UID]);
                $userInfo = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($userInfo as $row) {
                    echo "<h2>Welcome " . ucfirst($row['FirstName']) . " " . ucfirst($row['LastName']) . "!</h2>";
                }
            } catch (PDOException $e) {
                die("Error: " . $e->getMessage());
            }
        }
    }

    // Display student attendance percentage
    public function displayAttendancePercentage($StudentAttendance) {
        try {
            echo "<table border='1' width='90%'>
                <tr><th>Student ID</th>
                <th>Name</th>
                <th>Attendance Percentage</th>
                <th>Action Taken</th>
                <th>Send E-Mail</th></tr>";
    
            while ($row = $StudentAttendance->fetch(PDO::FETCH_ASSOC)) {
                $StudentSessionID = $row['StudentID'];
                echo "<tr><td align='center'><a href='8_StudentAttendanceRecord.php?StudentSessionID={$StudentSessionID}'>{$row['StudentID']}</a></td>
                <td align='center'>{$row['FullName']}</td>
                <td align='center'>{$row['AttendancePercentage']}%</td>
                <td align='center'>Warning email sent on Date: ";
    
                // Check if attendance is below 70%
                if ($row['AttendancePercentage'] <= 70) {
                    // Check if the email has already been sent by querying the database
                    $emailCheckQuery = "SELECT LastEmailSent FROM Student_Attendance_Record WHERE StudentId = :StudentSessionID";
                    $stmt = $this->conn->prepare($emailCheckQuery);
                    $stmt->bindParam(':StudentSessionID', $StudentSessionID);
                    $stmt->execute();
                    $emailCheck = $stmt->fetch(PDO::FETCH_ASSOC);
    
                    // Check if no email was sent or if the email was sent over a certain time period ago
                    if (empty($emailCheck['LastEmailSent']) || strtotime($emailCheck['LastEmailSent']) < strtotime('-1 week')) {
                        $AutoMailQuery = "SELECT Email FROM Login_Record WHERE Student_StaffId = :StudentSessionID";
                        $stmt = $this->conn->prepare($AutoMailQuery);
                        $stmt->bindParam(':StudentSessionID', $StudentSessionID);
                        $stmt->execute();
                        $AttendanceCheck = $stmt->fetch(PDO::FETCH_ASSOC);
    
                        $Subject = "Attendance Alert";
                        $Message = "Hi {$row['FullName']}, <br/><br/>
                            Your attendance is less than the required 70%, please attend classes to meet the criteria.
                            <br/><br/>
                            Regards, <br/>
                            QR Track Management System";
    
                        // Send the email
                        sendMail($AttendanceCheck['Email'], $Subject, $Message);
    
                        // Update the database with the email sent date
                        $updateEmailDateQuery = "UPDATE Student_Attendance_Record 
                        SET LastEmailSent = GETDATE() 
                        WHERE StudentId = :StudentSessionID";
                        $stmt = $this->conn->prepare($updateEmailDateQuery);
                        $stmt->bindParam(':StudentSessionID', $StudentSessionID);
                        $stmt->execute();
    
                        echo Date("d/m/y");
                    } else {
                        // Display the last sent email date
                        echo date("d/m/y", strtotime($emailCheck['LastEmailSent']));
                    }
                }
    
                echo "</td>
                <td align='center'>
                <form method='POST' action ='Email Sender.php?".SID."'>
                <input type='submit' name='select' value='Email'>
                <input type='hidden' name='PValue' value=''>
                </form></td></tr>";
            }
            echo "</table>";
        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
        }
    }
    
    public function AttendancePercentage($StudentId = null) {
        try {
            $StudentAttendanceQuery = "SELECT 
                StudentId AS StudentID, 
                Name as FullName, 
                ROUND((SUM(CASE WHEN AttendanceNum = 'Present' THEN 1 ELSE 0 END) / 5) * 100, 0) AS AttendancePercentage 
                FROM Student_Attendance_Record ";
    
            if ($StudentId !== null) {
                $StudentAttendanceQuery .= " WHERE 
                    StudentId = :StudentId";
            }
    
            $StudentAttendanceQuery .= " GROUP BY StudentId, Name";
    
            $stmt = $this->conn->prepare($StudentAttendanceQuery);
            if ($StudentId !== null) {
                $stmt->bindParam(':StudentId', $StudentId);
            }
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
        }
    }

    // Search student based on keyword

    public function searchFunction($keyword) {
        if ($this->keyword != $keyword) {
            $this->keyword = $keyword;
            try {
                // Using prepared statements to avoid SQL injection
                $SearchQuery = "SELECT DISTINCT StudentId, Name 
                                FROM Student_Attendance_Record 
                                WHERE StudentId LIKE '%{$keyword}%' OR Name LIKE '%{$keyword}%'";
                
                $stmt = $this->conn->query($SearchQuery);
       
    echo $stmt;
               
    
                echo "Search Stage 1 Check";
    
                // Fetching all rows
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo"Stage 2 v4";
                // Check if rows are retrieved
                if (!empty($rows)) { 
                    echo "<table border='1' width='90%'>
                        <tr><th>Student ID</th>
                        <th>Name</th>
                        <th>Attendance Percentage</th>
                        <th>Action Taken</th>
                        <th>Send E-Mail</th></tr>";
    
                    // Looping through the results
                    foreach ($rows as $row) {
                        echo "Search Stage 2 Check";
    
                        $StudentId = $row['StudentId'];
                        $Percentage = $this->AttendancePercentage($StudentId);
                        $this->displayAttendancePercentageSearch($Percentage);

                        echo"Stage 3 v4";
                    }
    
                    echo "</table>";
                } else {
                    echo "No Match Found";
                }
            } catch (PDOException $e) {
                die("Error: " . $e->getMessage());
            }
        }
    }
    
    


    
}
?>
