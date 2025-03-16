<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

try {
    $dbconnect = new PDO("mysql:host=localhost;dbname=usjr", "root", "root");
    $dbconnect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['student_id'])) {
        $studid = $_GET['student_id'];
        
        $sql = "SELECT s.*, c.collfullname, p.progfullname 
                FROM usjr.students s 
                JOIN usjr.colleges c ON s.studcollid = c.collid 
                JOIN usjr.programs p ON s.studprogid = p.progid 
                WHERE s.studid = :studid";
        $stmt = $dbconnect->prepare($sql);
        $stmt->execute([':studid' => $studid]);
        $student = $stmt->fetch(PDO::FETCH_ASSOC);

        $_SESSION['student'] = $student;
    }

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $data = json_decode(file_get_contents("php://input"), true);
        $studid = $data["studid"];

        $sql = "DELETE FROM usjr.students WHERE studid = :studid";
        $stmt = $dbconnect->prepare($sql);
        $stmt->bindParam(':studid', $studid, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "message" => $stmt->errorInfo()[2]]);
        }
        exit;
    }

} catch(PDOException $e) {
    echo json_encode(["success" => false, "message" => "Connection failed: " . $e->getMessage()]);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./design/student_form.css">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Delete Student</title>
</head>
<body>
    <div class="form_container">
        <h2>Delete Student Information</h2>
        <form id="delete_form" onsubmit="confirmDelete(event)">
            <div class="form_group">
                <label for="studid">Student ID</label>
                <input type="text" id="studid" name="studid" style="background: grey;" value="<?php echo $_SESSION['student']['studid']; ?>" readonly>
            </div>
            <div class="form_group">
                <label for="studfirstname">First Name</label>
                <input type="text" id="studfirstname" name="studfirstname" style="background: grey;" value="<?php echo $_SESSION['student']['studfirstname']; ?>" readonly>
            </div>
            <div class="form_group">
                <label for="studlastname">Last Name</label>
                <input type="text" id="studlastname" name="studlastname" style="background: grey;" value="<?php echo $_SESSION['student']['studlastname']; ?>" readonly>
            </div>
            <div class="form_group">
                <label for="studmidname">Middle Name</label>
                <input type="text" id="studmidname" name="studmidname" style="background: grey;" value="<?php echo $_SESSION['student']['studmidname']; ?>" readonly>
            </div>
            <div class="form_group">
                <label for="studcollid">College</label>
                <input type="text" id="studcollid" name="studcollid" style="background: grey;" value="<?php echo $_SESSION['student']['collfullname']; ?>" readonly>
            </div>
            <div class="form_group">
                <label for="studprogid">Program</label>
                <input type="text" id="studprogid" name="studprogid" style="background: grey;" value="<?php echo $_SESSION['student']['progfullname']; ?>" readonly>
            </div>
            <div class="form_group">
                <label for="studyear">Year</label>
                <input type="text" id="studyear" name="studyear" style="background: grey;" value="<?php echo $_SESSION['student']['studyear']; ?>" readonly>
            </div>
            <div class="form_buttons">
                <button type="submit" class="delete">Delete</button>
                <button type="button" class="cancel" onclick="cancelDelete()">Cancel</button>
            </div>
        </form>
    </div>

    <script src="./javascript/student_delete.js"></script>
    
</body>
</html>
