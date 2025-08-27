<?php
$page_title = 'My Grades';
require_once __DIR__ . '/../../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    redirect(SITE_URL . '/public/index.php');
}

require_once __DIR__ . '/../../app/core/db.php';
require_once __DIR__ . '/../../includes/sidebar_student.php';

$db = new Database();
$conn = $db->getConnection();

// Get student ID from session
$student_id_result = $conn->query("SELECT id FROM students WHERE user_id = {$_SESSION['user_id']}");
$student = $student_id_result->fetch_assoc();
$student_id = $student['id'];

// Fetch grades
$stmt = $conn->prepare("SELECT g.*, s.subject_name FROM grades g JOIN subjects s ON g.subject_id = s.id WHERE g.student_id = ? ORDER BY g.exam_date DESC");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

?>

<div class="main-content">
    <h1 class="h3 mb-4 text-gray-800">My Grades</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">My Grade History</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Subject</th>
                            <th>Marks</th>
                            <th>Grade</th>
                            <th>Exam Date</th>
                            <th>Comments</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo escape($row['subject_name']); ?></td>
                                <td><?php echo escape($row['marks']); ?></td>
                                <td><?php echo escape($row['grade']); ?></td>
                                <td><?php echo escape($row['exam_date']); ?></td>
                                <td><?php echo escape($row['comments']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
