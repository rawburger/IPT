<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

try {
    $dbconnect = new PDO("mysql:host=localhost;dbname=usjr", "root", "root");
    $dbconnect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $studid = $_POST['studid'];
        $studfirstname = $_POST['studfirstname'];
        $studlastname = $_POST['studlastname'];
        $studmidname = $_POST['studmidname'];
        $studprogid = $_POST['studprogid'];
        $studcollid = $_POST['studcollid'];
        $studyear = $_POST['studyear'];

        $sql = "UPDATE usjr.students SET 
                studfirstname = :studfirstname, 
                studlastname = :studlastname,
                studmidname = :studmidname, 
                studprogid = :studprogid, 
                studcollid = :studcollid, 
                studyear = :studyear 
                WHERE studid = :studid";

        $stmt = $dbconnect->prepare($sql);
        $stmt->execute([
            ':studfirstname' => $studfirstname,
            ':studlastname' => $studlastname,
            ':studmidname' => $studmidname,
            ':studprogid' => $studprogid,
            ':studcollid' => $studcollid,
            ':studyear' => $studyear,
            ':studid' => $studid
        ]);

        header("Location: student_dashboard.php");
        exit;
    } elseif ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['action']) && $_GET['action'] === 'fetch' && isset($_GET['student_id'])) {
        $studid = $_GET['student_id'];
        
        $sql = "SELECT * FROM usjr.students WHERE studid = :studid";
        $stmt = $dbconnect->prepare($sql);
        $stmt->execute([':studid' => $studid]);
        $student = $stmt->fetch(PDO::FETCH_ASSOC);

        $colleges_stmt = $dbconnect->query("SELECT collid, collfullname FROM colleges");
        $colleges = $colleges_stmt->fetchAll(PDO::FETCH_ASSOC);

        $programs_stmt = $dbconnect->query("SELECT progid, progfullname, progcollid FROM programs");
        $programs = $programs_stmt->fetchAll(PDO::FETCH_ASSOC);

        $_SESSION['student'] = $student;
        echo json_encode(['student' => $student, 'colleges' => $colleges, 'programs' => $programs]);
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
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./design/student_form.css">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Edit Student</title>
</head>
<body>
    <div class="form_container">
        <h2>Edit Student Information</h2>
        <form id="form_edit">
            <div class="form_group">
                <label for="studid">Student ID</label>
                <input type="text" id="studid" name="studid" style="background: grey" value="<?php echo $_SESSION['student']['studid']; ?>" readonly>
            </div>
            <div class="form_group">
                <label for="studfirstname">First Name</label>
                <input type="text" id="studfirstname" name="studfirstname" value="<?php echo $_SESSION['student']['studfirstname']; ?>">
            </div>
            <div class="form_group">
                <label for="studlastname">Last Name</label>
                <input type="text" id="studlastname" name="studlastname" value="<?php echo $_SESSION['student']['studlastname']; ?>">
            </div>
            <div class="form_group">
                <label for="studmidname">Middle Name</label>
                <input type="text" id="studmidname" name="studmidname" value="<?php echo $_SESSION['student']['studmidname']; ?>">
            </div>
            <div class="form_group">
                <label for="studcollid">College</label>
                <select id="studcollid" name="studcollid">
                    <option value="">----- Select College -----</option>
                    <?php foreach ($colleges as $college): ?>
                        <option value="<?php echo $college['collid']; ?>" <?php echo $_SESSION['student']['studcollid'] && $college['collid'] == $_SESSION['student']['studcollid'] ? 'selected' : ''; ?>>
                            <?php echo $college['collfullname'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form_group">
                <label for="studprogid">Program</label>
                <select id="studprogid" name="studprogid">
                    <option value="">----- Select Program -----</option>
                    <?php foreach ($programs as $program): ?>
                        <?php if (isset($_SESSION['student']['studcollid']) && $program['progcollid'] == $_SESSION['student']['studcollid']): ?>
                        <option value="<?php echo $program['progid']; ?>" <?php echo $_SESSION['student']['studprogid'] && $program['progid'] == $_SESSION['student']['studprogid'] ? 'selected' : ''; ?>>
                            <?php echo $program['progfullname']; ?>
                        </option>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form_group">
                <label for="studyear">Year</label>
                <input type="text" id="studyear" name="studyear" value="<?php echo $_SESSION['student']['studyear']; ?>" >
            </div>
            <div class="form_buttons">
                <button type="button" class="save" onclick="updateStudent()">Save</button>
                <button type="button" class="cancel" onclick="cancelEdit()">Cancel</button>
            </div>
            <input type="hidden" id="program_data" value='<?php echo json_encode($programs); ?>'>
        </form>
    </div>

    <script src="./javascript/student_edit.js"></script>

</body>
</html>
