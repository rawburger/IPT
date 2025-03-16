<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

try {
    $dbconnect = new PDO("mysql:host=localhost;dbname=usjr", "root", "root");
    $dbconnect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["college_id"])) {
        $college_id = $_GET["college_id"];
        $stmt = $dbconnect->prepare("SELECT collid, collfullname, collshortname FROM colleges WHERE collid = :college_id");
        $stmt->bindParam(':college_id', $college_id);
        $stmt->execute();
        $college = $stmt->fetch(PDO::FETCH_ASSOC);

        $_SESSION['college'] = $college;
    }

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $data = json_decode(file_get_contents("php://input"), true);
        $college_id = $data["college_id"];

        $sql = "DELETE FROM colleges WHERE collid = :college_id";
        $stmt = $dbconnect->prepare($sql);
        $stmt->bindParam(':college_id', $college_id);

        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "message" => $stmt->errorInfo()[2]]);
        }
        exit;
    }
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Connection failed: " . $e->getMessage()]);
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
    <title>Delete College</title>
</head>
<body>
    <div class="form_container">
        <h2>Delete College Information</h2>
        <form id="delete_form" method="POST" action="college_delete.php" onsubmit="confirmDelete(event)">
            <div class="form_group">
                <label for="college_id">College ID</label>
                <input type="text" id="college_id" name="college_id" style="background: grey;" value="<?php echo $_SESSION['college']['collid']; ?>" readonly>
            </div>
            <div class="form_group">
                <label for="college_fullname">College Full Name</label>
                <input type="text" id="college_fullname" name="college_fullname" style="background: grey;" value="<?php echo $_SESSION['college']['collfullname']; ?>" readonly>
            </div>
            <div class="form_group">
                <label for="college_shortname">College Short Name</label>
                <input type="text" id="college_shortname" name="college_shortname" style="background: grey;" value="<?php echo $_SESSION['college']['collshortname']; ?>" readonly>
            </div>
            <div class="form_buttons">
                <button type="submit" class="delete">Delete</button>
                <button type="button" class="cancel" onclick="cancelDelete()">Cancel</button>
            </div>
        </form>
    </div>

    <script src="./javascript/college_delete.js"></script>

</body>
</html>

