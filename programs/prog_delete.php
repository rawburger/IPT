<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

try {
    $dbconnect = new PDO("mysql:host=localhost;dbname=usjr", "root", "root");
    $dbconnect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['progid'])) {
        $progId = $_POST['progid'];

        $deleteQuery = $dbconnect->prepare("DELETE FROM programs WHERE progid = :progid");
        $deleteQuery->execute([':progid' => $progId]);

        echo json_encode(["success" => true]);
        exit;
    } elseif ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['prog_id'])) {
        $progId = $_GET['prog_id'];

        $query = $dbconnect->prepare("
            SELECT progid, progfullname, progshortname, progcollid, progcolldeptid 
            FROM programs 
            WHERE progid = :progid
        ");
        $query->execute([':progid' => $progId]);
        $program = $query->fetch(PDO::FETCH_ASSOC);

        $collegeQuery = $dbconnect->prepare("SELECT collfullname FROM colleges WHERE collid = :collid");
        $collegeQuery->execute([':collid' => $program['progcollid']]);
        $college = $collegeQuery->fetch(PDO::FETCH_ASSOC);

        $departmentQuery = $dbconnect->prepare("SELECT deptfullname FROM departments WHERE deptid = :deptid");
        $departmentQuery->execute([':deptid' => $program['progcolldeptid']]);
        $department = $departmentQuery->fetch(PDO::FETCH_ASSOC);

        $_SESSION['program'] = $program;
        $_SESSION['college'] = $college['collfullname'];
        $_SESSION['department'] = $department['deptfullname'];
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
    <link rel="stylesheet" href="./design/prog_form.css">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Delete Program</title>
</head>
<body>
    <div class="form_container">
        <h2>Delete Program Information</h2>
        <form id="delete_form" onsubmit="confirmDelete(event)">
            <div class="form_group">
                <label for="progid">Program ID</label>
                <input type="text" id="progid" name="progid" style="background: grey;" value="<?php echo $_SESSION['program']['progid']; ?>" readonly>
            </div>
            <div class="form_group">
                <label for="progfullname">Program Full Name</label>
                <input type="text" id="progfullname" name="progfullname" style="background: grey;" value="<?php echo $_SESSION['program']['progfullname']; ?>" readonly>
            </div>
            <div class="form_group">
                <label for="progshortname">Program Short Name</label>
                <input type="text" id="progshortname" name="progshortname" style="background: grey;" value="<?php echo $_SESSION['program']['progshortname']; ?>" readonly>
            </div>
            <div class="form_group">
                <label for="progcollname">Program College</label>
                <input type="text" id="progcollname" name="progcollname" style="background: grey;" value="<?php echo $_SESSION['college']; ?>" readonly>
            </div>
            <div class="form_group">
                <label for="progdeptname">Program Department</label>
                <input type="text" id="progdeptname" name="progdeptname" style="background: grey;" value="<?php echo $_SESSION['department']; ?>" readonly>
            </div>
            
            <div class="form_buttons">
                <button type="submit" class="delete">Delete</button>
                <button type="button" class="cancel" onclick="cancelDelete()">Cancel</button>
            </div>
        </form>
    </div>

    <script src="./javascript/prog_delete.js"></script>

</body>
</html>

