<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

try {
    $dbconnect = new PDO("mysql:host=localhost;dbname=usjr", "root", "root");
    $dbconnect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["college_id"]) && !isset($_GET["action"])) {
        $college_id = $_GET["college_id"];
        $stmt = $dbconnect->prepare("SELECT collid, collfullname, collshortname FROM colleges WHERE collid = :college_id");
        $stmt->bindParam(':college_id', $college_id);
        $stmt->execute();
        $college = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($college) {
            $_SESSION['college'] = $college;
        } else {
            $_SESSION['error'] = "College not found.";
        }
    }

    if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["action"]) && $_GET["action"] === "fetch" && isset($_GET["college_id"])) {
        $college_id = $_GET["college_id"];
        $stmt = $dbconnect->prepare("SELECT collid, collfullname, collshortname FROM colleges WHERE collid = :college_id");
        $stmt->bindParam(':college_id', $college_id);
        $stmt->execute();
        $college = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($college) {
            echo json_encode(['college' => $college]);
        } else {
            echo json_encode(['error' => 'College not found']);
        }
        exit;
    }

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $college_id = $_POST["college_id"];
        $college_fullname = $_POST["college_fullname"];
        $college_shortname = $_POST["college_shortname"];

        error_log("Received Data: " . print_r($_POST, true));

        $sql = "UPDATE colleges SET collfullname = :college_fullname, collshortname = :college_shortname WHERE collid = :college_id";
        $stmt = $dbconnect->prepare($sql);
        $stmt->bindParam(':college_id', $college_id);
        $stmt->bindParam(':college_fullname', $college_fullname);
        $stmt->bindParam(':college_shortname', $college_shortname);

        if ($stmt->execute()) {
            if ($stmt->rowCount() > 0) {
                header("Location: college_dashboard.php");
                exit;
            } else {
                $_SESSION['error'] = "No changes were made.";
                header("Location: college_edit.php?college_id=$college_id");
                exit;
            }
        } else {
            error_log("SQL Error: " . print_r($stmt->errorInfo(), true));
            echo "Error: " . $stmt->errorInfo()[2];
        }
    }
} catch (PDOException $e) {
    error_log("Connection failed: " . $e->getMessage());
    echo "Connection failed: " . $e->getMessage();
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
    <title>Edit College Information</title>
</head>
<body>
    <div class="form_container">
        <h2>Edit College Information</h2>
        <form id="edit_form" onsubmit="submitForm(event)">
            <div class="form_group">
                <label for="college_id">College ID</label>
                <input type="text" id="college_id" name="college_id" style="background: grey;" value="<?php echo isset($_SESSION['college']['collid']) ? htmlspecialchars($_SESSION['college']['collid'], ENT_QUOTES, 'UTF-8') : ''; ?>" readonly>
            </div>
            <div class="form_group">
                <label for="college_fullname">College Full Name</label>
                <input type="text" id="college_fullname" name="college_fullname" value="<?php echo isset($_SESSION['college']['collfullname']) ? htmlspecialchars($_SESSION['college']['collfullname'], ENT_QUOTES, 'UTF-8') : ''; ?>">
            </div>
            <div class="form_group">
                <label for="college_shortname">College Short Name</label>
                <input type="text" id="college_shortname" name="college_shortname" value="<?php echo isset($_SESSION['college']['collshortname']) ? htmlspecialchars($_SESSION['college']['collshortname'], ENT_QUOTES, 'UTF-8') : ''; ?>">
            </div>
            <div class="form_buttons">
                <button type="submit" class="save">Save</button>
                <button type="button" class="cancel" onclick="cancelEdit()">Cancel</button>
            </div>
        </form>
    </div>

    <script src="./javascript/college_edit.js"></script>
    
</body>
</html>
