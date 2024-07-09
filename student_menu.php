<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index.php" class="brand-link">
        <span class="brand-text font-weight-light">Welcome <?php echo htmlspecialchars($_SESSION['fname']); ?>!</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

                <!-- Available Books -->
                <li class="nav-item">
                    <a href="student.php" class="nav-link">
                        <i class="nav-icon fas fa-book"></i>
                        <p>Available Books</p>
                    </a>
                </li>

                <!-- Borrowed Books -->
                <li class="nav-item">
                    <a href="borrowed_books.php" class="nav-link">
                        <i class="nav-icon fas fa-user"></i>
                        <p>Profile</p>
                    </a>
                </li> 

                <!-- Logout Link -->
                <li class="nav-item">
                    <a href="logout.php" class="nav-link">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>Logout</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>
