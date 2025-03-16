<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

try {
    $dbconnect = new PDO("mysql:host=localhost;dbname=usjr", "root", "root");
    $dbconnect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["deptid"])) {
        $dept_id = $_GET["deptid"];
        $stmt = $dbconnect->prepare("SELECT d.deptid, d.deptfullname, d.deptshortname, c.collfullname AS college_name FROM departments d JOIN colleges c ON d.deptcollid = c.collid WHERE d.deptid = :deptid");
        $stmt->bindParam(':deptid', $dept_id);
        $stmt->execute();
        $department = $stmt->fetch(PDO::FETCH_ASSOC);

        $_SESSION['department'] = $department;
    }

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $data = json_decode(file_get_contents("php://input"), true);
        $dept_id = $data["deptid"];

        $sql = "DELETE FROM departments WHERE deptid = :deptid";
        $stmt = $dbconnect->prepare($sql);
        $stmt->bindParam(':deptid', $dept_id);

        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "message" => $stmt->errorInfo()[2]]);
        }
        exit;
    }
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
    exit;
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
    <title>Delete Department</title>
</head>
<body>
    <div class="form_container">
        <h2>Delete Department Information</h2>
        <form id="delete_form" onsubmit="confirmDelete()">
            <div class="form_group">
                <label for="deptid">Department ID</label>
                <input type="text" id="deptid" name="deptid" style="background: grey;" value="<?php echo $_SESSION['department']['deptid']; ?>" readonly>
            </div>
            <div class="form_group">
                <label for="deptfullname">Department Full Name</label>
                <input type="text" id="deptfullname" name="deptfullname" style="background: grey;" value="<?php echo $_SESSION['department']['deptfullname']; ?>" readonly>
            </div>
            <div class="form_group">
                <label for="deptshortname">Department Short Name</label>
                <input type="text" id="deptshortname" name="deptshortname" style="background: grey;" value="<?php echo $_SESSION['department']['deptshortname']; ?>" readonly>
            </div>
            <div class="form_group">
                <label for="deptcollname"> College Department </label>
                <input type="text" id="deptcollname" name="deptcollname" style="background: grey;" value="<?php echo $_SESSION['department']['college_name']; ?>" readonly>
            </div>
            <div class="form_buttons">
                <button type="submit" class="delete">Delete</button>
                <button type="button" class="cancel" onclick="cancelDelete()">Cancel</button>
            </div>
        </form>
    </div>

    <script src="./javascript/dept_delete.js"></script>

</body>
</html>

