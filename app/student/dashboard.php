<?php 
$page_title = 'Student Dashboard';
require_once __DIR__ . '/../../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    redirect(SITE_URL . '/public/index.php');
}

require_once __DIR__ . '/../../app/core/db.php';

$db = new Database();
$conn = $db->getConnection();

$student_id = $_SESSION['user_id'];

// Get student details
$stmt = $conn->prepare("SELECT s.*, c.class_name FROM students s JOIN classes c ON s.class_id = c.id WHERE s.user_id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

// Get attendance summary
$stmt = $conn->prepare("SELECT status, COUNT(*) as count FROM attendance WHERE student_id = ? GROUP BY status");
$stmt->bind_param("i", $student['id']);
$stmt->execute();
$attendance_summary = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$present_days = 0;
$absent_days = 0;
foreach ($attendance_summary as $summary) {
    if ($summary['status'] == 'present') {
        $present_days = $summary['count'];
    } else {
        $absent_days = $summary['count'];
    }
}

require_once __DIR__ . '/../../includes/sidebar_student.php';
?>

<div class="main-content">
    <h1 class="h3 mb-4 text-gray-800">Student Dashboard</h1>
    <p>Welcome, <?php echo escape($_SESSION['username']); ?>!</p>

    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">My Information</h6>
                </div>
                <div class="card-body">
                    <p><strong>Class:</strong> <?php echo escape($student['class_name']); ?></p>
                    <p><strong>Roll Number:</strong> <?php echo escape($student['roll_number']); ?></p>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">My Attendance</h6>
                </div>
                <div class="card-body">
                    <p><strong>Present:</strong> <?php echo $present_days; ?> days</p>
                    <p><strong>Absent:</strong> <?php echo $absent_days; ?> days</p>
                </div>
            </div>
        </div>
    </div>

</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>