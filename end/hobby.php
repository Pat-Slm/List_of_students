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
                GROUP_CONCAT(h.HobbyName SEPARATOR ', ') AS Hobbies
            FROM 
                tbl_student s
            JOIN 
                tbl_StudentHobby sh ON s.SID = sh.SID
            JOIN 
                tbl_hobby h ON sh.HobbyID = h.HobbyID
            WHERE 
                s.SID = :sid
            GROUP BY 
                s.SID, s.StudentName, s.StudentLastname";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':sid', $sid, PDO::PARAM_INT);
    $stmt->execute();
    $studentData = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    echo "No student selected.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Hobbies</title>
</head>
<body>
    <h1>Hobbies of <?php echo $studentData['StudentName'] . " " . $studentData['StudentLastname']; ?></h1>
    
    <?php if ($studentData) { ?>
        <table>
            <thead>
                <tr>
                    <th>SID</th>
                    <th>Student Name</th>
                    <th>Student Lastname</th>
                    <th>Hobbies</th> <!-- คอลัมน์งานอดิเรก -->
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php echo $studentData['SID']; ?></td>
                    <td><?php echo $studentData['StudentName']; ?></td>
                    <td><?php echo $studentData['StudentLastname']; ?></td>
                    <td><?php echo $studentData['Hobbies']; ?></td> <!-- แสดงงานอดิเรก -->
                </tr>
            </tbody>
        </table>
    <?php } else { ?>
        <p>No hobbies found for this student.</p>
    <?php } ?>
</body>
</html>
