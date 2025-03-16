<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

try {
    $dbconnect = new PDO("mysql:host=localhost;dbname=usjr", "root", "root");
    $dbconnect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["action"]) && $_GET["action"] === "fetch") {
        $colleges_stmt = $dbconnect->query("SELECT collid, collfullname FROM colleges");
        $colleges = $colleges_stmt->fetchAll(PDO::FETCH_ASSOC);

        $departments_stmt = $dbconnect->query("SELECT deptid, deptfullname, deptcollid FROM departments");
        $departments = $departments_stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(["colleges" => $colleges, "departments" => $departments]);
        exit;
    }

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $data = json_decode(file_get_contents("php://input"), true);

        $program_id = $data["prog_id"];
        $program_fullname = $data["prog_name"];
        $program_shortname = $data["prog_short"];
        $program_collegeid = $data["prog_collid"];
        $program_departmentid = $data["prog_deptid"];

        $check_stmt = $dbconnect->prepare("SELECT progid FROM programs WHERE progid = :program_id");
        $check_stmt->bindParam(':program_id', $program_id);
        $check_stmt->execute();
        
        if ($check_stmt->rowCount() > 0) {
            echo json_encode(["error" => "Program ID already exists"]);
            exit;
        }

        $sql = "INSERT INTO programs (progid, progfullname, progshortname, progcollid, progcolldeptid) 
                VALUES (:program_id, :program_fullname, :program_shortname, :program_collegeid, :program_departmentid)";
        $stmt = $dbconnect->prepare($sql);
        $stmt->bindParam(':program_id', $program_id);
        $stmt->bindParam(':program_fullname', $program_fullname);
        $stmt->bindParam(':program_shortname', $program_shortname);
        $stmt->bindParam(':program_collegeid', $program_collegeid);
        $stmt->bindParam(':program_departmentid', $program_departmentid);

        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["error" => $stmt->errorInfo()[2]]);
        }
        exit;
    }
} catch (PDOException $e) {
    echo json_encode(["error" => "Connection failed: " . $e->getMessage()]);
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./design/prog_form.css">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Add Program</title>
</head>
<body>
    <div class="form_container">
        <h2>Add Program Information</h2>
        <form onsubmit="submitForm(event)">
            <div class="form_group">
                <label for="prog_id">Program ID</label>
                <input type="number" id="prog_id" name="prog_id">
            </div>
            <div class="form_group">
                <label for="prog_name">Program Full Name</label>
                <input type="text" id="prog_name" name="prog_name">
            </div>
            <div class="form_group">
                <label for="prog_short">Program Short Name</label>
                <input type="text" id="prog_short" name="prog_short">
            </div>
            <div class="form_group">
                <label for="prog_collid">Program College</label>
                <select id="prog_collid" name="prog_collid">
                    <option value="">----- Select College -----</option>
                </select>
            </div>
            <div class="form_group">
                <label for="prog_deptid">Program Department</label>
                <select id="prog_deptid" name="prog_deptid">
                    <option value="">----- Select Department -----</option>
                </select>
            </div>
            <div class="form_buttons">
                <button type="submit" class="save">Save</button>
                <button type="reset" class="clear">Clear</button>
                <button type="button" class="cancel" onclick="cancelAdd()">Cancel</button>
            </div>
        </form>
    </div>

    <script src="./javascript/prog_add.js"></script>
    
</body>
</html>
