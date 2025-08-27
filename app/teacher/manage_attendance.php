<?php
$page_title = 'Manage Attendance';
require_once __DIR__ . '/../../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    redirect(SITE_URL . '/public/index.php');
}

require_once __DIR__ . '/../../app/core/db.php';
require_once __DIR__ . '/../../includes/sidebar_teacher.php';

$db = new Database();
$conn = $db->getConnection();

// Get teacher ID from session
$teacher_id_result = $conn->query("SELECT id FROM teachers WHERE user_id = {$_SESSION['user_id']}");
$teacher = $teacher_id_result->fetch_assoc();
$teacher_id = $teacher['id'];

// Handle form submission for saving attendance
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_attendance'])) {
    $class_id = $_POST['class_id'];
    $attendance_date = $_POST['attendance_date'];
    $attendance_data = $_POST['attendance'];

    $stmt = $conn->prepare("INSERT INTO attendance (student_id, class_id, teacher_id, status, attendance_date) VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE status = VALUES(status)");

    foreach ($attendance_data as $student_id => $status) {
        $stmt->bind_param("iiiss", $student_id, $class_id, $teacher_id, $status, $attendance_date);
        $stmt->execute();
    }

    $success_message = "Attendance saved successfully!";
}

// Fetch classes taught by the teacher (for now, all classes)
$classes_result = $conn->query("SELECT * FROM classes ORDER BY class_name");

$students = [];
$attendance_records = [];
if (isset($_GET['class_id']) && isset($_GET['attendance_date'])) {
    $class_id = $_GET['class_id'];
    $attendance_date = $_GET['attendance_date'];

    // Fetch students
    $stmt_students = $conn->prepare("SELECT * FROM students WHERE class_id = ? ORDER BY full_name");
    $stmt_students->bind_param("i", $class_id);
    $stmt_students->execute();
    $students_result = $stmt_students->get_result();
    while ($row = $students_result->fetch_assoc()) {
        $students[] = $row;
    }

    // Fetch existing attendance
    $stmt_attendance = $conn->prepare("SELECT student_id, status FROM attendance WHERE class_id = ? AND attendance_date = ?");
    $stmt_attendance->bind_param("is", $class_id, $attendance_date);
    $stmt_attendance->execute();
    $attendance_result = $stmt_attendance->get_result();
    while ($row = $attendance_result->fetch_assoc()) {
        $attendance_records[$row['student_id']] = $row['status'];
    }
}

?>

<div class="main-content">
    <h1 class="h3 mb-4 text-gray-800">Manage Attendance</h1>

    <?php if (isset($success_message)): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Select Class and Date</h6>
        </div>
        <div class="card-body">
            <form action="" method="get">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="class_id">Class</label>
                        <select class="form-control" id="class_id" name="class_id" required>
                            <option value="">Select Class</option>
                            <?php mysqli_data_seek($classes_result, 0); ?>
                            <?php while($class = $classes_result->fetch_assoc()): ?>
                                <option value="<?php echo $class['id']; ?>" <?php if(isset($_GET['class_id']) && $_GET['class_id'] == $class['id']) echo 'selected'; ?>><?php echo escape($class['class_name']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="attendance_date">Date</label>
                        <input type="date" class="form-control" id="attendance_date" name="attendance_date" value="<?php echo $_GET['attendance_date'] ?? date('Y-m-d'); ?>" required>
                    </div>
                    <div class="form-group col-md-2">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary btn-block">View Students</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php if (!empty($students)): ?>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Take Attendance for <?php echo escape($_GET['attendance_date']); ?></h6>
        </div>
        <div class="card-body">
            <form action="" method="post">
                <input type="hidden" name="class_id" value="<?php echo $_GET['class_id']; ?>">
                <input type="hidden" name="attendance_date" value="<?php echo $_GET['attendance_date']; ?>">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Roll Number</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $student): 
                            $status = $attendance_records[$student['id']] ?? 'Present';
                        ?>
                            <tr>
                                <td><?php echo escape($student['full_name']); ?></td>
                                <td><?php echo escape($student['roll_number']); ?></td>
                                <td>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="attendance[<?php echo $student['id']; ?>]" id="present_<?php echo $student['id']; ?>" value="Present" <?php if($status == 'Present') echo 'checked'; ?>>
                                        <label class="form-check-label" for="present_<?php echo $student['id']; ?>">Present</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="attendance[<?php echo $student['id']; ?>]" id="absent_<?php echo $student['id']; ?>" value="Absent" <?php if($status == 'Absent') echo 'checked'; ?>>
                                        <label class="form-check-label" for="absent_<?php echo $student['id']; ?>">Absent</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="attendance[<?php echo $student['id']; ?>]" id="late_<?php echo $student['id']; ?>" value="Late" <?php if($status == 'Late') echo 'checked'; ?>>
                                        <label class="form-check-label" for="late_<?php echo $student['id']; ?>">Late</label>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <button type="submit" name="save_attendance" class="btn btn-primary">Save Attendance</button>
            </form>
        </div>
    </div>
    <?php endif; ?>

</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
