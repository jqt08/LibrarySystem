<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is a student
if (!(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true && $_SESSION['role'] === 'student')) {
    header('Location: login.php'); // Redirect to login page if not logged in as a student
    exit;
}

$message_borrow = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        You can only borrow up to 2 books.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';

// Database connection details
$host = 'localhost';
$db_name = 'librarysystem';
$username_db = 'root';
$password_db = '';

// Initialize $books as an empty array to avoid undefined variable warning
$books = [];

try {
    // Connect to the database
    $pdo = new PDO("mysql:host=$host;dbname=$db_name", $username_db, $password_db);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch student information
    $student_id = $_SESSION['student_id'];
    $studentStmt = $pdo->prepare("SELECT * FROM student WHERE student_id = :student_id");
    $studentStmt->bindParam(':student_id', $student_id);
    $studentStmt->execute();
    $student = $studentStmt->fetch(PDO::FETCH_ASSOC);

    // Handle borrow and return actions
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $book_id = $_POST['book_id'];
        
        // Determine action type
        $action = $_POST['action'];

        // Ensure the student can only borrow up to 2 books
        $borrowedBooks = 0;
        if ($student['book_id1'] !== null) $borrowedBooks++;
        if ($student['book_id2'] !== null) $borrowedBooks++;

        if ($action === 'borrow') {
            if ($borrowedBooks >= 2) {
                echo $message_borrow;
                echo '<script>alert("You can only borrow up to 2 books.");</script>';
            } else {
                // Update book status to 'borrowed'
                $updateStmt = $pdo->prepare("UPDATE book SET status = 'borrowed' WHERE book_id = :book_id AND status = 'available'");
                $updateStmt->bindParam(':book_id', $book_id);
                if ($updateStmt->execute()) {
                    // Update student's borrowed books
                    if ($student['book_id1'] === null) {
                        $stmt = $pdo->prepare("UPDATE student SET book_id1 = :book_id WHERE student_id = :student_id");
                    } else {
                        $stmt = $pdo->prepare("UPDATE student SET book_id2 = :book_id WHERE student_id = :student_id");
                    }
                    $stmt->bindParam(':book_id', $book_id);
                    $stmt->bindParam(':student_id', $student_id);
                    $stmt->execute();

                    // Calculate fine if more than 7 days
                    $borrow_days = $_POST['borrow_days'];
                    $fine = 0;
                    $surplus_days = $borrow_days - 7;
                    if ($surplus_days > 0) {
                        $fine = $surplus_days * 10.00;

                        // Update student's fine in the database
                        $fineStmt = $pdo->prepare("UPDATE student SET fine = fine + :fine WHERE student_id = :student_id");
                        $fineStmt->execute(['fine' => $fine, 'student_id' => $student_id]);
                    }

                    // Refresh page to reflect changes
                    header("Location: student.php");
                    exit();
                } else {
                    echo '<script>alert("Failed to borrow the book. Please try again.");</script>';
                }
            }
        } elseif ($action === 'return') {
            // Ensure the book being returned belongs to the logged-in student
            $checkStmt = $pdo->prepare("SELECT * FROM student WHERE student_id = :student_id AND (book_id1 = :book_id OR book_id2 = :book_id)");
            $checkStmt->bindParam(':student_id', $student_id);
            $checkStmt->bindParam(':book_id', $book_id);
            $checkStmt->execute();
            $studentBorrowed = $checkStmt->fetch(PDO::FETCH_ASSOC);

            if ($studentBorrowed) {
                // Update book status to 'available'
                $updateStmt = $pdo->prepare("UPDATE book SET status = 'available' WHERE book_id = :book_id AND status = 'borrowed'");
                $updateStmt->bindParam(':book_id', $book_id);
                if ($updateStmt->execute()) {
                    // Update student's borrowed books
                    if ($student['book_id1'] == $book_id) {
                        $stmt = $pdo->prepare("UPDATE student SET book_id1 = NULL WHERE student_id = :student_id");
                    } elseif ($student['book_id2'] == $book_id) {
                        $stmt = $pdo->prepare("UPDATE student SET book_id2 = NULL WHERE student_id = :student_id");
                    }
                    $stmt->bindParam(':student_id', $student_id);
                    $stmt->execute();
                    // Refresh page to reflect changes
                    header("Location: student.php");
                    exit();
                } else {
                    echo '<script>alert("Failed to return the book. Please try again.");</script>';
                }
            } else {
                echo '<script>alert("You can only return books that you have borrowed.");</script>';
            }
        }
    }

    // Fetch available books (excluding those borrowed by other students)
    $stmt = $pdo->prepare("SELECT * FROM book WHERE status = 'available' OR book_id IN (:book_id1, :book_id2)");
    $stmt->bindParam(':book_id1', $student['book_id1']);
    $stmt->bindParam(':book_id2', $student['book_id2']);
    $stmt->execute();
    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/css/adminlte.min.css">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <!-- Student Sidebar Menu -->
    <?php include 'student_menu.php'; ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <section class="content">
            <div class="container-fluid">
                <!-- Student Profile -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Student Profile</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th>Student ID</th>
                                    <td><?= htmlspecialchars($student['student_id']) ?></td>
                                </tr>
                                <tr>
                                    <th>First Name</th>
                                    <td><?= htmlspecialchars($student['fname']) ?></td>
                                </tr>
                                <tr>
                                    <th>Last Name</th>
                                    <td><?= htmlspecialchars($student['lname']) ?></td>
                                </tr>
                                <tr>
                                    <th>Fine</th>
                                    <td><?= htmlspecialchars($student['fine']) ?></td>
                                </tr>
                                <tr>
                                    <th>Borrowed Books</th>
                                    <td>
                                        <?php
                                        $borrowedBooks = [];
                                        if ($student['book_id1'] !== null) {
                                            $borrowedBooks[] = $student['book_id1'];
                                        }
                                        if ($student['book_id2'] !== null) {
                                            $borrowedBooks[] = $student['book_id2'];
                                        }
                                        echo implode(', ', $borrowedBooks);
                                        ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <!-- /.content-wrapper -->

    <!-- Main Footer -->
    <footer class="main-footer">
        <strong>Copyright &copy; 2023 Your Company.</strong>
        All rights reserved.
        <div class="float-right d-none d-sm-inline-block">
            <b>Version</b> 3.2.0
        </div>
    </footer>
</div>
<!-- REQUIRED SCRIPTS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/js/adminlte.min.js"></script>

</body>
</html>
