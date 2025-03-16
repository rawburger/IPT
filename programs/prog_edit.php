<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

try {
    $dbconnect = new PDO("mysql:host=localhost;dbname=usjr", "root", "root");
    $dbconnect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["prog_id"])) {
        $prog_id = $_GET["prog_id"];
        
        $prog_stmt = $dbconnect->prepare("SELECT * FROM programs WHERE progid = :prog_id");
        $prog_stmt->bindParam(':prog_id', $prog_id);
        $prog_stmt->execute();
        $program = $prog_stmt->fetch(PDO::FETCH_ASSOC);

        if (!$program) {
            echo json_encode(["error" => "Program not found."]);
            exit;
        }

        $colleges_stmt = $dbconnect->query("SELECT collid, collfullname FROM colleges");
        $colleges = $colleges_stmt->fetchAll(PDO::FETCH_ASSOC);

        $departments_stmt = $dbconnect->query("SELECT deptid, deptfullname, deptcollid FROM departments");
        $departments = $departments_stmt->fetchAll(PDO::FETCH_ASSOC);

        $_SESSION['program'] = $program;
        $_SESSION['colleges'] = $colleges;
        $_SESSION['departments'] = $departments;
    }

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $data = json_decode(file_get_contents("php://input"), true);

        $prog_id = $data["prog_id"];
        $prog_name = $data["prog_name"];
        $prog_short = $data["prog_short"];
        $prog_collid = $data["prog_collid"];
        $prog_deptid = $data["prog_deptid"];

        $prog_check_stmt = $dbconnect->prepare("SELECT COUNT(*) FROM programs WHERE progid = :prog_id");
        $prog_check_stmt->bindParam(':prog_id', $prog_id);
        $prog_check_stmt->execute();
        $count = $prog_check_stmt->fetchColumn();

        if ($count == 0) {
            echo json_encode(["success" => false, "message" => "Program ID does not exist."]);
            exit;
        }

        $sql = "UPDATE programs SET progfullname = :prog_name, progshortname = :prog_short, progcollid = :prog_collid, progcolldeptid = :prog_deptid WHERE progid = :prog_id";
        $stmt = $dbconnect->prepare($sql);
        $stmt->bindParam(':prog_id', $prog_id);
        $stmt->bindParam(':prog_name', $prog_name);
        $stmt->bindParam(':prog_short', $prog_short);
        $stmt->bindParam(':prog_collid', $prog_collid);
        $stmt->bindParam(':prog_deptid', $prog_deptid);

        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "message" => $stmt->errorInfo()[2]]);
        }
        exit;
    }
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Connection failed: " . $e->getMessage()]);
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
    <title>Edit Program</title>
</head>
<body>
    <div class="form_container">
        <h2>Edit Program Information</h2>
        <form id="edit_form" onsubmit="submitForm(event)">
            <div class="form_group">
                <label for="prog_id">Program ID</label>
                <input type="text" id="prog_id" name="prog_id" style="background: grey;" readonly value="<?php echo $_SESSION['program']['progid']; ?>">
            </div>
            <div class="form_group">
                <label for="prog_name">Program Full Name</label>
                <input type="text" id="prog_name" name="prog_name" value="<?php echo $_SESSION['program']['progfullname']; ?>">
            </div>
            <div class="form_group">
                <label for="prog_short">Program Short Name</label>
                <input type="text" id="prog_short" name="prog_short" value="<?php echo $_SESSION['program']['progshortname']; ?>">
            </div>
            <div class="form_group">
                <label for="prog_collid">College</label>
                <select id="prog_collid" name="prog_collid">
                    <option value="">----- Select College -----</option>
                    <?php foreach ($_SESSION['colleges'] as $college): ?>
                        <option value="<?php echo $college['collid']; ?>" <?php if ($college['collid'] == $_SESSION['program']['progcollid']) echo 'selected'; ?>><?php echo $college['collfullname']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form_group">
                <label for="prog_deptid">Department</label>
                <select id="prog_deptid" name="prog_deptid">
                    <option value="">----- Select Department -----</option>
                    <?php foreach ($_SESSION['departments'] as $department): ?>
                        <?php if ($department['deptcollid'] == $_SESSION['program']['progcollid']): ?>
                            <option value="<?php echo $department['deptid']; ?>" <?php if ($department['deptid'] == $_SESSION['program']['progcolldeptid']) echo 'selected'; ?>><?php echo $department['deptfullname']; ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form_buttons">
                <button type="submit" class="save">Save</button>
                <button type="button" class="cancel" onclick="cancelEdit()">Cancel</button>
            </div>
        </form>
    </div>

    <script src="./javascript/prog_edit.js"></script>

</body>
</html>
