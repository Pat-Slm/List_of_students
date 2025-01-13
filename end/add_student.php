<?php
$servername = "localhost";
$username = "u299560388_651225";
$password = "QK2033Zg";


ini_set('display_errors', 1);
error_reporting(E_ALL);

try {
    $conn = new PDO("mysql:host=$servername;dbname=u299560388_651225", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit; // หยุดการทำงานถ้าการเชื่อมต่อไม่สำเร็จ
}

// สำหรับการเพิ่มข้อมูลนักเรียนใหม่
if (isset($_POST['add'])) {
    $studentName = $_POST['StudentName'];
    $studentLastname = $_POST['StudentLastname'];
    $address = $_POST['Address'];
    $telephone = $_POST['Telephone'];
    $depID = $_POST['DepID'];
    $hobbies = $_POST['HobbyName'];

    try {
        // เพิ่มข้อมูลนักเรียน
        $insertStudentSQL = "INSERT INTO tbl_student (StudentName, StudentLastname, Address, Telephone, DepID) 
                             VALUES (:studentName, :studentLastname, :address, :telephone, :depID)";
        
        $stmt = $conn->prepare($insertStudentSQL);
        $stmt->bindParam(':studentName', $studentName);
        $stmt->bindParam(':studentLastname', $studentLastname);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':telephone', $telephone);
        $stmt->bindParam(':depID', $depID, PDO::PARAM_INT);
    
        if ($stmt->execute()) {
            $sid = $conn->lastInsertId(); // รับ SID ล่าสุดที่เพิ่งเพิ่ม
        
            // แทรกงานอดิเรกใหม่ลงใน tbl_StudentHobby
            $hobbyArray = explode(',', $hobbies);
            foreach ($hobbyArray as $hobby) {
                $hobby = trim($hobby);
        
                // ตรวจสอบว่ามีงานอดิเรกอยู่ในฐานข้อมูลหรือไม่
                $hobbySQL = "SELECT HobbyID FROM tbl_hobby WHERE HobbyName = :hobby";
                $hobbyStmt = $conn->prepare($hobbySQL);
                $hobbyStmt->bindParam(':hobby', $hobby);
                $hobbyStmt->execute();
                $hobbyData = $hobbyStmt->fetch(PDO::FETCH_ASSOC);
        
                if (!$hobbyData) {
                    // ถ้างานอดิเรกยังไม่มีในฐานข้อมูล ให้เพิ่มใหม่
                    $insertHobbySQL = "INSERT INTO tbl_hobby (HobbyName) VALUES (:hobby)";
                    $insertHobbyStmt = $conn->prepare($insertHobbySQL);
                    $insertHobbyStmt->bindParam(':hobby', $hobby);
                    $insertHobbyStmt->execute();
                    $hobbyID = $conn->lastInsertId();
                } else {
                    $hobbyID = $hobbyData['HobbyID'];
                }
        
                // แทรกงานอดิเรกใหม่ลงใน tbl_StudentHobby
                $insertStudentHobbySQL = "INSERT INTO tbl_StudentHobby (SID, HobbyID) VALUES (:sid, :hobbyID)";
                $insertStudentHobbyStmt = $conn->prepare($insertStudentHobbySQL);
                $insertStudentHobbyStmt->bindParam(':sid', $sid, PDO::PARAM_INT);
                $insertStudentHobbyStmt->bindParam(':hobbyID', $hobbyID, PDO::PARAM_INT);
                $insertStudentHobbyStmt->execute();
            }
        
            echo "<script>alert('เพิ่มข้อมูลนักเรียนสำเร็จ');</script>";
            echo "<script>window.location.href = 'main.php';</script>";
        
        } else {
            echo "<script>alert('การเพิ่มข้อมูลนักเรียนล้มเหลว');</script>";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// ดึงข้อมูล Department
$departmentSQL = "SELECT * FROM tbl_department";
$departments = $conn->query($departmentSQL)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มข้อมูลนักเรียน</title>
</head>
<body>
    <form action="" method="post">
        <label for="StudentName">ชื่อ:</label>
        <input type="text" name="StudentName" required><br>
        
        <label for="StudentLastname">นามสกุล:</label>
        <input type="text" name="StudentLastname" required><br>

        <label for="Address">ที่อยู่:</label>
        <input type="text" name="Address" required><br>

        <label for="Telephone">เบอร์โทรศัพท์:</label>
        <input type="text" name="Telephone" required><br>

        <label for="HobbyName">งานอดิเรก (คั่นด้วยจุลภาค):</label>
        <input type="text" name="HobbyName" required><br>

        <label for="DepID">แผนก:</label>
        <select name="DepID" required>
            <?php foreach ($departments as $department): ?>
                <option value="<?php echo $department['DepID']; ?>">
                    <?php echo $department['Department']; ?>
                </option>
            <?php endforeach; ?>
        </select><br>

        <input type="submit" name="add" value="เพิ่มข้อมูลนักเรียน">
    </form>
</body>
</html>
