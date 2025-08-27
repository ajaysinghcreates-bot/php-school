<?php
$page_title = 'Manage Grades';
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

// Handle form submission for saving grades
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_grades'])) {
    $class_id = $_POST['class_id'];
    $subject_id = $_POST['subject_id'];
    $grades_data = $_POST['grades'];
    $exam_date = $_POST['exam_date'];

    $stmt = $conn->prepare("INSERT INTO grades (student_id, class_id, subject_id, teacher_id, marks, exam_date) VALUES (?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE marks = VALUES(marks), exam_date = VALUES(exam_date)");

    foreach ($grades_data as $student_id => $marks) {
        if (!empty($marks)) {
            $stmt->bind_param("iiiids", $student_id, $class_id, $subject_id, $teacher_id, $marks, $exam_date);
            $stmt->execute();
        }
    }

    $success_message = "Grades saved successfully!";
}

// Fetch classes taught by the teacher (for now, all classes)
$classes_result = $conn->query("SELECT * FROM classes ORDER BY class_name");

$subjects = [];
if (isset($_GET['class_id'])) {
    $class_id = $_GET['class_id'];
    $stmt_subjects = $conn->prepare("SELECT * FROM subjects WHERE class_id = ? ORDER BY subject_name");
    $stmt_subjects->bind_param("i", $class_id);
    $stmt_subjects->execute();
    $subjects_result = $stmt_subjects->get_result();
    while ($row = $subjects_result->fetch_assoc()) {
        $subjects[] = $row;
    }
}

$students = [];
$grades_records = [];
if (isset($_GET['class_id']) && isset($_GET['subject_id'])) {
    $class_id = $_GET['class_id'];
    $subject_id = $_GET['subject_id'];

    // Fetch students
    $stmt_students = $conn->prepare("SELECT * FROM students WHERE class_id = ? ORDER BY full_name");
    $stmt_students->bind_param("i", $class_id);
    $stmt_students->execute();
    $students_result = $stmt_students->get_result();
    while ($row = $students_result->fetch_assoc()) {
        $students[] = $row;
    }

    // Fetch existing grades
    $stmt_grades = $conn->prepare("SELECT student_id, marks FROM grades WHERE class_id = ? AND subject_id = ?");
    $stmt_grades->bind_param("ii", $class_id, $subject_id);
    $stmt_grades->execute();
    $grades_result = $stmt_grades->get_result();
    while ($row = $grades_result->fetch_assoc()) {
        $grades_records[$row['student_id']] = $row['marks'];
    }
}

?>

<div class="main-content">
    <h1 class="h3 mb-4 text-gray-800">Manage Grades</h1>

    <?php if (isset($success_message)): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Select Class and Subject</h6>
        </div>
        <div class="card-body">
            <form action="" method="get">
                <div class="form-row">
                    <div class="form-group col-md-5">
                        <label for="class_id">Class</label>
                        <select class="form-control" id="class_id" name="class_id" required onchange="this.form.submit()">
                            <option value="">Select Class</option>
                            <?php mysqli_data_seek($classes_result, 0); ?>
                            <?php while($class = $classes_result->fetch_assoc()): ?>
                                <option value="<?php echo $class['id']; ?>" <?php if(isset($_GET['class_id']) && $_GET['class_id'] == $class['id']) echo 'selected'; ?>><?php echo escape($class['class_name']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <?php if (!empty($subjects)): ?>
                    <div class="form-group col-md-5">
                        <label for="subject_id">Subject</label>
                        <select class="form-control" id="subject_id" name="subject_id" required onchange="this.form.submit()">
                            <option value="">Select Subject</option>
                            <?php foreach ($subjects as $subject): ?>
                                <option value="<?php echo $subject['id']; ?>" <?php if(isset($_GET['subject_id']) && $_GET['subject_id'] == $subject['id']) echo 'selected'; ?>><?php echo escape($subject['subject_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <?php if (!empty($students)): ?>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Enter Grades</h6>
        </div>
        <div class="card-body">
            <form action="" method="post">
                <input type="hidden" name="class_id" value="<?php echo $_GET['class_id']; ?>">
                <input type="hidden" name="subject_id" value="<?php echo $_GET['subject_id']; ?>">
                <div class="form-group">
                    <label for="exam_date">Exam Date</label>
                    <input type="date" class="form-control" id="exam_date" name="exam_date" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Roll Number</th>
                            <th>Marks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $student): 
                            $marks = $grades_records[$student['id']] ?? '';
                        ?>
                            <tr>
                                <td><?php echo escape($student['full_name']); ?></td>
                                <td><?php echo escape($student['roll_number']); ?></td>
                                <td>
                                    <input type="number" step="0.01" class="form-control" name="grades[<?php echo $student['id']; ?>]" value="<?php echo $marks; ?>">
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <button type="submit" name="save_grades" class="btn btn-primary">Save Grades</button>
            </form>
        </div>
    </div>
    <?php endif; ?>

</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
