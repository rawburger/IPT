<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        $dbconnect = new PDO("mysql:host=localhost;dbname=usjr", "root", "root");
        $dbconnect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $data = json_decode(file_get_contents("php://input"), true);
        $college_id = $data["coll_id"];
        $college_fullname = $data["coll_name"];
        $college_shortname = $data["coll_short"];

        $sql = "SELECT COUNT(*) FROM colleges WHERE collid = :college_id";
        $stmt = $dbconnect->prepare($sql);
        $stmt->bindParam(':college_id', $college_id);
        $stmt->execute();
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            echo json_encode(["success" => false, "message" => "College ID already exists, please use a different ID."]);
        } else {
            $sql = "INSERT INTO colleges (collid, collfullname, collshortname) VALUES (:college_id, :college_fullname, :college_shortname)";
            $stmt = $dbconnect->prepare($sql);
            $stmt->bindParam(':college_id', $college_id);
            $stmt->bindParam(':college_fullname', $college_fullname);
            $stmt->bindParam(':college_shortname', $college_shortname);

            if ($stmt->execute()) {
                echo json_encode(["success" => true]);
            } else {
                echo json_encode(["success" => false, "message" => $stmt->errorInfo()[2]]);
            }
        }
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
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
    <link rel="stylesheet" href="./design/college_form.css">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Add New College</title>
</head>
<body>
    <div class="form_container">
        <h2>Add New College</h2>
        <form onsubmit="submitForm(event);">
            <div class="form_group">
                <label for="coll_id">College ID</label>
                <input type="number" id="coll_id" name="coll_id">
            </div>
            <div class="form_group">
                <label for="coll_name">College Full Name</label>
                <input type="text" id="coll_name" name="coll_name">
            </div>
            <div class="form_group">
                <label for="coll_shortname">College Short Name</label>
                <input type="text" id="coll_shortname" name="coll_short">
            </div>
            <div class="form_buttons">
                <button type="submit" class="save">Save</button>
                <button type="reset" class="clear">Clear</button>
                <button type="button" class="cancel" onclick="cancelAdd()">Cancel</button>
            </div>
        </form>
    </div>

    <script src="./javascript/college_add.js"></script>
    
</body>
</html>



