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
        Name";
        $stmt = $this->conn->prepare($STudentRecordQuery);
        $stmt->bindParam(':CurrentWeek', $CurrentWeek);
        $stmt->bindParam(':SubjectCode', $selectedSubject);
    
        $stmt->execute();
        $studentInfo = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($studentInfo)){
        echo "<h3>Enrolled Student Data</h3>";
        echo "<table>
                <tr><th>Student ID</th>
                <th>Name</th>
                <th>Attendance Percentage</th>
                <th>Action Taken</th>
                <th>Send E-Mail</th></tr>";

        foreach ($studentInfo as $row){
            echo "<tr> <td align='center'><a href='8_StudentAttendanceRecord.php?StudentSessionID={$row['StudentId']}&SubCode={$selectedSubject}'>{$row['StudentId']}</a></td>";
            echo "<td align='center'>". ucwords($row['Name'])."</td>";
   
            echo "<td align='center'> {$row['AttendancePercentage']} %</td>";
        if (!empty($row['LastEmailSent'])) {
            // If LastEmailSent is not null, format and display the date
            echo "<td align='center'> Warning mail sent on Date:  ".date("d/m/y", strtotime($row['LastEmailSent']))."</td>";
            
        } else {
            // If LastEmailSent is null, display the "No attendance marked" message
            echo "<td align='center'> No Warning Email Issued</td>";
        }
        
         echo "<td align='center'> 
            <form method='POST' action ='Email Sender.php?StudentId={$row['StudentId']}&SubCode={$selectedSubject}'>
                <input type='submit' name='select' value='Email' class='buttons'>
                
                </form>
            </td>";
            //<input type='hidden' name='EmailID' value='{$row['StudentId']}'>
        echo "</tr>";

        

        }
            echo "<form action='download_csv.php' method='POST'>
                <input type='submit' name='download_csv' value='Download-CSV' class='buttons'>
                </form>";


        }
        else {
        echo "<p>No record to display</p>";
        }
        }catch (PDOException $e) {
        die("Error: " . $e->getMessage());
        }

    }


    public function IndividualStudentRecord($StudentSessionID, $selectedSubject) {
        try {
            // Fetch and display student's name
            $NameDisplayQuery = "SELECT DISTINCT Name FROM Student_Attendance_Record WHERE StudentId = :StudentId AND SubCode = :SubjectCode";
            $NameDisplay = $this->conn->prepare($NameDisplayQuery);
            $NameDisplay->bindParam(':StudentId', $StudentSessionID);
            $NameDisplay->bindParam(':SubjectCode', $selectedSubject);
            $NameDisplay->execute();
            $studentInfo = $NameDisplay->fetchAll(PDO::FETCH_ASSOC);
    
            foreach ($studentInfo as $row){
                echo "<h3>Student Name: <strong>" . ucwords($row['Name']) . "</strong></h3>";
            }
            echo "<h3>Student ID: <strong>" . $StudentSessionID . "</strong></h3>";
            echo "<h3>Subject Code: <strong>" . $selectedSubject . "</strong></h3>";
    
            // Fetch and display week-wise attendance records
            $WeekWiseAttendanceRecordQuery = "SELECT DISTINCT LectureWeek FROM Student_Attendance_Record WHERE StudentId = :StudentId AND SubCode = :SubjectCode";
            $WeekWiseAttendanceRecord = $this->conn->prepare($WeekWiseAttendanceRecordQuery);
            $WeekWiseAttendanceRecord->bindParam(':StudentId', $StudentSessionID);
            $WeekWiseAttendanceRecord->bindParam(':SubjectCode', $selectedSubject);  // Fixed here
            $WeekWiseAttendanceRecord->execute();
            $WeekWiseInfo = $WeekWiseAttendanceRecord->fetchAll(PDO::FETCH_ASSOC);
    
            echo "<table>
                <tr><th>Lecture Week</th>
                <th>Attendance Marked</th></tr>";
            
            foreach ($WeekWiseInfo as $row) {
                echo "<tr><td align='center'>{$row['LectureWeek']}</td>";
    
                // Fetch attendance for each week
                $AttendanceCheckQuery = "SELECT AttendanceNum FROM Student_Attendance_Record WHERE LectureWeek = :LectureWeek AND StudentId = :StudentId";
                $AttendanceCheck = $this->conn->prepare($AttendanceCheckQuery);
                $AttendanceCheck->bindParam(':LectureWeek', $row['LectureWeek']);  // Bind LectureWeek
                $AttendanceCheck->bindParam(':StudentId', $StudentSessionID);      // Bind StudentId
                $AttendanceCheck->execute();
                $AttendanceCheckInfo = $AttendanceCheck->fetchAll(PDO::FETCH_ASSOC);
    
                foreach ($AttendanceCheckInfo as $attendanceRow) {
                    echo "<td align='center'>{$attendanceRow['AttendanceNum']}</td>";
                }
    
                echo "</tr>";
            }
    
            echo "</table>";
    
            // Add form to download student data as CSV
            echo "<br/><form action='download_student_csv.php' method='POST'>
                <input type='submit' name='download_student_csv' value='Download-CSV' class='buttons'>
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
                                        <form method='POST' action='Email Sender.php?StudentId={$row['StudentId']}&SubCode={$selectedSubject}'>
                                            <input type='submit' name='select' value='Email' class='buttons'>
                                            
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
        echo "<p>Select Subject and Add Week :</p>";
        echo "<form action ='' method='POST'>";
        echo "<div class='select-sub'>";
        
        echo "<select name='SelectSubject' class ='option-select' required>";
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
        
        echo "<input type='text' name='CurrentWeek' placeholder = 'Current Week' class='input-week'>";
        echo "<input type='submit' name='ShowStudentList' value='Show' class='add-button'>
                </div>
        
            </form>";
        
        }
        catch (PDOException $e) {
            die("Error: " . $e->getMessage());
        }
        }
    
     public function selectSubjectQRCode(){
        try{   
        
        $selectSubQuery = "SELECT * FROM Subject_Record";
        $selectSub = $this->conn->prepare($selectSubQuery);    
        // Execute the query
        $selectSub->execute();
        $SubjectInfo = $selectSub->fetchAll(PDO::FETCH_ASSOC);
            
                      
            echo "<form action ='qr_generated.php' method='POST'>
            <label>Subject :</label>";
            echo "<div class='select-subQR'>";
            echo "<select name='subject_code' class ='option-selectQR' required> ";
            foreach ($SubjectInfo as $Row){
                echo "<option value ='";
                echo $Row['SubCode'];
                echo "'>";
                echo $Row['SubCode'];
                echo " - ";
                echo ucwords($Row['SubName']);
                echo "</option>";
    
            }
            echo "</select></div>";
            
            echo "<label for='week'>Week:</label>
            <input type='text' id='week' name='week' class='input-textQR' required>
        
            <label for='validity'>Validity in Minutes:</label>
            <input type='text' id='validity' name='validity' class='input-textQR' required>
        
            <label for='latitude'>Latitude:</label>
            <input type='float' name='latitude' id='latitude' placeholder='Enter Latitude' class='input-textQR' required>

            <label for='longitude'>Longitude:</label>
            <input type='float' name='longitude' id='longitude' placeholder='Enter Longitude' class='input-textQR' required>
            <br /><br />
            <button type='submit' class='buttons-QR'>Generate QR Code</button>
            </form>";

             
            }
        catch (PDOException $e) {
            die("Error: " . $e->getMessage());
        }
        } 
}
?>
