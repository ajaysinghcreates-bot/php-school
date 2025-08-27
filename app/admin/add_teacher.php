<?php
$page_title = 'Add Teacher';
require_once __DIR__ . '/../../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    redirect(SITE_URL . '/public/index.php');
}

require_once __DIR__ . '/../../app/core/db.php';
require_once __DIR__ . '/../../app/core/functions.php';
require_once __DIR__ . '/../../includes/sidebar.php';

$db = new Database();
$conn = $db->getConnection();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_teacher'])) {
    if (!validate_csrf_token($_POST['csrf_token'])) {
        $errors[] = 'Invalid CSRF token.';
    } else {
        $errors = validate_teacher_input($_POST);

        if (empty($errors)) {
            $conn->begin_transaction();

            try {
                // User data
                $username = $_POST['username'];
                $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $email = $_POST['email'];
                $full_name = $_POST['full_name'];
                $role = 'teacher';

                $stmt_user = $conn->prepare("INSERT INTO users (username, password, email, full_name, role) VALUES (?, ?, ?, ?, ?)");
                $stmt_user->bind_param("sssss", $username, $password, $email, $full_name, $role);
                $stmt_user->execute();
                $user_id = $stmt_user->insert_id;

                // Teacher data
                $gender = $_POST['gender'];
                $dob = $_POST['dob'];
                $phone = $_POST['phone'];
                $address = $_POST['address'];
                $qualification = $_POST['qualification'];
                $hire_date = $_POST['hire_date'];

                $stmt_teacher = $conn->prepare("INSERT INTO teachers (user_id, full_name, gender, dob, phone, address, qualification, hire_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt_teacher->bind_param("isssssss", $user_id, $full_name, $gender, $dob, $phone, $address, $qualification, $hire_date);
                $stmt_teacher->execute();

                $conn->commit();
                redirect(SITE_URL . '/app/admin/manage_teachers.php?success=1');
            } catch (Exception $e) {
                $conn->rollback();
                $error_message = "Error adding teacher: " . $e->getMessage();
            }
        }
    }
}

$csrf_token = generate_csrf_token();
?>

<div class="main-content">
    <h1 class="h3 mb-4 text-gray-800">Add New Teacher</h1>

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
            <h6 class="m-0 font-weight-bold text-primary">Teacher Details</h6>
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
                        <label for="phone">Phone</label>
                        <input type="text" class="form-control" id="phone" name="phone" value="<?php echo escape($_POST['phone'] ?? ''); ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="hire_date">Hire Date</label>
                        <input type="date" class="form-control" id="hire_date" name="hire_date" required value="<?php echo escape($_POST['hire_date'] ?? ''); ?>">
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
                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea class="form-control" id="address" name="address" rows="3"><?php echo escape($_POST['address'] ?? ''); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="qualification">Qualification</label>
                    <input type="text" class="form-control" id="qualification" name="qualification" value="<?php echo escape($_POST['qualification'] ?? ''); ?>">
                </div>
                <button type="submit" name="add_teacher" class="btn btn-primary">Add Teacher</button>
                <a href="manage_teachers.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
