<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
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
        $query = $dbconnect->prepare("SELECT collid, collfullname, collshortname FROM colleges");
        $query->execute();
        $colleges = $query->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(["success" => true, "data" => $colleges]);
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="./design/college_dashboard.css">
    <title>College Dashboard</title>
</head>
<body>
<div class="header">
    <div class="left">
        <button type="button" id="back" onclick="backtoLanding()"><i class="bi bi-arrow-left-square-fill"></i> Back</button>
        <button type="button" id="add" onclick="addNewCollege()"><i class="bi bi-plus-circle-fill"></i> Add New College</button>
    </div>
    <div class="right">
        <p>You are logged in as: <span id="username"><?php echo $_SESSION['username']; ?></span></p>
        <form action="college_dashboard.php" method="post">
            <button type="submit" id="logout" name="action" value="logout">Logout</button>
        </form>
    </div>
</div>
<div class="container">
    <table>
        <thead>
            <tr>
                <th>College ID</th>
                <th>College Full Name</th>
                <th>College Short Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="college-data">
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="./javascript/college_dashboard.js"></script>

</body>
</html>
