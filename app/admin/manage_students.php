<?php
$page_title = 'Manage Students';
require_once __DIR__ . '/../../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    redirect(SITE_URL . '/public/index.php');
}

require_once __DIR__ . '/../../app/core/db.php';
require_once __DIR__ . '/../../includes/sidebar.php';

$db = new Database();
$conn = $db->getConnection();

// Handle success message
if (isset($_GET['success'])) {
    if ($_GET['success'] == 1) {
        $success_message = "Student added successfully!";
    } elseif ($_GET['success'] == 2) {
        $success_message = "Student updated successfully!";
    }
}

// Handle delete request
if (isset($_GET['delete_student'])) {
    $student_id = $_GET['delete_student'];
    $user_id = $_GET['user_id'];

    $conn->begin_transaction();

    try {
        // Delete from students table
        $stmt_student = $conn->prepare("DELETE FROM students WHERE id = ?");
        $stmt_student->bind_param("i", $student_id);
        $stmt_student->execute();

        // Delete from users table
        $stmt_user = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt_user->bind_param("i", $user_id);
        $stmt_user->execute();

        $conn->commit();
        $success_message = "Student deleted successfully!";
    } catch (Exception $e) {
        $conn->rollback();
        $error_message = "Error deleting student: " . $e->getMessage();
    }
}

// Fetch all students with their class and user email
$result = $conn->query("SELECT s.*, c.class_name, u.email, u.id as user_id FROM students s JOIN classes c ON s.class_id = c.id JOIN users u ON s.user_id = u.id ORDER BY s.full_name");

?>

<div class="main-content">
    <h1 class="h3 mb-4 text-gray-800">Manage Students</h1>

    <?php if (isset($success_message)): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>
    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <a href="<?php echo SITE_URL; ?>/app/admin/add_student.php" class="btn btn-primary mb-3">Add New Student</a>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Existing Students</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Class</th>
                            <th>Roll Number</th>
                            <th>Admission Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo escape($row['full_name']); ?></td>
                                <td><?php echo escape($row['email']); ?></td>
                                <td><?php echo escape($row['class_name']); ?></td>
                                <td><?php echo escape($row['roll_number']); ?></td>
                                <td><?php echo escape($row['admission_date']); ?></td>
                                <td>
                                    <a href="<?php echo SITE_URL; ?>/app/admin/edit_student.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <a href="?delete_student=<?php echo $row['id']; ?>&user_id=<?php echo $row['user_id']; ?>" class="btn btn-sm btn-danger delete-student">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>

<script>
    document.querySelectorAll('.delete-student').forEach(item => {
        item.addEventListener('click', event => {
            event.preventDefault();
            const href = event.target.getAttribute('href');
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = href;
                }
            })
        });
    });
</script>
