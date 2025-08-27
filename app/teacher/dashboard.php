<?php 
$page_title = 'Teacher Dashboard';
require_once __DIR__ . '/../../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    redirect(SITE_URL . '/public/index.php');
}

require_once __DIR__ . '/../../includes/sidebar_teacher.php';
?>

<div class="main-content">
    <h1 class="h3 mb-4 text-gray-800">Teacher Dashboard</h1>
    <p>Welcome, <?php echo escape($_SESSION['username']); ?>!</p>
    
    <!-- Page content goes here -->

</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>