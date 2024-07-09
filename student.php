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

// Initialize $books as an empty array to avoid unded variable warning
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/css/adminlte.min.css">
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
                <!-- Display all books -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">All Books</h3>
                    </div>
                    <div class="card-body">
                        <?php if (empty($books)): ?>
                            <p>No books found.</p>
                        <?php else: ?>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Title</th>
                                        <th>Publication Date</th>
                                        <th>Category</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($books as $book): ?>
                                    <tr>
                                        <td><?= $book['book_id'] ?></td>
                                        <td><?= $book['title'] ?></td>
                                        <td><?= $book['publication_date'] ?></td>
                                        <td><?= $book['category'] ?></td>
                                        <td><?= $book['status'] ?></td>
                                        <td>

                                        <?php if ($book['status'] === 'available'): ?>
                                        <!-- HTML Form with Borrow Button, Confirm Button, and Hidden Number of Days Input -->
                                        <form id="borrowForm_<?= $book['book_id'] ?>" action="student.php" method="post" style="display: none;">
                                            <input type="hidden" name="book_id" value="<?= htmlspecialchars($book['book_id']) ?>">
                                            <input type="hidden" name="action" value="borrow">
                                            <div class="form-group">
                                                <label for="borrow_days_<?= $book['book_id'] ?>">Number of days:</label>
                                                <input type="number" class="form-control" id="borrow_days_<?= $book['book_id'] ?>" name="borrow_days" placeholder="Enter number of days" required>
                                            </div>
                                            <button type="button" id="confirmButton_<?= $book['book_id'] ?>" class="btn btn-success btn-sm">Confirm</button>
                                        </form>

                                        <!-- Borrow Button to Toggle Display of Borrow Form -->
                                        <button id="borrowButton_<?= $book['book_id'] ?>" class="btn btn-primary btn-sm">Borrow</button>

                                        <script>
                                            // JavaScript to toggle display of borrow form and confirm button for each book
                                            document.getElementById('borrowButton_<?= $book['book_id'] ?>').addEventListener('click', function() {
                                                // Hide the Borrow button
                                                document.getElementById('borrowButton_<?= $book['book_id'] ?>').style.display = 'none';
                                                // Show the Borrow form
                                                document.getElementById('borrowForm_<?= $book['book_id'] ?>').style.display = 'block';
                                            });

                                            // JavaScript to handle confirm button click for each book
                                            document.getElementById('confirmButton_<?= $book['book_id'] ?>').addEventListener('click', function() {
                                                // Ensure number of days is entered
                                                var borrowDays = document.getElementById('borrow_days_<?= $book['book_id'] ?>').value;
                                                if (borrowDays.trim() === '') {
                                                    alert('Please enter the number of days.');
                                                    return;
                                                }

                                                // Submit the form
                                                document.getElementById('borrowForm_<?= $book['book_id'] ?>').submit();
                                            });
                                        </script>

                                    <?php elseif ($book['status'] === 'borrowed' && ($book['book_id'] == $student['book_id1'] || $book['book_id'] == $student['book_id2'])): ?>
                                        <form action="student.php" method="post">
                                            <input type="hidden" name="book_id" value="<?= $book['book_id'] ?>">
                                            <input type="hidden" name="action" value="return">
                                            <button type="submit" class="btn btn-warning btn-sm">Return</button>
                                        </form>
                                    <?php else: ?>
                                        <button class="btn btn-secondary btn-sm" disabled>Unavailable</button>
                                    <?php endif; ?>



                                        </td>
                                    </tr>
                                    <?php endforeach; ?>

                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>
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
