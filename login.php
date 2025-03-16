<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    $username = $data["username"] ?? null;
    $password = $data["password"] ?? null;

    try {
        $dbconnect = new PDO("mysql:host=localhost;dbname=usjr", "root", "root");
        $dbconnect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "SELECT username, password FROM appusers WHERE username = :username";
        $stmt = $dbconnect->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['username'] = $user['username'];
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "message" => "Invalid username or password!"]);
        }
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Connection failed: " . $e->getMessage()]);
    }
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./loginPageFiles/login.css">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Login</title>
</head>
<body>
    <div class="login_container">
        <div class="login_header">- LOGIN -</div>
        <form class="login_form" method="POST" onsubmit="loginUser(event);">
            <div class="form_group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username">
            </div>
            <div class="form_group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password">
            </div>
            <div class="button_container">
                <button type="submit" class="login_button">Login</button>
                <button type="reset" class="clear_button">Clear</button>
            </div>
        </form>
        <div class="registerLabel">
        <label for="registerLabel">Don't have an account yet? <a href="register.php"> Register here!</a></label>
        </div>
    </div>

    <script src="./loginPageFiles/login.js"></script>

</body>
</html>
