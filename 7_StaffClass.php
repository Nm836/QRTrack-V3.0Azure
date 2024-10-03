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
        echo "<table border='1' width='90%'>
            <tr><th>Student ID</th>
            <th>Name</th>
            <th>Attendance Percentage</th>
            <th>Action Taken</th>
            <th>Send E-Mail</th></tr>";

        foreach ($StudentAttendance as $row) {
            $StudentSessionID = $row['StudentID'];
            echo "<tr><td align='center'><a href='8_StudentAttendanceRecord.php?StudentSessionID={$StudentSessionID}'>{$row['StudentID']}</a></td>
            <td align='center'>{$row['FullName']}</td>
            <td align='center'>{$row['AttendancePercentage']}%</td>
            <td align='center'>Warning email sent on Date: ";
            
            if ($row['AttendancePercentage'] <= 70) {
                // Check if an email was already sent
                $emailCheckQuery = "SELECT LastEmailSent FROM Student_Attendance_Record WHERE StudentId = :StudentSessionID";
                $stmt = $this->conn->prepare($emailCheckQuery);
                $stmt->execute(['StudentSessionID' => $StudentSessionID]);
                $emailCheck = $stmt->fetch(PDO::FETCH_ASSOC);

                if (empty($emailCheck['LastEmailSent']) || strtotime($emailCheck['LastEmailSent']) < strtotime('-1 week')) {
                    // Fetch student email
                    $AutoMailQuery = "SELECT Email FROM Login_Record WHERE Student_StaffId = :StudentSessionID";
                    $stmt = $this->conn->prepare($AutoMailQuery);
                    $stmt->execute(['StudentSessionID' => $StudentSessionID]);
                    $AttendanceCheck = $stmt->fetch(PDO::FETCH_ASSOC);

                    $Subject = "Attendance Alert";
                    $Message = "Hi {$row['FullName']}, <br/><br/>
                        Your attendance is less than the required 70%, please attend classes to meet the criteria.
                        <br/><br/>
                        Regards, <br/>
                        QR Track Management System";

                    // Send the email (you'll need a function for this)
                    sendMail($AttendanceCheck['Email'], $Subject, $Message);

                    // Update the email sent date
                    $updateEmailDateQuery = "UPDATE Student_Attendance_Record SET LastEmailSent = NOW() WHERE StudentId = :StudentSessionID";
                    $stmt = $this->conn->prepare($updateEmailDateQuery);
                    $stmt->execute(['StudentSessionID' => $StudentSessionID]);

                    echo date("d/m/y");
                } else {
                    echo date("d/m/y", strtotime($emailCheck['LastEmailSent']));
                }
            }

            echo "</td>
                <td align='center'>
                <form method='POST' action='EmailSender.php'>
                <input type='submit' name='select' value='Email'>
                <input type='hidden' name='PValue' value=''>
                </form></td></tr>";
        }

        echo "</table>";
    }

    // Fetch attendance percentage
    public function AttendancePercentage($StudentId = null) {
        try {   /*
$StudentAttendanceQuery = "SELECT 
            StudentId AS StudentID, 
            Name as FullName, 
            ROUND((SUM(CASE WHEN AttendanceNum = 'Present' THEN 1 ELSE 0 END) / 5) * 100, 0) AS AttendancePercentage 
            FROM Student_Attendance_Record 
            GROUP BY StudentId, Name";
        $stmt = $this->conn->prepare($StudentAttendanceQuery);

          if ($StudentId !== null) {
                $StudentAttendanceQuery .= " WHERE StudentId = :StudentId";
            }

            $StudentAttendanceQuery .= " GROUP BY StudentId, Name";
            $stmt = $this->conn->prepare($StudentAttendanceQuery);

            if ($StudentId !== null) {
                $stmt->execute(['StudentId' => $StudentId]);
                
            } else {
                $stmt->execute();
            }
*/

            $StudentAttendanceQuery ="SELECT 
            StudentId AS StudentID, 
            Name as FullName, 
            ROUND((SUM(CASE WHEN AttendanceNum = 'Present' THEN 1 ELSE 0 END) / 5) * 100, 0) AS AttendancePercentage 
            FROM Student_Attendance_Record ";

                if ($StudentId!==Null){
                    
                $StudentAttendanceQuery .= " WHERE 
                    StudentId = {$StudentId}";
                } 
                
                $StudentAttendanceQuery .= " GROUP BY StudentId, Name";
$StudentAttendance = $this->conn->query($StudentAttendanceQuery);
return $StudentAttendance->fetchAll(PDO::FETCH_ASSOC);;



            //return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
        }
    }

    // Search student based on keyword
    public function searchFunction($keyword) {
        if ($this->keyword != $keyword) {
            $this->keyword = $keyword;
            try {
                // Correct SQL query with placeholders for binding
   /*             $SearchQuery = "SELECT DISTINCT StudentId 
                                FROM Student_Attendance_Record 
                                WHERE StudentId LIKE :keyword OR Name LIKE :keyword";
                $stmt = $this->conn->prepare($SearchQuery);
                
                // Bind parameter correctly
                $stmt->execute(['keyword' => "%{$keyword}%"]);
                
                $SearchResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
    */
                $SearchQuery = "SELECT Distinct StudentId FROM Student_Attendance_Record WHERE StudentId LIKE '%".$keyword."%' OR Name LIKE '%".$keyword."%'";
                $SearchResult = $this->conn->query($SearchQuery);
echo "Seacrh Stage 1 Check";

                if (!empty($SearchResult)) {
                    echo "<table border='1' width='90%'>
                    <tr><th>Student ID</th>
                    <th>Name</th>
                    <th>Attendance Percentage</th>
                    <th>Action Taken</th>
                    <th>Send E-Mail</th></tr>";
                    
                    foreach ($SearchResult as $row) {
                        $StudentId = $row['StudentId'];
                        $Percentage = $this->AttendancePercentage($StudentId);
                        $this->displayAttendancePercentageSearch($Percentage);
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
