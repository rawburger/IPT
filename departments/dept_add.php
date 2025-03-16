<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

try {
    $dbconnect = new PDO("mysql:host=localhost;dbname=usjr", "root", "root");
    $dbconnect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $data = json_decode(file_get_contents("php://input"), true);

        $dept_id = $data["dept_id"];
        $dept_fullname = $data["dept_fullname"];
        $dept_shortname = $data["dept_shortname"];
        $dept_collid = $data["dept_collid"];
        
        $check_stmt = $dbconnect->prepare("SELECT deptid FROM departments WHERE deptid = :dept_id");
        $check_stmt->bindParam(':dept_id', $dept_id);
        $check_stmt->execute();

        if ($check_stmt->fetch()) {
            echo json_encode(["error" => "Department ID already exists"]);
            exit;
        }

        $sql = "INSERT INTO departments (deptid, deptfullname, deptshortname, deptcollid)
                VALUES (:dept_id, :dept_fullname, :dept_shortname, :dept_collid)";
        $stmt = $dbconnect->prepare($sql);
        $stmt->bindParam(':dept_id', $dept_id);
        $stmt->bindParam(':dept_fullname', $dept_fullname);
        $stmt->bindParam(':dept_shortname', $dept_shortname);
        $stmt->bindParam(':dept_collid', $dept_collid);

        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["error" => $stmt->errorInfo()[2]]);
        }
        exit;
    } elseif ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["action"]) && $_GET["action"] === "fetch") {
        $colleges_stmt = $dbconnect->query("SELECT collid, collfullname FROM colleges");
        $colleges = $colleges_stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(["colleges" => $colleges]);
        exit;
    }
} catch (PDOException $e) {
    echo json_encode(["error" => "Connection failed: " . $e->getMessage()]);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./design/dept_form.css">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Add Department</title>
</head>
<body>
    <div class="form_container">
        <h2>Add Department Information</h2>
        <form id="form_edit" onsubmit="submitForm(event)">
            <div class="form_group">
                <label for="dept_id">Department ID</label>
                <input type="number" id="dept_id" name="dept_id">
            </div>
            <div class="form_group">
                <label for="dept_fullname">Department Full Name</label>
                <input type="text" id="dept_fullname" name="dept_fullname">
            </div>
            <div class="form_group">
                <label for="dept_shortname">Department Short Name</label>
                <input type="text" id="dept_shortname" name="dept_shortname">
            </div>
            <div class="form_group">
                <label for="dept_collid">College Department </label>
                <select id="dept_collid" name="dept_collid">
                    <option value="">----- Select College -----</option>
                </select>
            </div>
            <div class="form_buttons">
                <button type="submit" class="save">Save</button>
                <button type="reset" class="clear">Clear Entries</button>
                <button type="button" class="cancel" onclick="cancelAdd()">Cancel</button>
            </div>
        </form>
    </div>

    <script src="./javascript/dept_add.js"></script>

</body>
</html>

