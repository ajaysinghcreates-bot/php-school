<?php
$page_title = 'Edit Class';
require_once __DIR__ . '/../../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    redirect(SITE_URL . '/public/index.php');
}

require_once __DIR__ . '/../../app/core/db.php';
require_once __DIR__ . '/../../includes/sidebar.php';

$db = new Database();
$conn = $db->getConnection();

$class_id = $_GET['id'];

// Handle form submission for updating the class
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_class'])) {
    $class_name = $_POST['class_name'];
    $class_code = $_POST['class_code'];

    $stmt = $conn->prepare("UPDATE classes SET class_name = ?, class_code = ? WHERE id = ?");
    $stmt->bind_param("ssi", $class_name, $class_code, $class_id);

    if ($stmt->execute()) {
        redirect(SITE_URL . '/app/admin/manage_classes.php?success=1');
    } else {
        $error_message = "Error updating class: " . $stmt->error;
    }
}

// Fetch class details
$stmt = $conn->prepare("SELECT * FROM classes WHERE id = ?");
$stmt->bind_param("i", $class_id);
$stmt->execute();
$result = $stmt->get_result();
$class = $result->fetch_assoc();

?>

<div class="main-content">
    <h1 class="h3 mb-4 text-gray-800">Edit Class</h1>

    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Edit Class Details</h6>
        </div>
        <div class="card-body">
            <form action="" method="post">
                <div class="form-group">
                    <label for="class_name">Class Name</label>
                    <input type="text" class="form-control" id="class_name" name="class_name" value="<?php echo escape($class['class_name']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="class_code">Class Code</label>
                    <input type="text" class="form-control" id="class_code" name="class_code" value="<?php echo escape($class['class_code']); ?>" required>
                </div>
                <button type="submit" name="update_class" class="btn btn-primary">Update Class</button>
                <a href="manage_classes.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
