<?php
$page_title = 'Edit Teacher';
require_once __DIR__ . '/../../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    redirect(SITE_URL . '/public/index.php');
}

require_once __DIR__ . '/../../app/core/db.php';
require_once __DIR__ . '/../../includes/sidebar.php';

$db = new Database();
$conn = $db->getConnection();

$teacher_id = $_GET['id'];

// Handle form submission for updating the teacher
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_teacher'])) {
    $conn->begin_transaction();

    try {
        // User data
        $full_name = $_POST['full_name'];
        $email = $_POST['email'];
        $user_id = $_POST['user_id'];

        $stmt_user = $conn->prepare("UPDATE users SET email = ?, full_name = ? WHERE id = ?");
        $stmt_user->bind_param("ssi", $email, $full_name, $user_id);
        $stmt_user->execute();

        // Teacher data
        $gender = $_POST['gender'];
        $dob = $_POST['dob'];
        $phone = $_POST['phone'];
        $address = $_POST['address'];
        $qualification = $_POST['qualification'];
        $hire_date = $_POST['hire_date'];

        $stmt_teacher = $conn->prepare("UPDATE teachers SET full_name = ?, gender = ?, dob = ?, phone = ?, address = ?, qualification = ?, hire_date = ? WHERE id = ?");
        $stmt_teacher->bind_param("sssssssi", $full_name, $gender, $dob, $phone, $address, $qualification, $hire_date, $teacher_id);
        $stmt_teacher->execute();

        $conn->commit();
        redirect(SITE_URL . '/app/admin/manage_teachers.php?success=2');
    } catch (Exception $e) {
        $conn->rollback();
        $error_message = "Error updating teacher: " . $e->getMessage();
    }
}

// Fetch teacher details
$stmt = $conn->prepare("SELECT t.*, u.email, u.username FROM teachers t JOIN users u ON t.user_id = u.id WHERE t.id = ?");
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();
$teacher = $result->fetch_assoc();

?>

<div class="main-content">
    <h1 class="h3 mb-4 text-gray-800">Edit Teacher</h1>

    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Teacher Details</h6>
        </div>
        <div class="card-body">
            <form action="" method="post">
                <input type="hidden" name="user_id" value="<?php echo $teacher['user_id']; ?>">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="full_name">Full Name</label>
                        <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo escape($teacher['full_name']); ?>" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo escape($teacher['email']); ?>" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="username">Username</label>
                        <input type="text" class="form-control" id="username" name="username" value="<?php echo escape($teacher['username']); ?>" disabled>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="phone">Phone</label>
                        <input type="text" class="form-control" id="phone" name="phone" value="<?php echo escape($teacher['phone']); ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="hire_date">Hire Date</label>
                        <input type="date" class="form-control" id="hire_date" name="hire_date" value="<?php echo escape($teacher['hire_date']); ?>" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="gender">Gender</label>
                        <select class="form-control" id="gender" name="gender">
                            <option value="Male" <?php if($teacher['gender'] == 'Male') echo 'selected'; ?>>Male</option>
                            <option value="Female" <?php if($teacher['gender'] == 'Female') echo 'selected'; ?>>Female</option>
                            <option value="Other" <?php if($teacher['gender'] == 'Other') echo 'selected'; ?>>Other</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="dob">Date of Birth</label>
                        <input type="date" class="form-control" id="dob" name="dob" value="<?php echo escape($teacher['dob']); ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="qualification">Qualification</label>
                        <input type="text" class="form-control" id="qualification" name="qualification" value="<?php echo escape($teacher['qualification']); ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea class="form-control" id="address" name="address" rows="3"><?php echo escape($teacher['address']); ?></textarea>
                </div>
                <button type="submit" name="update_teacher" class="btn btn-primary">Update Teacher</button>
                <a href="manage_teachers.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
