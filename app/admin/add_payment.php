<?php
$page_title = 'Add Fee Payment';
require_once __DIR__ . '/../../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    redirect(SITE_URL . '/public/index.php');
}

require_once __DIR__ . '/../../app/core/db.php';
require_once __DIR__ . '/../../includes/sidebar.php';

$db = new Database();
$conn = $db->getConnection();

// Handle form submission for adding a new payment
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_payment'])) {
    $student_id = $_POST['student_id'];
    $class_id = $_POST['class_id'];
    $amount_paid = $_POST['amount_paid'];
    $payment_date = $_POST['payment_date'];
    $receipt_no = $_POST['receipt_no'];

    $stmt = $conn->prepare("INSERT INTO fee_payments (student_id, class_id, amount_paid, payment_date, receipt_no) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iidss", $student_id, $class_id, $amount_paid, $payment_date, $receipt_no);

    if ($stmt->execute()) {
        redirect(SITE_URL . '/app/admin/manage_fees.php?success=1');
    } else {
        $error_message = "Error adding payment: " . $stmt->error;
    }
}

// Fetch all students for the dropdown
$students_result = $conn->query("SELECT id, full_name, class_id FROM students ORDER BY full_name");

?>

<div class="main-content">
    <h1 class="h3 mb-4 text-gray-800">Add Fee Payment</h1>

    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Payment Details</h6>
        </div>
        <div class="card-body">
            <form action="" method="post">
                <div class="form-group">
                    <label for="student_id">Student</label>
                    <select class="form-control" id="student_id" name="student_id" required>
                        <option value="">Select Student</option>
                        <?php while($student = $students_result->fetch_assoc()): ?>
                            <option value="<?php echo $student['id']; ?>" data-class-id="<?php echo $student['class_id']; ?>"><?php echo escape($student['full_name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <input type="hidden" id="class_id" name="class_id">
                <div class="form-group">
                    <label for="amount_paid">Amount Paid</label>
                    <input type="number" step="0.01" class="form-control" id="amount_paid" name="amount_paid" required>
                </div>
                <div class="form-group">
                    <label for="payment_date">Payment Date</label>
                    <input type="date" class="form-control" id="payment_date" name="payment_date" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <div class="form-group">
                    <label for="receipt_no">Receipt Number</label>
                    <input type="text" class="form-control" id="receipt_no" name="receipt_no" required>
                </div>
                <button type="submit" name="add_payment" class="btn btn-primary">Add Payment</button>
                <a href="manage_fees.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>

<script>
    document.getElementById('student_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        document.getElementById('class_id').value = selectedOption.getAttribute('data-class-id');
    });
</script>
