<?php
$servername = "localhost";
$username = "u299560388_651225";
$password = "QK2033Zg";

try {
    $conn = new PDO("mysql:host=$servername;dbname=u299560388_651225", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

if (isset($_GET['sid'])) {
    $sid = $_GET['sid']; 

    $sql = "SELECT 
                s.SID, 
                s.StudentName, 
                s.StudentLastname, 
                s.Address,
                s.Telephone,
                s.DepID,
                GROUP_CONCAT(h.HobbyName SEPARATOR ', ') AS Hobbies
            FROM 
                tbl_student s
            LEFT JOIN 
                tbl_StudentHobby sh ON s.SID = sh.SID
            LEFT JOIN 
                tbl_hobby h ON sh.HobbyID = h.HobbyID
            WHERE 
                s.SID = :sid
            GROUP BY 
                s.SID, s.StudentName, s.StudentLastname, s.Address, s.Telephone, s.DepID";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':sid', $sid, PDO::PARAM_INT);
    $stmt->execute();
    $studentData = $stmt->fetch(PDO::FETCH_ASSOC); 
} else {
    echo "No student selected.";
    exit;
}

// สำหรับการแก้ไขข้อมูล
if (isset($_POST['update'])) {
    $sid = $_POST['sid'];
    $studentName = $_POST['StudentName'];
    $studentLastname = $_POST['StudentLastname'];
    $address = $_POST['Address'];
    $telephone = $_POST['Telephone'];
    $depID = $_POST['DepID'];

    $updateSQL = "UPDATE tbl_student SET 
                      StudentName = :studentName,
                      StudentLastname = :studentLastname,
                      Address = :address,
                      Telephone = :telephone,
                      DepID = :depID
                  WHERE SID = :sid";

    $stmt = $conn->prepare($updateSQL);
    $stmt->bindParam(':studentName', $studentName);
    $stmt->bindParam(':studentLastname', $studentLastname);
    $stmt->bindParam(':address', $address);
    $stmt->bindParam(':telephone', $telephone);
    $stmt->bindParam(':depID', $depID, PDO::PARAM_INT);
    $stmt->bindParam(':sid', $sid, PDO::PARAM_INT);
    
    if ($stmt->execute()) {
        echo "<script>alert('แก้ไขข้อมูลสำเร็จ');</script>";
        echo "<script>window.location.href = 'main.php';</script>";
    } else {
        echo "<script>alert('การแก้ไขข้อมูลล้มเหลว');</script>";
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
    <title>แก้ไขข้อมูลนักเรียน</title>
</head>
<body>
    <form action="" method="post">
        <input type="hidden" name="sid" value="<?php echo $studentData['SID']; ?>">
        <label for="StudentName">ชื่อ:</label>
        <input type="text" name="StudentName" value="<?php echo $studentData['StudentName']; ?>" required><br>
        
        <label for="StudentLastname">นามสกุล:</label>
        <input type="text" name="StudentLastname" value="<?php echo $studentData['StudentLastname']; ?>" required><br>

        <label for="Address">ที่อยู่:</label>
        <input type="text" name="Address" value="<?php echo $studentData['Address']; ?>" required><br>

        <label for="Telephone">เบอร์โทรศัพท์:</label>
        <input type="text" name="Telephone" value="<?php echo $studentData['Telephone']; ?>" required><br>

        <label for="DepID">แผนก:</label>
        <select name="DepID" required>
            <?php foreach ($departments as $department): ?>
                <option value="<?php echo $department['DepID']; ?>" <?php if ($department['DepID'] == $studentData['DepID']) echo 'selected'; ?>>
                    <?php echo $department['Department']; ?>
                </option>
            <?php endforeach; ?>
        </select><br>

        <input type="submit" name="update" value="บันทึกการแก้ไข">
    </form>
</body>
</html>
