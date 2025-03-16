<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    $un = htmlspecialchars($data["username"]);
    $pass = $data["password"];
    $ver = $data["verify"];

    if (empty($un) || empty($pass) || empty($ver)) {
        echo json_encode(["success" => false, "message" => "All fields are required."]);
        exit;
    }

    if ($pass === $ver) {
        try {
            $dbconnect = new PDO("mysql:host=localhost;dbname=usjr", "root", "root");
            $dbconnect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = "SELECT COUNT(*) FROM appusers WHERE username = :username";
            $stmt = $dbconnect->prepare($sql);
            $stmt->bindParam(':username', $un);
            $stmt->execute();
            $count = $stmt->fetchColumn();

            if ($count > 0) {
                echo json_encode(["success" => false, "message" => "Username is already taken, please choose a different one."]);
            } else {
                $hashed_pass = password_hash($pass, PASSWORD_BCRYPT);

                $sql = "INSERT INTO appusers (username, password) VALUES (:username, :password)";
                $stmt = $dbconnect->prepare($sql);
                $stmt->bindParam(':username', $un);
                $stmt->bindParam(':password', $hashed_pass);

                if ($stmt->execute()) {
                    echo json_encode(["success" => true]);
                } else {
                    echo json_encode(["success" => false, "message" => $stmt->errorInfo()[2]]);
                }
            }
        } catch (PDOException $e) {
            echo json_encode(["success" => false, "message" => $e->getMessage()]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "The password does not match the verify password."]);
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
    <title>Register</title>
    <link rel="stylesheet" href="./loginPageFiles/register.css">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="container">
        <form id="register-form" method="POST" onsubmit="addUser(event);">
            <h1>Add New User</h1>
            <label>Username:</label>
            <input type="text" id="username" name="username">
            <label>Password:</label>
            <input type="password" id="password" name="password">
            <label>Verify Password:</label>
            <input type="password" id="verify" name="verify">
            <button type="submit">Submit</button>
        </form>
        <div class="loginLabel">
        <label for="loginLabel">Have an account already? <a href="login.php"> Login here!</a></label>
        </div>
    </div>

    <script src="./loginPageFiles/register.js"></script>

</body>
</html>

