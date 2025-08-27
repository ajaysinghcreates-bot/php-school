<div class="sidebar">
    <h3 class="text-white text-center mt-3">Naamu SMS</h3>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link active" href="<?php echo SITE_URL; ?>/app/admin/dashboard.php">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="<?php echo SITE_URL; ?>/app/admin/manage_students.php">
                <i class="fas fa-user-graduate"></i> Students
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="<?php echo SITE_URL; ?>/app/admin/manage_teachers.php">
                <i class="fas fa-chalkboard-teacher"></i> Teachers
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="<?php echo SITE_URL; ?>/app/admin/manage_classes.php">
                <i class="fas fa-school"></i> Classes
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="<?php echo SITE_URL; ?>/app/admin/manage_subjects.php">
                <i class="fas fa-book"></i> Subjects
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseFinancial" aria-expanded="true" aria-controls="collapseFinancial">
                <i class="fas fa-money-bill-wave"></i>
                <span>Financial</span>
            </a>
            <div id="collapseFinancial" class="collapse" aria-labelledby="headingFinancial" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item" href="<?php echo SITE_URL; ?>/app/admin/manage_fees.php">Manage Fees</a>
                    <a class="collapse-item" href="<?php echo SITE_URL; ?>/app/admin/manage_expenses.php">Manage Expenses</a>
                </div>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">
                <i class="fas fa-cog"></i> Settings
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="<?php echo SITE_URL; ?>/public/logout.php">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </li>
    </ul>
</div>