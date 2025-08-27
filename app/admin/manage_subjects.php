<?php
$page_title = 'Manage Subjects';
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
    $success_message = "Subject updated successfully!";
}

// Handle delete request
if (isset($_GET['delete_subject'])) {
    $subject_id = $_GET['delete_subject'];
    $stmt = $conn->prepare("DELETE FROM subjects WHERE id = ?");
    $stmt->bind_param("i", $subject_id);
    if ($stmt->execute()) {
        $success_message = "Subject deleted successfully!";
    } else {
        $error_message = "Error deleting subject: " . $stmt->error;
    }
}

// Handle form submission for adding a new subject
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_subject'])) {
    $subject_name = $_POST['subject_name'];
    $subject_code = $_POST['subject_code'];
    $class_id = $_POST['class_id'];

    $stmt = $conn->prepare("INSERT INTO subjects (subject_name, subject_code, class_id) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $subject_name, $subject_code, $class_id);

    if ($stmt->execute()) {
        $success_message = "Subject added successfully!";
    } else {
        $error_message = "Error adding subject: " . $stmt->error;
    }
}

// Fetch all subjects with class names
$result = $conn->query("SELECT s.*, c.class_name FROM subjects s JOIN classes c ON s.class_id = c.id ORDER BY s.subject_name");

// Fetch all classes for the dropdown
$classes_result = $conn->query("SELECT * FROM classes ORDER BY class_name");

?>

<div class="main-content">
    <h1 class="h3 mb-4 text-gray-800">Manage Subjects</h1>

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
                    <h6 class="m-0 font-weight-bold text-primary">Existing Subjects</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Subject Name</th>
                                    <th>Subject Code</th>
                                    <th>Class</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo escape($row['subject_name']); ?></td>
                                        <td><?php echo escape($row['subject_code']); ?></td>
                                        <td><?php echo escape($row['class_name']); ?></td>
                                        <td>
                                            <a href="edit_subject.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                            <a href="?delete_subject=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger delete-subject">Delete</a>
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
                    <h6 class="m-0 font-weight-bold text-primary">Add New Subject</h6>
                </div>
                <div class="card-body">
                    <form action="" method="post">
                        <div class="form-group">
                            <label for="subject_name">Subject Name</label>
                            <input type="text" class="form-control" id="subject_name" name="subject_name" required>
                        </div>
                        <div class="form-group">
                            <label for="subject_code">Subject Code</label>
                            <input type="text" class="form-control" id="subject_code" name="subject_code" required>
                        </div>
                        <div class="form-group">
                            <label for="class_id">Class</label>
                            <select class="form-control" id="class_id" name="class_id" required>
                                <option value="">Select Class</option>
                                <?php while($class = $classes_result->fetch_assoc()): ?>
                                    <option value="<?php echo $class['id']; ?>"><?php echo escape($class['class_name']); ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <button type="submit" name="add_subject" class="btn btn-primary">Add Subject</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>

<script>
    document.querySelectorAll('.delete-subject').forEach(item => {
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
