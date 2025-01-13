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

if (isset($_POST['delete'])) {
    $sid = $_POST['sid']; 
    $deleteSQL = "DELETE FROM tbl_student WHERE SID = :sid";
    $stmt = $conn->prepare($deleteSQL);
    $stmt->bindParam(':sid', $sid, PDO::PARAM_INT);
    if ($stmt->execute()) {
        echo "<script>alert('ลบข้อมูลสำเร็จ');</script>";
        echo "<script>window.location.href = window.location.href;</script>";
    } else {
        echo "<script>alert('การลบข้อมูลล้มเหลว');</script>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Information</title>
    <style>
        body {
            background: #f4f4f4;
            height: 100vh;
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
            background: white;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        input[type="submit"], a.edit-button, a.hobby-button {
            font-family: 'Ubuntu', sans-serif;
            width: 100px;
            height: 40px;
            font-size: 16px;
            font-weight: bold;
            color: #fff;
            background-color: #d9534f;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-align: center;
            display: inline-block;
            line-height: 40px;
            margin-right: 5px;
            transition: background-color 0.3s;
        }
        a.edit-button:hover, a.hobby-button:hover {
            background-color: #0275d8;
        }
        input[type="submit"]:hover {
            background-color: #c9302c;
        }
    </style>
</head>
<h1>ชื่อนักศึกษา</h1>
<a href="add_student.php">เพิ่มข้อมูล</a>
<body>
    <table id="mytable">
        <thead>
            <tr>
                <th>SID</th>
                <th>Student Name</th>
                <th>Student Lastname</th>
                <th>Age</th>
                <th>Year</th>
                <th>Department</th>
                <th>Actions</th> <!-- คอลัมน์สำหรับปุ่มการกระทำต่างๆ -->
            </tr>
        </thead>
        <tbody>
    <?php
        $stml = $conn->query("SELECT 
                s.SID, 
                s.StudentName, 
                s.StudentLastname, 
                s.Age,         
                s.Year,        
                d.Department, 
                h.HobbyName
            FROM 
                tbl_student s
            JOIN 
                tbl_department d ON s.DepID = d.DepID
            JOIN 
                tbl_StudentHobby sh ON s.SID = sh.SID
            JOIN 
                tbl_hobby h ON sh.HobbyID = h.HobbyID;"
        );
        $stml->execute();

        $result = $stml->fetchAll();
        foreach($result as $row){
    ?>
          <tr>
            <td><?php echo $row['SID']; ?></td>
            <td><?php echo $row['StudentName']; ?></td>
            <td><?php echo $row['StudentLastname']; ?></td>
            <td><?php echo $row['Age']; ?></td>
            <td><?php echo $row['Year']; ?></td>
            <td><?php echo $row['Department']; ?></td>
            <td>
                <a href="hobby.php?sid=<?php echo $row['SID']; ?>" class="hobby-button">Hobby</a>

                <a href="edit_student.php?sid=<?php echo $row['SID']; ?>" class="edit-button">แก้ไข</a>
                
                <form method="POST" action="" style="display:inline-block;">
                    <input type="hidden" name="sid" value="<?php echo $row['SID']; ?>">
                    <input type="submit" name="delete" value="ลบ" onclick="return confirm('คุณต้องการลบข้อมูลนี้หรือไม่?');">
                </form>

            </td>
          </tr>
    <?php
        }
    ?>
        </tbody>
    </table>
</body>
</html>
