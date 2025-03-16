<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

try {
    $dbconnect = new PDO("mysql:host=localhost;dbname=usjr", "root", "root");
    $dbconnect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["action"]) && $_GET["action"] === "fetch") {
        $colleges_stmt = $dbconnect->query("SELECT collid, collfullname FROM colleges");
        $colleges = $colleges_stmt->fetchAll(PDO::FETCH_ASSOC);

        $programs_stmt = $dbconnect->query("SELECT progid, progfullname, progcollid FROM programs");
        $programs = $programs_stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(["colleges" => $colleges, "programs" => $programs]);
        exit;
    }

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $data = json_decode(file_get_contents("php://input"), true);
        $student_id = $data["student_id"];
        $first_name = $data["first_name"];
        $middle_name = $data["middle_name"];
        $last_name = $data["last_name"];
        $college = $data["college"];
        $program = $data["program"];
        $year = $data["year"];

        $sql = "SELECT COUNT(*) FROM students WHERE studid = :student_id";
        $stmt = $dbconnect->prepare($sql);
        $stmt->bindParam(':student_id', $student_id);
        $stmt->execute();
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            echo json_encode(["success" => false, "message" => "Student ID already exists, please use a different ID."]);
        } else {
            $sql = "INSERT INTO students (studid, studfirstname, studmidname, studlastname, studcollid, studprogid, studyear) 
                    VALUES (:student_id, :first_name, :middle_name, :last_name, :college, :program, :year)";
            $stmt = $dbconnect->prepare($sql);
            $stmt->bindParam(':student_id', $student_id);
            $stmt->bindParam(':first_name', $first_name);
            $stmt->bindParam(':middle_name', $middle_name);
            $stmt->bindParam(':last_name', $last_name);
            $stmt->bindParam(':college', $college);
            $stmt->bindParam(':program', $program);
            $stmt->bindParam(':year', $year);

            if ($stmt->execute()) {
                echo json_encode(["success" => true]);
            } else {
                echo json_encode(["success" => false, "message" => $stmt->errorInfo()[2]]);
            }
        }
        exit;
    }
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Connection failed: " . $e->getMessage()]);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./design/student_form.css">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Student Information Data Entry</title>
</head>
<body>
    <div class="form_container">
        <h2>Add Student Information</h2>
        <form onsubmit="submitForm(event)">
            <div class="form_group">
                <label for="student_id">Student ID</label>
                <input type="number" id="student_id" name="student_id">
            </div>
            <div class="form_group">
                <label for="first_name">First Name</label>
                <input type="text" id="first_name" name="first_name">
            </div>
            <div class="form_group">
                <label for="middle_name">Middle Name</label>
                <input type="text" id="middle_name" name="middle_name">
            </div>
            <div class="form_group">
                <label for="last_name">Last Name</label>
                <input type="text" id="last_name" name="last_name">
            </div>
            <div class="form_group">
                <label for="college">College</label>
                <select id="college" name="college">
                    <option value="">----- Select College -----</option>
                </select>
            </div>
            <div class="form_group">
                <label for="program">Program</label>
                <select id="program" name="program">
                    <option value="">----- Select Program -----</option>
                </select>
            </div>
            <div class="form_group">
                <label for="year">Year</label>
                <input type="number" id="year" name="year">
            </div>
            <div class="form_buttons">
                <button type="submit" class="save">Save</button>
                <button type="reset" class="clear">Clear Entries</button>
                <button type="button" class="cancel" onclick="cancelAdd()">Cancel</button>
            </div>
        </form>
    </div>

    <script src="./javascript/student_add.js"></script>
    
</body>
</html>
