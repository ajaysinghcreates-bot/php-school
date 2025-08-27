<?php
$page_title = 'Edit Subject';
require_once __DIR__ . '/../../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    redirect(SITE_URL . '/public/index.php');
}

require_once __DIR__ . '/../../app/core/db.php';
require_once __DIR__ . '/../../includes/sidebar.php';

$db = new Database();
$conn = $db->getConnection();

$subject_id = $_GET['id'];

// Handle form submission for updating the subject
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_subject'])) {
    $subject_name = $_POST['subject_name'];
    $subject_code = $_POST['subject_code'];
    $class_id = $_POST['class_id'];

    $stmt = $conn->prepare("UPDATE subjects SET subject_name = ?, subject_code = ?, class_id = ? WHERE id = ?");
    $stmt->bind_param("ssii", $subject_name, $subject_code, $class_id, $subject_id);

    if ($stmt->execute()) {
        redirect(SITE_URL . '/app/admin/manage_subjects.php?success=1');
    } else {
        $error_message = "Error updating subject: " . $stmt->error;
    }
}

// Fetch subject details
$stmt = $conn->prepare("SELECT * FROM subjects WHERE id = ?");
$stmt->bind_param("i", $subject_id);
$stmt->execute();
$result = $stmt->get_result();
$subject = $result->fetch_assoc();

// Fetch all classes for the dropdown
$classes_result = $conn->query("SELECT * FROM classes ORDER BY class_name");

?>

<div class="main-content">
    <h1 class="h3 mb-4 text-gray-800">Edit Subject</h1>

    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Edit Subject Details</h6>
        </div>
        <div class="card-body">
            <form action="" method="post">
                <div class="form-group">
                    <label for="subject_name">Subject Name</label>
                    <input type="text" class="form-control" id="subject_name" name="subject_name" value="<?php echo escape($subject['subject_name']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="subject_code">Subject Code</label>
                    <input type="text" class="form-control" id="subject_code" name="subject_code" value="<?php echo escape($subject['subject_code']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="class_id">Class</label>
                    <select class="form-control" id="class_id" name="class_id" required>
                        <option value="">Select Class</option>
                        <?php while($class = $classes_result->fetch_assoc()): ?>
                            <option value="<?php echo $class['id']; ?>" <?php if($class['id'] == $subject['class_id']) echo 'selected'; ?>><?php echo escape($class['class_name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <button type="submit" name="update_subject" class="btn btn-primary">Update Subject</button>
                <a href="manage_subjects.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
