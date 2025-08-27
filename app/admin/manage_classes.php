<?php
$page_title = 'Manage Classes';
require_once __DIR__ . '/../../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    redirect(SITE_URL . '/public/index.php');
}

require_once __DIR__ . '/../../app/core/db.php';
require_once __DIR__ . '/../../includes/sidebar.php';

$db = new Database();
$conn = $db->getConnection();

// Handle success message
if (isset($_GET['success']) && $_GET['success'] == 1) {
    $success_message = "Class updated successfully!";
}

// Handle delete request
if (isset($_GET['delete_class'])) {
    $class_id = $_GET['delete_class'];
    $stmt = $conn->prepare("DELETE FROM classes WHERE id = ?");
    $stmt->bind_param("i", $class_id);
    if ($stmt->execute()) {
        $success_message = "Class deleted successfully!";
    } else {
        $error_message = "Error deleting class: " . $stmt->error;
    }
}

// Handle form submission for adding a new class
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_class'])) {
    $class_name = $_POST['class_name'];
    $class_code = $_POST['class_code'];

    $stmt = $conn->prepare("INSERT INTO classes (class_name, class_code) VALUES (?, ?)");
    $stmt->bind_param("ss", $class_name, $class_code);

    if ($stmt->execute()) {
        $success_message = "Class added successfully!";
    } else {
        $error_message = "Error adding class: " . $stmt->error;
    }
}

// Fetch all classes
$result = $conn->query("SELECT * FROM classes ORDER BY class_name");

?>

<div class="main-content">
    <h1 class="h3 mb-4 text-gray-800">Manage Classes</h1>

    <?php if (isset($success_message)): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>
    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Existing Classes</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Class Name</th>
                                    <th>Class Code</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo escape($row['class_name']); ?></td>
                                        <td><?php echo escape($row['class_code']); ?></td>
                                        <td>
                                            <a href="edit_class.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                            <a href="?delete_class=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger delete-class">Delete</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Add New Class</h6>
                </div>
                <div class="card-body">
                    <form action="" method="post">
                        <div class="form-group">
                            <label for="class_name">Class Name</label>
                            <input type="text" class="form-control" id="class_name" name="class_name" required>
                        </div>
                        <div class="form-group">
                            <label for="class_code">Class Code</label>
                            <input type="text" class="form-control" id="class_code" name="class_code" required>
                        </div>
                        <button type="submit" name="add_class" class="btn btn-primary">Add Class</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>

<script>
    document.querySelectorAll('.delete-class').forEach(item => {
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
