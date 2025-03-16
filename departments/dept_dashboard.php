<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

 if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'logout'){
    unset($_SESSION);
    session_destroy();
    header("Location: ../login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["action"]) && $_GET["action"] === "fetch") {
    try {

        $dbconnect = new PDO("mysql:host=localhost;dbname=usjr", "root", "root");
        $dbconnect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = $dbconnect->prepare("
            SELECT departments.deptid, departments.deptfullname, departments.deptshortname, colleges.collfullname
            FROM departments
            INNER JOIN colleges ON departments.deptcollid = colleges.collid
        ");
        $query->execute();
        $departments = $query->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(["success" => true, "departments" => $departments]);
        exit;
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./design/dept_dashboard.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
     <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <title>Department Dashboard</title>
</head>
<body data-username="<?php echo $_SESSION['username']; ?>">
    <div class="header">
        <div class="left">
            <button type="button" id="back" onclick="backtoLanding()"><i class="bi bi-arrow-left-square-fill"></i> Back</button>
            <button type="button" id="add" onclick="addNewDept()"><i class="bi bi-plus-circle-fill"></i> Add New Department</button>
        </div>
        <div class="right">
            <p>You are logged in as: <span id="username"></span></p>
            <form action="dept_dashboard.php" method="post">
                <button type="submit" id="logout" name="action" value="logout">Logout</button>
            </form>
        </div>
    </div>
    <div class="container">
        <table>
            <thead>
                <tr>
                    <th>Department ID</th>
                    <th>Department Full Name</th>
                    <th>Department Short Name</th>
                    <th>College Department </th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="departments-tbody">
            </tbody>
        </table>
    </div>

    <script src="./javascript/dept_dashboard.js"></script>
    
</body>
</html>
