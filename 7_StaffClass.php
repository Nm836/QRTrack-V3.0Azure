<?php
class Staff {

    private $conn;
    private $UID = "";
    private $StudentListDisplay = "";
    private $keyword = "";
    private $StudentSessionID = "";
    private $NewSubCode = "";
    private $NewSubName = "";
    

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


    public function DisplayStudentRecordFunction($selectedSubject, $CurrentWeek){

    try {

        echo "<h3>Enrolled Student Data</h3>";
    $STudentRecordQuery ="SELECT DISTINCT 
    StudentId, 
    Name, 
    ROUND(
        (SUM(CASE WHEN AttendanceNum = 'Present' THEN 1 ELSE 0 END) * 100) / :CurrentWeek, 0
    ) AS AttendancePercentage,
    MAX(LastEmailSent) AS LastEmailSent
    FROM 
    Student_Attendance_Record
    WHERE SubCode = :SubjectCode 
    GROUP BY 
    StudentId, 
    Name
    ";
    $stmt = $this->conn->prepare($STudentRecordQuery);
    $stmt->bindParam(':CurrentWeek', $CurrentWeek);
    $stmt->bindParam(':SubjectCode', $selectedSubject);
    
    $stmt->execute();
    $studentInfo = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<table border='1' width='90%'>
                <tr><th>Student ID</th>
                <th>Name</th>
                <th>Attendance Percentage</th>
                <th>Action Taken</th>
                <th>Send E-Mail</th></tr>";

    foreach ($studentInfo as $row){
        echo "<tr> <td align='center'><a href='8_StudentAttendanceRecord.php?StudentSessionID={$row['StudentId']}&SubjectCode={$selectedSubject}'>{$row['StudentId']}</a></td>";
        echo "<td align='center'>". ucwords($row['Name'])."</td>";
   /* echo "<td align='center'> {$row['SubCode']}</td>";
    echo "<td align='center'> {$row['LectureWeek']}</td>";*/
        echo "<td align='center'> {$row['AttendancePercentage']} %</td>";
        echo "<td align='center'> Warning mail sent on Date:  ".date("d/m/y", strtotime($row['LastEmailSent']))."</td>";
        echo "<td align='center'> 
        <form method='POST' action ='Email Sender.php?".SID."'>
                <input type='submit' name='select' value='Email'>
                <input type='hidden' name='PValue' value=''>
                </form>
        </td>";
        echo "</tr>";

        

    }
    echo "All runned";
    }catch (PDOException $e) {
    die("Error: " . $e->getMessage());
    }

    }


    public function IndividualStudentRecord($StudentSessionID, $selectedSubject) {
    try {
        //$NameDisplayQuery = "SELECT DISTINCT Name FROM Student_Attendance_Record WHERE StudentId = :StudentId AND SubCode = :SubjectCode ";
        $NameDisplayQuery = "SELECT Name FROM Student_Attendance_Record WHERE StudentId = :StudentId AND SubCode = :SubjectCode ";
        $NameDisplay=$this->conn->prepare($NameDisplayQuery);
        $NameDisplay->bindParam(':StudentId', $StudentSessionID);
        $NameDisplay->bindParam(':SubjectCode', $selectedSubject);
        $NameDisplay->execute();
    $studentInfo = $NameDisplay->fetchAll(PDO::FETCH_ASSOC);
    echo "<h2>Student Name: " . ucfirst($row['Name']) . "</h2>";
    echo "<h2>Student ID: " . $StudentSessionID . "</h2>";
    echo "<h2>Student ID: " . $selectedSubject . "</h2>";
/*    foreach ($studentInfo as $row){

            echo "<h2>Student Name: " . ucfirst($row['Name']) . "</h2>";
            echo "<h2>Student ID: " . $StudentSessionID . "</h2>";
            echo "<h2>Student ID: " . $selectedSubject . "</h2>";
        }
*/
        // Fetch and display week-wise attendance records
        $WeekWiseAttendanceRecordQuery = "SELECT DISTINCT LectureWeek FROM Student_Attendance_Record WHERE StudentId= :StudentId AND SubCode = :SubjectCode ";
        $WeekWiseAttendanceRecord = $this->conn->prepare($WeekWiseAttendanceRecordQuery);
        $WeekWiseAttendanceRecord->bindParam(':StudentId', $StudentSessionID);
        $WeekWiseAttendanceRecord->bindParam(':StudentId', $selectedSubject);
        
        $WeekWiseAttendanceRecord->execute();
        $WeekWiseInfo = $WeekWiseAttendanceRecord->fetchAll(PDO::FETCH_ASSOC);

        echo "<table border='1' width='90%'>
            <tr><th>Lecture Week</th>
            <th>Attendance Marked</th>
            </tr>";
        
        foreach ($WeekWiseInfo as $row){
        echo "<tr><td align='center'>{$row['LectureWeek']}</td>";
            
            $AttendanceCheckQuery = "SELECT AttendanceNum FROM Student_Attendance_Record WHERE LectureWeek={$row['LectureWeek']} AND StudentId= :StudentId ";
        $AttendanceCheck = $this->conn->prepare($AttendanceCheckQuery);
        $AttendanceCheck->bindParam(':StudentId', $StudentSessionID);
        $AttendanceCheck->execute();
        $AttendanceCheckInfo = $AttendanceCheck->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($AttendanceCheckInfo as $attendanceRow){
            
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
                                WHERE StudentId LIKE :StudentID OR Name LIKE :StudentName";
                $SearchQueryCheck = $this->conn->prepare($SearchQuery);
    
                // Prepare the parameter values with '%' for LIKE
                $keywordParam = "%" . $keyword . "%";
    
                // Bind the correct parameters
                $SearchQueryCheck->bindParam(':StudentID', $keywordParam);
                $SearchQueryCheck->bindParam(':StudentName', $keywordParam);
                $SearchQueryCheck->execute();
    
                // Fetching all rows
                $rows = $SearchQueryCheck->fetchAll(PDO::FETCH_ASSOC);
    
                // Check if rows are retrieved
                if (!empty($rows)) { 
                    echo "<h3>Enrolled Student Data</h3>";
                    echo "<table border='1' width='90%'>
                            <tr>
                                <th>Student ID</th>
                                <th>Name</th>
                                <th>Attendance Percentage</th>
                                <th>Action Taken</th>
                                <th>Send E-Mail</th>
                            </tr>";
    
                    // Loop through the search results
                    foreach ($rows as $row) {
                        // Fetch attendance details for each student
                        $StudentId = $row['StudentId'];
                        $STudentRecordQuery = "
                            SELECT DISTINCT 
                                StudentId, 
                                Name, 
                                ROUND(
                                    (SUM(CASE WHEN AttendanceNum = 'Present' THEN 1 ELSE 0 END) * 100) / COUNT(*), 0
                                ) AS AttendancePercentage,
                                MAX(LastEmailSent) AS LastEmailSent
                            FROM 
                                Student_Attendance_Record
                            WHERE StudentId = :StudentId
                            GROUP BY 
                                StudentId, 
                                Name";
                        $stmt = $this->conn->prepare($STudentRecordQuery);
                        $stmt->bindParam(':StudentId', $StudentId);
                        $stmt->execute();
                        $studentInfo = $stmt->fetch(PDO::FETCH_ASSOC);
    
                        // Display the student's information
                        if ($studentInfo) {
                            echo "<tr>
                                    <td align='center'><a href='8_StudentAttendanceRecord.php?StudentSessionID={$studentInfo['StudentId']}'>{$studentInfo['StudentId']}</a></td>
                                    <td align='center'>{$studentInfo['Name']}</td>
                                    <td align='center'>{$studentInfo['AttendancePercentage']}%</td>
                                    <td align='center'>Warning mail sent on Date: " . date("d/m/y", strtotime($studentInfo['LastEmailSent'])) . "</td>
                                    <td align='center'>
                                        <form method='POST' action='EmailSender.php?".SID."'>
                                            <input type='submit' name='select' value='Email'>
                                            <input type='hidden' name='PValue' value=''>
                                        </form>
                                    </td>
                                </tr>";
                        }
                    }
    
                    echo "</table>";  // Close the table after the foreach loop
                } else {
                    echo "No Match Found";
                }
            } catch (PDOException $e) {
                die("Error: " . $e->getMessage());
            }
        }
        }
    
    
    

    public function getAttendanceDataForCSV() {
        $studentData = [];
        $STudentRecordQuery ="SELECT DISTINCT 
        StudentId, 
        Name, 
        ROUND(
        (SUM(CASE WHEN AttendanceNum = 'Present' THEN 1 ELSE 0 END) * 100) / COUNT(*), 0
        ) AS AttendancePercentage 
        FROM 
        Student_Attendance_Record
        GROUP BY 
        StudentId, 
        Name
        ";
        $stmt = $this->conn->prepare($STudentRecordQuery);
        $stmt->execute();
        $studentInfo = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($studentInfo as $row) {
            $studentData[] = [$row['StudentId'], $row['Name'], $row['AttendancePercentage']];
        }
        return $studentData;
         }



    public function getAttendanceDataForStudentCSV($StudentSessionID) {
    $studentData = [];
    $query = "SELECT Name, LectureWeek, SubCode, AttendanceNum 
              FROM Student_Attendance_Record 
              WHERE StudentId=:StudentId ";
        
        $NameDisplay=$this->conn->prepare($query);
        $NameDisplay->bindParam(':StudentId', $StudentSessionID);
        $NameDisplay->execute();
        $studentInfo = $NameDisplay->fetchAll(PDO::FETCH_ASSOC);

        foreach ($studentInfo as $row) {
            $studentData[] = [$StudentSessionID, $row['Name'], $row['SubCode'], $row['LectureWeek'], $row['AttendanceNum']];
        }
    
    
    
        return $studentData;
    }
    

    public function AddNewSubject($NewSubCode, $NewSubName) { 
        try {
            // Corrected SQL query
            $addSubQuery = "INSERT INTO Subject_Record (SubCode, SubName) VALUES (:SubCode, :SubName)";
            $addSub = $this->conn->prepare($addSubQuery);
            
            // Bind the parameters
            $addSub->bindParam(':SubCode', $NewSubCode);
            $addSub->bindParam(':SubName', $NewSubName);
            
            // Execute the query
            $addSub->execute();
            
            // Success message
            echo "<p>{$NewSubCode} - ".ucwords($NewSubName)." has been added</p>";
            
            
        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
        }
        }
    


public function selectSubject(){
    try{   
    
           
    $selectSubQuery = "SELECT * FROM Subject_Record";

    //$selectSubQuery = "SELECT * FROM Student_Attendance_Record";
    $selectSub = $this->conn->prepare($selectSubQuery);    
    // Execute the query
    $selectSub->execute();
    $SubjectInfo = $selectSub->fetchAll(PDO::FETCH_ASSOC);
        echo "<p>Select Subject and add current week.</p>";
        echo "<form action ='' method='POST'>";
        echo "<select name='SelectSubject' required>";
    foreach ($SubjectInfo as $SubjectRow){
        echo "<option value ='";
        echo $SubjectRow['SubCode'];
        echo "'>";
        echo $SubjectRow['SubCode'];
        echo " - ";
        echo ucwords($SubjectRow['SubName']);
        echo "</option>";

    }
    echo "</select>";
    echo "<input type='text' name='CurrentWeek' placeholder = 'Current Week'>";
    echo "<input type='submit' name='ShowStudentList' value='Show'>
        
            </form>";
            
    /*


    if (empty($subInfo)) {
        echo "No subjects found in the database.";
    } else {

    echo "<form action='' method='POST'>
        <select name='SelectSubject' required>";
        echo "Stage func 2";
    foreach ($subInfo as $rows) {
        echo "<option value='".$rows['SubCode']."'>".ucwords($rows['SubName'])."</option>"; 
    }

    echo "</select>
        <input type='submit' name='ShowStudentList' value='Show'>
        </form>";

        echo "Stage  func 3";
    */
        }
    catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
    }
    
}
?>
