<?php
$page_title = 'Edit Student';
require_once __DIR__ . '/../../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    redirect(SITE_URL . '/public/index.php');
}

require_once __DIR__ . '/../../app/core/db.php';
require_once __DIR__ . '/../../includes/sidebar.php';

$db = new Database();
$conn = $db->getConnection();

$student_id = $_GET['id'];

// Fetch all classes for the dropdown
$classes_result = $conn->query("SELECT * FROM classes ORDER BY class_name");

// Handle form submission for updating the student
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_student'])) {
    $conn->begin_transaction();

    try {
        // User data
        $full_name = $_POST['full_name'];
        $email = $_POST['email'];
        $user_id = $_POST['user_id'];

        $stmt_user = $conn->prepare("UPDATE users SET email = ?, full_name = ? WHERE id = ?");
        $stmt_user->bind_param("ssi", $email, $full_name, $user_id);
        $stmt_user->execute();

        // Student data
        $class_id = $_POST['class_id'];
        $roll_number = $_POST['roll_number'];
        $gender = $_POST['gender'];
        $dob = $_POST['dob'];
        $parent_name = $_POST['parent_name'];
        $parent_phone = $_POST['parent_phone'];
        $address = $_POST['address'];
        $admission_date = $_POST['admission_date'];

        $stmt_student = $conn->prepare("UPDATE students SET class_id = ?, roll_number = ?, full_name = ?, gender = ?, dob = ?, parent_name = ?, parent_phone = ?, address = ?, admission_date = ? WHERE id = ?");
        $stmt_student->bind_param("issssssssi", $class_id, $roll_number, $full_name, $gender, $dob, $parent_name, $parent_phone, $address, $admission_date, $student_id);
        $stmt_student->execute();

        $conn->commit();
        redirect(SITE_URL . '/app/admin/manage_students.php?success=2');
    } catch (Exception $e) {
        $conn->rollback();
        $error_message = "Error updating student: " . $e->getMessage();
    }
}

// Fetch student details
$stmt = $conn->prepare("SELECT s.*, u.email, u.username FROM students s JOIN users u ON s.user_id = u.id WHERE s.id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

?>

<div class="main-content">
    <h1 class="h3 mb-4 text-gray-800">Edit Student</h1>

    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Student Details</h6>
        </div>
        <div class="card-body">
            <form action="" method="post">
                <input type="hidden" name="user_id" value="<?php echo $student['user_id']; ?>">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="full_name">Full Name</label>
                        <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo escape($student['full_name']); ?>" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo escape($student['email']); ?>" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="username">Username</label>
                        <input type="text" class="form-control" id="username" name="username" value="<?php echo escape($student['username']); ?>" disabled>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="class_id">Class</label>
                        <select class="form-control" id="class_id" name="class_id" required>
                            <option value="">Select Class</option>
                            <?php while($class = $classes_result->fetch_assoc()): ?>
                                <option value="<?php echo $class['id']; ?>" <?php if($class['id'] == $student['class_id']) echo 'selected'; ?>><?php echo escape($class['class_name']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="roll_number">Roll Number</label>
                        <input type="text" class="form-control" id="roll_number" name="roll_number" value="<?php echo escape($student['roll_number']); ?>" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="gender">Gender</label>
                        <select class="form-control" id="gender" name="gender">
                            <option value="Male" <?php if($student['gender'] == 'Male') echo 'selected'; ?>>Male</option>
                            <option value="Female" <?php if($student['gender'] == 'Female') echo 'selected'; ?>>Female</option>
                            <option value="Other" <?php if($student['gender'] == 'Other') echo 'selected'; ?>>Other</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="dob">Date of Birth</label>
                        <input type="date" class="form-control" id="dob" name="dob" value="<?php echo escape($student['dob']); ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="parent_name">Parent's Name</label>
                        <input type="text" class="form-control" id="parent_name" name="parent_name" value="<?php echo escape($student['parent_name']); ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="parent_phone">Parent's Phone</label>
                        <input type="text" class="form-control" id="parent_phone" name="parent_phone" value="<?php echo escape($student['parent_phone']); ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="admission_date">Admission Date</label>
                        <input type="date" class="form-control" id="admission_date" name="admission_date" value="<?php echo escape($student['admission_date']); ?>" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea class="form-control" id="address" name="address" rows="3"><?php echo escape($student['address']); ?></textarea>
                </div>
                <button type="submit" name="update_student" class="btn btn-primary">Update Student</button>
                <a href="manage_students.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
