<?php 
$page_title = 'Teacher Dashboard';
require_once __DIR__ . '/../../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    redirect(SITE_URL . '/public/index.php');
}

require_once __DIR__ . '/../../app/core/db.php';

$db = new Database();
$conn = $db->getConnection();

$teacher_id = $_SESSION['user_id'];

// Get teacher's classes
$stmt = $conn->prepare("SELECT c.id, c.class_name, COUNT(s.id) as student_count FROM classes c JOIN teacher_classes tc ON c.id = tc.class_id LEFT JOIN students s ON c.id = s.class_id WHERE tc.teacher_id = ? GROUP BY c.id");
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$classes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

require_once __DIR__ . '/../../includes/sidebar_teacher.php';
?>

<div class="main-content">
    <h1 class="h3 mb-4 text-gray-800">Teacher Dashboard</h1>
    <p>Welcome, <?php echo escape($_SESSION['username']); ?>!</p>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">My Classes</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Class</th>
                                    <th>Number of Students</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($classes as $class): ?>
                                    <tr>
                                        <td><?php echo escape($class['class_name']); ?></td>
                                        <td><?php echo $class['student_count']; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>