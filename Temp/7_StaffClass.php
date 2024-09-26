<?php
require("email.php");
class Staff {
	
    private $conn;
	private $UID="";
	private $StudentListDisplay="";
	private $keyword="";
	private $StudentSessionID = "";
	function __construct() {
        include 'Inc_Connect.php';//Change to azure
        $this->conn = $conn;
   
    }

    public function nameHeader($UID) {
		if ($this->UID != $UID){
			$this->UID = $UID;
    try {
			
            $sql = "SELECT * FROM Login_Record WHERE Student_StaffId='{$UID}'";
            $userInfo = $this->conn->query($sql);
            while ($row = $userInfo->fetch_assoc()) {
                echo "<h2>Welcome " . ucfirst($row['FirstName']) . " " . ucfirst($row['LastName']) . " !</h2>";
            }
        } catch (mysqli_sql_exception $e) {
            die("Error : " . $e->getMessage());
        }
    }
	}
	
	
	
	
	
/*public function displayAttendancePercentage($StudentAttendance){
			echo "<table border='1' width='90%'>
        <tr><th>Student ID</th>
        <th>Name</th>
        <th>Attendance Percentage</th>
        <th>Action Taken</th>
        <th>Send E-Mail</th>
        </tr>";
        
        while ($row = $StudentAttendance->fetch_assoc()) {
			
			$StudentSessionID = $row['StudentID'];
			echo "<tr><td align='center'><a href='8_StudentAttendanceRecord.php?StudentSessionID={$StudentSessionID}'>{$row['StudentID']}</a></td>
		
            <td align='center'>{$row['FullName']}</td>
			
            <td align='center'>{$row['AttendancePercentage']}%</td>
			
            <td align='center'>Warning email sent on Date :";
			
			if($row['AttendancePercentage']<=70){
				
				
				
				$AutoMailQuery= "Select Email from Login_Record where Student_StaffId = {$StudentSessionID}";
				$AutoMail=$this->conn->query($AutoMailQuery);
				
				$AttendanceCheck = $AutoMail->fetch_assoc();
				$Subject="Attendance Alert";
				$Message= "Hi {$row['FullName']}, <br/><br/>
				Your Attendance is less than required 70%, please attend classes to meet the criteria.
				<br/><br/>
				Regards <br/>
				QR Track Management System";
				
				sendMail($AttendanceCheck['Email'], $Subject,$Message);
				
				
				
				echo Date("d/m/y");
				
			}
			
			
			
			echo "</td>
            
            <td align='center'>
			
			
			<form method='POST' action ='Email Sender.php?".SID."'>
            <input type='submit' name='select' value='Email'>
            <input type='hidden' name='PValue' value=''> <!--Define USer ID-->
            </form></td></tr>";
        }
        echo "</table>";
		
	}
  */
  
  public function displayAttendancePercentage($StudentAttendance) {
    echo "<table border='1' width='90%'>
    <tr><th>Student ID</th>
    <th>Name</th>
    <th>Attendance Percentage</th>
    <th>Action Taken</th>
    <th>Send E-Mail</th>
    </tr>";

    while ($row = $StudentAttendance->fetch_assoc()) {
        $StudentSessionID = $row['StudentID'];
        echo "<tr><td align='center'><a href='8_StudentAttendanceRecord.php?StudentSessionID={$StudentSessionID}'>{$row['StudentID']}</a></td>
        <td align='center'>{$row['FullName']}</td>
        <td align='center'>{$row['AttendancePercentage']}%</td>
        <td align='center'>Warning email sent on Date: ";

        // Check if attendance is below 70%
        if ($row['AttendancePercentage'] <= 70) {
            // Check if the email has already been sent by querying the database
            $emailCheckQuery = "SELECT LastEmailSent FROM Student_Attendance_Record WHERE StudentId = {$StudentSessionID}";
            $emailResult = $this->conn->query($emailCheckQuery);
            $emailCheck = $emailResult->fetch_assoc();

            // Check if no email was sent or if the email was sent over a certain time period ago
            if (empty($emailCheck['LastEmailSent']) || strtotime($emailCheck['LastEmailSent']) < strtotime('-1 week')) {
                $AutoMailQuery = "SELECT Email FROM Login_Record WHERE Student_StaffId = {$StudentSessionID}";
                $AutoMail = $this->conn->query($AutoMailQuery);
                $AttendanceCheck = $AutoMail->fetch_assoc();

                $Subject = "Attendance Alert";
                $Message = "Hi {$row['FullName']}, <br/><br/>
                    Your attendance is less than the required 70%, please attend classes to meet the criteria.
                    <br/><br/>
                    Regards, <br/>
                    QR Track Management System";

                // Send the email
                sendMail($AttendanceCheck['Email'], $Subject, $Message);

                // Update the database with the email sent date
                $updateEmailDateQuery = "UPDATE Student_Attendance_Record SET LastEmailSent = NOW() WHERE StudentId = {$StudentSessionID}";
                $this->conn->query($updateEmailDateQuery);

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
}

  public function AttendancePercentage($StudentId=null) {
		
		try{
	
/*			$StudentAttendanceQuery ="SELECT 
				sar.StudentId AS StudentID,
				sar.Name AS FullName,
				round (COALESCE(
						(COUNT(DISTINCT CASE 
                         WHEN sar.AttendanceNum = 'P1' 
                              AND EXISTS (SELECT 1 
                                          FROM Student_Attendance_Record sar2 
                                          WHERE sar2.StudentId = sar.StudentId 
                                          AND sar2.LectureWeek = sar.LectureWeek 
                                          AND sar2.AttendanceNum = 'P2')
                         THEN sar.LectureWeek 
                         END) 
						/ MAX(sar.LectureWeek)) * 100, 
							0
							),0) AS AttendancePercentage
							FROM Student_Attendance_Record sar";*/
					

					$StudentAttendanceQuery ="SELECT 
    StudentId AS StudentID, 
    Name as FullName, 
    round((SUM(CASE WHEN AttendanceNum = 'Present' THEN 1 ELSE 0 END) / 5) * 100) AS AttendancePercentage
FROM 
    Student_Attendance_Record";
		
							if ($StudentId!==Null){
								
							$StudentAttendanceQuery .= " WHERE 
		StudentId = {$StudentId}
    ";
							} 
							
						$StudentAttendanceQuery .= "	
						GROUP BY 
    StudentId, Name";
            $StudentAttendance = $this->conn->query($StudentAttendanceQuery);
			return $StudentAttendance;
			//$this->displayAttendancePercentage($StudentAttendance);
			
			
			
			
		}catch (mysqli_sql_exception $e) {
            die("Error : " . $e->getMessage());
        }	
        
    }


	public function displayAttendancePercentageSearch($StudentAttendance){
			
        while ($row = $StudentAttendance->fetch_assoc()) {
			$StudentSessionID = $row['StudentID'];
			echo "<tr><td align='center'><a href='8_StudentAttendanceRecord.php?StudentSessionID={$StudentSessionID}'>{$row['StudentID']}</a></td>

            <td align='center'>{$row['FullName']}</td>
			
            <td align='center'>{$row['AttendancePercentage']}%</td>
			
            <td align='center'>Warning email sent on Date : </td>
            
            <td align='center'>
			
			
			<form method='POST' action ='Email Sender.php?".SID."'>
            <input type='submit' name='select' value='Email'>
            <input type='hidden' name='PValue' value=''> <!--Define USer ID-->
            </form></td></tr>";
        }
        
		
	}

public function searchfunction($keyword){
		if ($this->keyword != $keyword){
		$this->keyword = $keyword;
		
		try {
			
            $SearchQuery = "SELECT Distinct StudentId FROM Student_Attendance_Record WHERE StudentId LIKE '%".$keyword."%' OR Name LIKE '%".$keyword."%'";
            $SearchResult = $this->conn->query($SearchQuery);
			if ($SearchResult->num_rows>0){
				
			
			
        
        while ($row = $SearchResult->fetch_assoc()) {
			
			$StudentId = $row['StudentId'];
            $Percentage=$this->AttendancePercentage($StudentId);
			//$this->displayAttendancePercentageSearch($Percentage);
        $this->displayAttendancePercentage($Percentage);
        }
        echo "</table>";
			
				}
				
				
				
			 else echo "No Match Found";
			
        } catch (mysqli_sql_exception $e) {
            die("Error : " . $e->getMessage());
        }
		}
		
	}
	
public function IndividualStudentRecord($StudentSessionID) {
    try {
        $NameDisplayQuery = "SELECT DISTINCT Name FROM Student_Attendance_Record WHERE StudentId='{$StudentSessionID}'";
        $NameDisplay = $this->conn->query($NameDisplayQuery);
        
        // Display the student's name and ID
        while ($row = $NameDisplay->fetch_assoc()) {
            echo "<h2>Student Name: " . ucfirst($row['Name']) . "</h2>";
            echo "<h2>Student ID: " . $StudentSessionID . "</h2>";
        }

        // Fetch and display week-wise attendance records
        $WeekWiseAttendanceRecordQuery = "SELECT DISTINCT LectureWeek FROM Student_Attendance_Record WHERE StudentId='{$StudentSessionID}'";
        $WeekWiseAttendanceRecord = $this->conn->query($WeekWiseAttendanceRecordQuery);
        
        echo "<table border='1' width='90%'>
            <tr><th>Lecture Week</th>
            <th>Attendance Marked</th>
            </tr>";
        
        while ($row = $WeekWiseAttendanceRecord->fetch_assoc()) {
            echo "<tr><td align='center'>{$row['LectureWeek']}</td>";
            
            $AttendanceCheckQueryP1 = "SELECT AttendanceNum FROM Student_Attendance_Record WHERE LectureWeek={$row['LectureWeek']} AND StudentId={$StudentSessionID}";
            $AttendanceCheckP1 = $this->conn->query($AttendanceCheckQueryP1);
            
            while ($attendanceRow = $AttendanceCheckP1->fetch_assoc()) {
                echo "<td align='center'>{$attendanceRow['AttendanceNum']}</td>";
            }

            echo "</tr>";
        }
        
        echo "</table>";
        
        // Add form to download student data as CSV
        echo "<br/><form action='download_student_csv.php' method='POST'>
            <input type='submit' name='download_student_csv' value='Download Student Data as CSV' class='csv-button'>
            <input type='hidden' name='StudentSessionID' value='{$StudentSessionID}'>
        </form>";
        
    } catch (mysqli_sql_exception $e) {
        die("Error: " . $e->getMessage());
    }
}


 public function getAttendanceDataForCSV() {
        $studentData = [];
        $query = "SELECT StudentId, Name, ROUND((SUM(CASE WHEN AttendanceNum = 'Present' THEN 1 ELSE 0 END) / 5) * 100) AS AttendancePercentage 
                  FROM Student_Attendance_Record 
                  GROUP BY StudentId, Name";
        $result = $this->conn->query($query);

        while ($row = $result->fetch_assoc()) {
            $studentData[] = [$row['StudentId'], $row['Name'], $row['AttendancePercentage']];
        }
        return $studentData;
    }



public function getAttendanceDataForStudentCSV($StudentSessionID) {
    $studentData = [];
    $query = "SELECT Name, LectureWeek, SubCode, AttendanceNum 
              FROM Student_Attendance_Record 
              WHERE StudentId='{$StudentSessionID}'";
    
    $result = $this->conn->query($query);
    
    // Fetch each row of attendance data
    while ($row = $result->fetch_assoc()) {
        $studentData[] = [$StudentSessionID, $row['Name'], $row['SubCode'], $row['LectureWeek'], $row['AttendanceNum']];
    }
    
    return $studentData;
}

	
	
}



?>
