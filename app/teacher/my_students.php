<?php
$page_title = 'My Students';
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

// Fetch students taught by the teacher
$stmt = $conn->prepare("SELECT s.*, c.class_name FROM students s JOIN classes c ON s.class_id = c.id WHERE c.teacher_id = ? ORDER BY c.class_name, s.full_name");
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();

?>

<div class="main-content">
    <h1 class="h3 mb-4 text-gray-800">My Students</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Student List</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Full Name</th>
                            <th>Class</th>
                            <th>Roll Number</th>
                            <th>Email</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo escape($row['full_name']); ?></td>
                                <td><?php echo escape($row['class_name']); ?></td>
                                <td><?php echo escape($row['roll_number']); ?></td>
                                <td><?php echo escape($row['email']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
