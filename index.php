<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['alert'])) {
    $_SESSION['alert'] = null;
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['videos'])) {
    $_SESSION['videos'] = array(); // Initialize videos session array if not already set
}

require 'functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Rental System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <?php include 'menu.php'; ?>
    <div class="content-wrapper">
        <section class="content">
            <div class="container-fluid">
                <?php
                $page = $_GET['page'] ?? 'home'; // Default to home page if no specific page request
                switch ($page) {

                    // BOOK OPTIONS
                    case 'add_book':
                        include 'add_book.php';
                        break;
                        
                    case 'edit_book':
                        include 'edit_book.php';
                        break;
                
                    case 'delete_book':
                        include 'delete_book.php';
                        break;
                            
                    case 'view_book':
                        include 'view_book.php';
                        break;
                        
                    case 'view_single_book':
                        include 'view_single_book.php';
                        break;
                    case 'add_student':

                    case 'edit_student':

                    case 'delete_student':
                        if ($_SESSION['role'] === 'student') {
                            header('Location: student.php'); // Redirect students to student.php
                            exit;
                        } else {
                            include $_GET['page'] . '.php'; // Allow access for librarians
                        }
                        break;

                    case 'view_student':

                    case 'view_single_student':
                        if ($_SESSION['role'] === 'student') {
                            header('Location: student.php'); // Redirect students to student.php
                            exit;
                        } else {
                            include $_GET['page'] . '.php'; // Allow access for librarians
                        }
                        break;

                    case 'add_librarian':
                        include 'add_librarian.php';
                        break;

                    default:
                        echo '<div class="alert alert-info">Welcome to the Video Rental System!</div>';
                        break;
                }
                ?>
            </div>
        </section>
    </div>
    <footer class="main-footer">
        <strong>Copyright &copy; 2023 Your Company.</strong>
        All rights reserved.
        <div class="float-right d-none d-sm-inline-block">
            <b>Version</b> 3.2.0
        </div>
    </footer>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/js/adminlte.min.js"></script>
</body>
</html>
