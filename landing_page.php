<?php
session_start();

if(!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'logout'){
    unset($_SESSION);
    session_destroy();
    header("Location: ./login.php");
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
    <link rel="stylesheet" href="landingPageFiles/landing.css">
    <title>Overview</title>
</head>
<body>
    <div class="container">
        <div class="header"> 
            <div class="left">
                
                <h1><img src="./icons/usjr_logo.png"> University of San Jose-Recoletos</h1>
            </div>
            <div class="right">
            <form action="landing_page.php" method="post">
                <button type="submit" id="logout" name="action" value="logout">Logout</button>
            </form>
            </div>
        </div>
        <div class="button-box">
            <div class="stud-box">
                <button type="button" class="btn-box" onclick="gotoStuds()"><i class="bi bi-person-circle"></i> Students</button>
            </div>
            <div class="coll_box">
                <button type="button" class="btn-box" onclick="gotoColls()"><i class="bi bi-journal-bookmark-fill"></i> Colleges</button>
            </div>
            <div class="prog_box">
                <button type="button" class="btn-box" onclick="gotoProgs()"><i class="bi bi-app-indicator"></i> Programs</button>
            </div>
            <div class="dept_box">
                <button type="button" class="btn-box" onclick="gotoDepts()"><i class="bi bi-building"></i> Departments</button>
            </div>
        </div>
    </div>
    <div class="message">
        <h2>Welcome, <span id="username"><?php echo $_SESSION['username']; ?></span>!</h2>
    </div>


<script src="./landingPageFiles/landing_page.js"></script>

</body>
</html>