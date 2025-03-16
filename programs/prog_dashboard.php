<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}

try {
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'logout'){
        unset($_SESSION);
        session_destroy();
        header("Location: ../login.php");
        exit;
    }

    $dbconnect = new PDO("mysql:host=localhost;dbname=usjr", "root", "root");
    $dbconnect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['action']) && $_GET['action'] === 'fetch') {
        $query = $dbconnect->prepare("
            SELECT 
                p.progid, 
                p.progfullname, 
                p.progshortname, 
                c.collfullname AS college_name, 
                d.deptfullname AS dept_name 
            FROM programs p
            JOIN colleges c ON p.progcollid = c.collid
            JOIN departments d ON p.progcolldeptid = d.deptid
        ");
        $query->execute();
        $programs = $query->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(["success" => true, "data" => $programs]);
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
    <link rel="stylesheet" href="./design/prog_dashboard.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <title>Program Dashboard Information</title>
</head>
<body>
    <div class="header">
        <div class="left">
            <button type="button" id="back" onclick="backtoLanding()"><i class="bi bi-arrow-left-square-fill"></i> Back</button>
            <button type="button" id="add" onclick="addNewProgram()"><i class="bi bi-plus-circle-fill"></i> Add New Program</button>
        </div>
        <div class="right">
            <p>You are logged in as: <span id="username"><?php echo $_SESSION['username']; ?></span></p>
            <form action="prog_dashboard.php" method="post">
                <button type="submit" id="logout" name="action" value="logout">Logout</button>
            </form>
        </div>
    </div>
    <div class="container">
        <table>
            <thead>
                <tr>
                    <th>Program ID</th>
                    <th>Program Full Name</th>
                    <th>Program Short Name</th>
                    <th>Program College</th>
                    <th>Program Department</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="program-data">
            </tbody>
        </table>
    </div>

    <script src="./javascript/prog_dashboard.js"></script>

</body>
</html>
