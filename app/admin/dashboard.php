<?php
$page_title = 'Admin Dashboard';
require_once __DIR__ . '/../../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    redirect(SITE_URL . '/public/index.php');
}

require_once __DIR__ . '/../../app/core/db.php';

$db = new Database();
$conn = $db->getConnection();

// Get total students
$student_result = $conn->query("SELECT COUNT(*) as count FROM students");
$total_students = $student_result->fetch_assoc()['count'];

// Get total teachers
$teacher_result = $conn->query("SELECT COUNT(*) as count FROM teachers");
$total_teachers = $teacher_result->fetch_assoc()['count'];

// Get total revenue
$revenue_result = $conn->query("SELECT SUM(amount_paid) as total FROM fee_payments");
$total_revenue = $revenue_result->fetch_assoc()['total'] ?? '0.00';

// Get total expenses
$expenses_result = $conn->query("SELECT SUM(amount) as total FROM expenses");
$total_expenses = $expenses_result->fetch_assoc()['total'] ?? '0.00';

// Get student distribution by class
$class_distribution_result = $conn->query("SELECT c.class_name, COUNT(s.id) as student_count FROM classes c LEFT JOIN students s ON c.id = s.class_id GROUP BY c.id");
$class_labels = [];
$class_data = [];
while($row = $class_distribution_result->fetch_assoc()) {
    $class_labels[] = $row['class_name'];
    $class_data[] = $row['student_count'];
}

require_once __DIR__ . '/../../includes/sidebar.php';
?>

<div class="main-content">
    <h1 class="h3 mb-4 text-gray-800">Admin Dashboard</h1>

    <div class="row">
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <a href="<?php echo SITE_URL; ?>/app/admin/add_student.php" class="btn btn-primary btn-block mb-2">Add Student</a>
                    <a href="<?php echo SITE_URL; ?>/app/admin/add_teacher.php" class="btn btn-primary btn-block mb-2">Add Teacher</a>
                    <a href="<?php echo SITE_URL; ?>/app/admin/manage_classes.php#addClassModal" class="btn btn-primary btn-block">Add Class</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Students</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_students; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-graduate fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Teachers</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_teachers; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chalkboard-teacher fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Revenue</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">$<?php echo $total_revenue; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Expenses</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">$<?php echo $total_expenses; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-invoice-dollar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Student Distribution by Class</h6>
                </div>
                <div class="card-body">
                    <canvas id="studentClassChart"></canvas>
                </div>
            </div>
        </div>
    </div>

</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>

<script>
const ctx = document.getElementById('studentClassChart').getContext('2d');
const studentClassChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($class_labels); ?>,
        datasets: [{
            label: 'Number of Students',
            data: <?php echo json_encode($class_data); ?>,
            backgroundColor: 'rgba(78, 115, 223, 0.8)',
            borderColor: 'rgba(78, 115, 223, 1)',
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>