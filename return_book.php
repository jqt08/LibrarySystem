<?php
session_start();

// Check if user is logged in and is a student
if (!(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true && $_SESSION['role'] === 'student')) {
    header('Location: login.php'); // Redirect to login page if not logged in as a student
    exit;
}

// Database connection details (replace with your actual database credentials)
$host = 'localhost';
$db_name = 'myfinalproj';
$username_db = 'root';
$password_db = '';

try {
    // Connect to the database
    $pdo = new PDO("mysql:host=$host;dbname=$db_name", $username_db, $password_db);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Handle return action
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $book_id = $_POST['book_id'];

        // Update book status to 'available'
        $updateStmt = $pdo->prepare("UPDATE book SET status = 'available' WHERE book_id = :book_id");
        $updateStmt->bindParam(':book_id', $book_id);
        if ($updateStmt->execute()) {
            // Update student's borrowed books
            $stmt = $pdo->prepare("UPDATE student SET book_id1 = NULL WHERE student_id = :student_id AND book_id1 = :book_id");
            $stmt->bindParam(':book_id', $book_id);
            $stmt->bindParam(':student_id', $_SESSION['student_id']);
            $stmt->execute();

            $stmt = $pdo->prepare("UPDATE student SET book_id2 = NULL WHERE student_id = :student_id AND book_id2 = :book_id");
            $stmt->bindParam(':book_id', $book_id);
            $stmt->bindParam(':student_id', $_SESSION['student_id']);
            $stmt->execute();

            // Redirect to borrowed books page
            header("Location: borrowed_books.php");
            exit();
        } else {
            echo '<script>alert("Failed to return the book. Please try again.");</script>';
        }
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
