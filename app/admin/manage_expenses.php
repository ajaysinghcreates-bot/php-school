<?php
$page_title = 'Manage Expenses';
require_once __DIR__ . '/../../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    redirect(SITE_URL . '/public/index.php');
}

require_once __DIR__ . '/../../app/core/db.php';
require_once __DIR__ . '/../../includes/sidebar.php';

$db = new Database();
$conn = $db->getConnection();

// Handle delete request
if (isset($_GET['delete_expense'])) {
    $expense_id = $_GET['delete_expense'];
    $stmt = $conn->prepare("DELETE FROM expenses WHERE id = ?");
    $stmt->bind_param("i", $expense_id);
    if ($stmt->execute()) {
        $success_message = "Expense deleted successfully!";
    } else {
        $error_message = "Error deleting expense: " . $stmt->error;
    }
}

// Handle form submission for adding a new expense
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_expense'])) {
    $expense_category = $_POST['expense_category'];
    $amount = $_POST['amount'];
    $expense_date = $_POST['expense_date'];
    $description = $_POST['description'];

    $stmt = $conn->prepare("INSERT INTO expenses (expense_category, amount, expense_date, description) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sdss", $expense_category, $amount, $expense_date, $description);

    if ($stmt->execute()) {
        $success_message = "Expense added successfully!";
    } else {
        $error_message = "Error adding expense: " . $stmt->error;
    }
}

// Fetch all expenses
$expenses_result = $conn->query("SELECT * FROM expenses ORDER BY expense_date DESC");

?>

<div class="main-content">
    <h1 class="h3 mb-4 text-gray-800">Manage Expenses</h1>

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
                    <h6 class="m-0 font-weight-bold text-primary">Expense List</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = $expenses_result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo escape($row['expense_category']); ?></td>
                                        <td><?php echo escape($row['amount']); ?></td>
                                        <td><?php echo escape($row['expense_date']); ?></td>
                                        <td><?php echo escape($row['description']); ?></td>
                                        <td>
                                            <a href="?delete_expense=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger delete-expense">Delete</a>
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
                    <h6 class="m-0 font-weight-bold text-primary">Add New Expense</h6>
                </div>
                <div class="card-body">
                    <form action="" method="post">
                        <div class="form-group">
                            <label for="expense_category">Category</label>
                            <input type="text" class="form-control" id="expense_category" name="expense_category" required>
                        </div>
                        <div class="form-group">
                            <label for="amount">Amount</label>
                            <input type="number" step="0.01" class="form-control" id="amount" name="amount" required>
                        </div>
                        <div class="form-group">
                            <label for="expense_date">Date</label>
                            <input type="date" class="form-control" id="expense_date" name="expense_date" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                        <button type="submit" name="add_expense" class="btn btn-primary">Add Expense</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>

<script>
    document.querySelectorAll('.delete-expense').forEach(item => {
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
