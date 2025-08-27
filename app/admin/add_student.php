<?php
$page_title = 'Add Student';
require_once __DIR__ . '/../../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    redirect(SITE_URL . '/public/index.php');
}

require_once __DIR__ . '/../../app/core/db.php';
require_once __DIR__ . '/../../app/core/functions.php';
require_once __DIR__ . '/../../includes/sidebar.php';

$db = new Database();
$conn = $db->getConnection();

// Fetch all classes for the dropdown
$classes_result = $conn->query("SELECT * FROM classes ORDER BY class_name");

$errors = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_student'])) {
    if (!validate_csrf_token($_POST['csrf_token'])) {
        $errors[] = 'Invalid CSRF token.';
    } else {
        $errors = validate_student_input($_POST);

        if (empty($errors)) {
            $conn->begin_transaction();

            try {
                // User data
                $username = $_POST['username'];
                $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $email = $_POST['email'];
                $full_name = $_POST['full_name'];
                $role = 'student';

                $stmt_user = $conn->prepare("INSERT INTO users (username, password, email, full_name, role) VALUES (?, ?, ?, ?, ?)");
                $stmt_user->bind_param("sssss", $username, $password, $email, $full_name, $role);
                $stmt_user->execute();
                $user_id = $stmt_user->insert_id;

                // Student data
                $class_id = $_POST['class_id'];
                $roll_number = $_POST['roll_number'];
                $gender = $_POST['gender'];
                $dob = $_POST['dob'];
                $parent_name = $_POST['parent_name'];
                $parent_phone = $_POST['parent_phone'];
                $address = $_POST['address'];
                $admission_date = $_POST['admission_date'];

                $stmt_student = $conn->prepare("INSERT INTO students (user_id, class_id, roll_number, full_name, gender, dob, parent_name, parent_phone, address, admission_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt_student->bind_param("iissssssss", $user_id, $class_id, $roll_number, $full_name, $gender, $dob, $parent_name, $parent_phone, $address, $admission_date);
                $stmt_student->execute();

                $conn->commit();
                redirect(SITE_URL . '/app/admin/manage_students.php?success=1');
            } catch (Exception $e) {
                $conn->rollback();
                $error_message = "Error adding student: " . $e->getMessage();
            }
        }
    }
}

$csrf_token = generate_csrf_token();
?>

<div class="main-content">
    <h1 class="h3 mb-4 text-gray-800">Add New Student</h1>

    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Student Details</h6>
        </div>
        <div class="card-body">
            <form action="" method="post">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="full_name">Full Name</label>
                        <input type="text" class="form-control" id="full_name" name="full_name" required value="<?php echo escape($_POST['full_name'] ?? ''); ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required value="<?php echo escape($_POST['email'] ?? ''); ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="username">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required value="<?php echo escape($_POST['username'] ?? ''); ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="class_id">Class</label>
                        <select class="form-control" id="class_id" name="class_id" required>
                            <option value="">Select Class</option>
                            <?php while($class = $classes_result->fetch_assoc()): ?>
                                <option value="<?php echo $class['id']; ?>" <?php if (isset($_POST['class_id']) && $_POST['class_id'] == $class['id']) echo 'selected'; ?>><?php echo escape($class['class_name']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="roll_number">Roll Number</label>
                        <input type="text" class="form-control" id="roll_number" name="roll_number" required value="<?php echo escape($_POST['roll_number'] ?? ''); ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="gender">Gender</label>
                        <select class="form-control" id="gender" name="gender">
                            <option value="Male" <?php if (isset($_POST['gender']) && $_POST['gender'] == 'Male') echo 'selected'; ?>>Male</option>
                            <option value="Female" <?php if (isset($_POST['gender']) && $_POST['gender'] == 'Female') echo 'selected'; ?>>Female</option>
                            <option value="Other" <?php if (isset($_POST['gender']) && $_POST['gender'] == 'Other') echo 'selected'; ?>>Other</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="dob">Date of Birth</label>
                        <input type="date" class="form-control" id="dob" name="dob" value="<?php echo escape($_POST['dob'] ?? ''); ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="parent_name">Parent's Name</label>
                        <input type="text" class="form-control" id="parent_name" name="parent_name" value="<?php echo escape($_POST['parent_name'] ?? ''); ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="parent_phone">Parent's Phone</label>
                        <input type="text" class="form-control" id="parent_phone" name="parent_phone" value="<?php echo escape($_POST['parent_phone'] ?? ''); ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea class="form-control" id="address" name="address" rows="3"><?php echo escape($_POST['address'] ?? ''); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="admission_date">Admission Date</label>
                    <input type="date" class="form-control" id="admission_date" name="admission_date" required value="<?php echo escape($_POST['admission_date'] ?? ''); ?>">
                </div>
                <button type="submit" name="add_student" class="btn btn-primary">Add Student</button>
                <a href="manage_students.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
