<?php
$page_title = 'My Attendance';
require_once __DIR__ . '/../../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    redirect(SITE_URL . '/public/index.php');
}

require_once __DIR__ . '/../../app/core/db.php';
require_once __DIR__ . '/../../includes/sidebar_student.php';

$db = new Database();
$conn = $db->getConnection();

// Get student ID from session
$student_id_result = $conn->query("SELECT id FROM students WHERE user_id = {$_SESSION['user_id']}");
$student = $student_id_result->fetch_assoc();
$student_id = $student['id'];

// Fetch attendance records
$stmt = $conn->prepare("SELECT attendance_date, status FROM attendance WHERE student_id = ? ORDER BY attendance_date DESC");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

// Calculate attendance summary
$total_present = 0;
$total_absent = 0;
$total_late = 0;

$stmt_summary = $conn->prepare("SELECT status, COUNT(*) as count FROM attendance WHERE student_id = ? GROUP BY status");
$stmt_summary->bind_param("i", $student_id);
$stmt_summary->execute();
$summary_result = $stmt_summary->get_result();
while($row = $summary_result->fetch_assoc()) {
    if ($row['status'] == 'Present') {
        $total_present = $row['count'];
    } elseif ($row['status'] == 'Absent') {
        $total_absent = $row['count'];
    } elseif ($row['status'] == 'Late') {
        $total_late = $row['count'];
    }
}

?>

<div class="main-content">
    <h1 class="h3 mb-4 text-gray-800">My Attendance</h1>

    <div class="row">
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Attendance Summary</h6>
                </div>
                <div class="card-body">
                    <p><strong>Total Present:</strong> <?php echo $total_present; ?></p>
                    <p><strong>Total Absent:</strong> <?php echo $total_absent; ?></p>
                    <p><strong>Total Late:</strong> <?php echo $total_late; ?></p>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Attendance History</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo escape($row['attendance_date']); ?></td>
                                        <td><?php echo escape($row['status']); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
