<?php
session_start();

if(!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'logout'){
    unset($_SESSION);
    session_destroy();
    header("Location: ../login.php");
    exit;
}

try {
    $dbconnect = new PDO("mysql:host=localhost;dbname=usjr", "root", "root");
    $dbconnect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["action"]) && $_GET["action"] === "fetch") {
        $sql = "SELECT a.studid AS ID, a.studlastname AS LastName, a.studfirstname AS FirstName, a.studmidname AS MiddleName,
        e.collfullname AS College, d.progfullname AS Program, a.studyear AS Year FROM usjr.students a
        JOIN usjr.colleges e ON a.studcollid = e.collid JOIN usjr.programs d ON a.studprogid = d.progid";
                
        $stmt = $dbconnect->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($result);
        exit;
    }

} catch(PDOException $e) {
    echo json_encode(["error" => "Connection failed: " . $e->getMessage()]);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./design/student_dashboard.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <title>Student Dashboard</title>
</head>
<body>
    <div class="header">
        <div class="left">
            <button type="button" id="back" onclick="backtoLanding()"><i class="bi bi-arrow-left-square-fill"></i> Back</button>
            <button type="button" id="add" onclick="addNewStudent()"><i class="bi bi-plus-circle-fill"></i> Add New Student</button>
        </div>
        <div class="right">
            <p>You are logged in as: <span id="username"><?php echo $_SESSION['username']; ?></span></p>
            <form action="student_dashboard.php" method="post">
                <button type="submit" id="logout" name="action" value="logout">Logout</button>
            </form>
        </div>
    </div>
    <div class="container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Last Name</th>
                    <th>First Name</th>
                    <th>Middle Name</th>
                    <th>College Name</th>
                    <th>Program Name</th>
                    <th>Year</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table>
    </div>

    <script src="./javascript/student_dashboard.js"></script>

</body>
</html>

