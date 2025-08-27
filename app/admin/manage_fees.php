<?php
$page_title = 'Manage Fees';
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
    $success_message = "Payment added successfully!";
}

// Handle form submission for adding a new fee structure
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_fee_structure'])) {
    $class_id = $_POST['class_id'];
    $fee_type = $_POST['fee_type'];
    $amount = $_POST['amount'];

    $stmt = $conn->prepare("INSERT INTO fee_structure (class_id, fee_type, amount) VALUES (?, ?, ?)");
    $stmt->bind_param("isd", $class_id, $fee_type, $amount);

    if ($stmt->execute()) {
        $success_message = "Fee structure added successfully!";
    } else {
        $error_message = "Error adding fee structure: " . $stmt->error;
    }
}

// Fetch all fee structures with class names
$fee_structures_result = $conn->query("SELECT fs.*, c.class_name FROM fee_structure fs JOIN classes c ON fs.class_id = c.id ORDER BY c.class_name, fs.fee_type");

// Fetch all classes for the dropdown
$classes_result = $conn->query("SELECT * FROM classes ORDER BY class_name");

// Fetch all fee payments
$payments_result = $conn->query("SELECT fp.*, s.full_name, c.class_name FROM fee_payments fp JOIN students s ON fp.student_id = s.id JOIN classes c ON fp.class_id = c.id ORDER BY fp.payment_date DESC");

?>

<div class="main-content">
    <h1 class="h3 mb-4 text-gray-800">Manage Fees</h1>

    <?php if (isset($success_message)): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>
    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <a href="add_payment.php" class="btn btn-primary mb-3">Add New Payment</a>

    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Fee Structure</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Class</th>
                                    <th>Fee Type</th>
                                    <th>Amount</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = $fee_structures_result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo escape($row['class_name']); ?></td>
                                        <td><?php echo escape($row['fee_type']); ?></td>
                                        <td><?php echo escape($row['amount']); ?></td>
                                        <td>
                                            <a href="#" class="btn btn-sm btn-danger">Delete</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Add Fee Structure</h6>
                </div>
                <div class="card-body">
                    <form action="" method="post">
                        <div class="form-group">
                            <label for="class_id">Class</label>
                            <select class="form-control" id="class_id" name="class_id" required>
                                <option value="">Select Class</option>
                                <?php while($class = $classes_result->fetch_assoc()): ?>
                                    <option value="<?php echo $class['id']; ?>"><?php echo escape($class['class_name']); ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="fee_type">Fee Type</label>
                            <input type="text" class="form-control" id="fee_type" name="fee_type" required>
                        </div>
                        <div class="form-group">
                            <label for="amount">Amount</label>
                            <input type="number" step="0.01" class="form-control" id="amount" name="amount" required>
                        </div>
                        <button type="submit" name="add_fee_structure" class="btn btn-primary">Add Fee Structure</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Fee Payments</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable2" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Receipt No</th>
                            <th>Student Name</th>
                            <th>Class</th>
                            <th>Amount Paid</th>
                            <th>Payment Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $payments_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo escape($row['receipt_no']); ?></td>
                                <td><?php echo escape($row['full_name']); ?></td>
                                <td><?php echo escape($row['class_name']); ?></td>
                                <td><?php echo escape($row['amount_paid']); ?></td>
                                <td><?php echo escape($row['payment_date']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
