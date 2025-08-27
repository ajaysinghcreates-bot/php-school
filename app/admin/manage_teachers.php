<?php
$page_title = 'Manage Teachers';
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
        $success_message = "Teacher added successfully!";
    } elseif ($_GET['success'] == 2) {
        $success_message = "Teacher updated successfully!";
    }
}

// Handle delete request
if (isset($_GET['delete_teacher'])) {
    $teacher_id = $_GET['delete_teacher'];
    $user_id = $_GET['user_id'];

    $conn->begin_transaction();

    try {
        // Delete from teachers table
        $stmt_teacher = $conn->prepare("DELETE FROM teachers WHERE id = ?");
        $stmt_teacher->bind_param("i", $teacher_id);
        $stmt_teacher->execute();

        // Delete from users table
        $stmt_user = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt_user->bind_param("i", $user_id);
        $stmt_user->execute();

        $conn->commit();
        $success_message = "Teacher deleted successfully!";
    } catch (Exception $e) {
        $conn->rollback();
        $error_message = "Error deleting teacher: " . $e->getMessage();
    }
}


// Fetch all teachers with their user email
$result = $conn->query("SELECT t.*, u.email FROM teachers t JOIN users u ON t.user_id = u.id ORDER BY t.full_name");

?>

<div class="main-content">
    <h1 class="h3 mb-4 text-gray-800">Manage Teachers</h1>

    <?php if (isset($success_message)): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>
    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <a href="add_teacher.php" class="btn btn-primary mb-3">Add New Teacher</a>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Existing Teachers</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Hire Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo escape($row['full_name']); ?></td>
                                <td><?php echo escape($row['email']); ?></td>
                                <td><?php echo escape($row['phone']); ?></td>
                                <td><?php echo escape($row['hire_date']); ?></td>
                                <td>
                                    <a href="edit_teacher.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <a href="?delete_teacher=<?php echo $row['id']; ?>&user_id=<?php echo $row['user_id']; ?>" class="btn btn-sm btn-danger delete-teacher">Delete</a>
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
    document.querySelectorAll('.delete-teacher').forEach(item => {
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
