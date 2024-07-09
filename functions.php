<?php
require_once 'db.php'; 

// ADDING a book on library; accessed only by: librarian
if (!function_exists('addBook')) {
    // Add a book in the db
    function addBook($title, $publication_date, $category) {
        global $conn;
        $sql = "INSERT INTO book (title, publication_date, category) VALUES ('$title', '$publication_date', '$category')"; // status is optional
        if ($conn->query($sql) === TRUE){
            return true;
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
            return false;
        }
    }
}
// DISPLAYING book on library accessed only by: librarian
if (!function_exists('getBook')) {
    function getBook() {
        global $conn;
        $sql = "SELECT * FROM book";
        $result = $conn->query($sql);
        $books = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $books[] = $row;
            }
        }
        return $books;
    }
}
// EDITING a book on library; accessed only by: librarian
if (!function_exists('editBook')) {
    function editBook($book_id, $title, $publication_date, $category) {
        global $conn;
        $sql = "UPDATE book SET title='$title', publication_date='$publication_date', category='$category' WHERE book_id = '$book_id'";
        if ($conn->query($sql) === TRUE) {
            return true;
        } else {
            echo "Error editing book details: " . $conn->error;
            return false;
        }
    }
}

if (!function_exists('getBookById')) {
    // Get a single book by ID from the database
    function getBookById($id) {
        global $conn;
        $sql = "SELECT * FROM book WHERE book_id = '$id'";
        $result = $conn->query($sql);
        
        if ($result->num_rows == 1) {
            return $result->fetch_assoc();
        } else {
            return null;
        }
    }
}
// DELETING a book on library; accessed only by: librarian
if (!function_exists('deleteBook')) {
    function deleteBook($id) {
        global $conn;
        $sql = "DELETE FROM book WHERE book_id = '$id'";
        if ($conn->query($sql) === TRUE) {
            return true;
        } else {
            echo "Error deleting record: " . $conn->error;
            return false;
        }

    }
}

// ADDING a student on library; accessed only by: librarian
if (!function_exists('addStudent')) {
    function addStudent($student_id, $fname, $lname, $password) {
        global $conn;
        $sql = "INSERT INTO student (student_id, fname, lname, password) VALUES ('$student_id', '$fname', '$lname', '$password')";
        if ($conn->query($sql) === TRUE){
            return true;
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
            return false;
        }
    }
}


// DISPLAYING student on library accessed only by: librarian
if (!function_exists('getStudent')) {
    function getStudent() {
        global $conn;
        $sql = "SELECT * FROM student";
        $result = $conn->query($sql);
        $students = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $students[] = $row;
            }
        }
        return $students;
    }
}

if (!function_exists('getStudentById')) {
    // Get a single student by ID from the database
    function getStudentById($id) {
        global $conn;
        $sql = "SELECT * FROM student WHERE student_id = '$id'";
        $result = $conn->query($sql);
        
        if ($result->num_rows == 1) {
            return $result->fetch_assoc();
        } else {
            return null;
        }
    }
}

// EDITING a student on library; accessed only by: librarian
if (!function_exists('editStudent')) {
    function editStudent($student_id, $fname, $lname, $password) {
        global $conn;
        $sql = "UPDATE student SET fname='$fname', lname='$lname', password='$password' WHERE student_id = '$student_id'";
        if ($conn->query($sql) === TRUE) {
            return true;
        } else {
            echo "Error editing student details: " . $conn->error;
            return false;
        }
    }
}

// DELETING a student on library; accessed only by: librarian
if (!function_exists('deleteStudent')) {
    function deleteStudent($id) {
        global $conn;
        $sql = "DELETE FROM student WHERE student_id = '$id'";
        if ($conn->query($sql) === TRUE) {
            return true;
        } else {
            echo "Error deleting student: " . $conn->error;
            return false;
        }

    }
}
if (!function_exists('addLibrarian')) {
    function addLibrarian($librarian_id, $fname, $lname, $password) {
        // Database connection details (replace with your actual database credentials)
        $host = 'localhost';
        $db_name = 'librarysystem';
        $username_db = 'root';
        $password_db = '';

        try {
            // Connect to the database
            $pdo = new PDO("mysql:host=$host;dbname=$db_name", $username_db, $password_db);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Prepare SQL statement with parameterized query to prevent SQL injection
            $stmt = $pdo->prepare("INSERT INTO librarian (librarian_id, fname, lname, password) VALUES (:librarian_id, :fname, :lname, :password)");

            // Bind parameters
            $stmt->bindParam(':librarian_id', $librarian_id);
            $stmt->bindParam(':fname', $fname);
            $stmt->bindParam(':lname', $lname);
            $stmt->bindParam(':password', $password);

            // Execute the statement
            $stmt->execute();

            return true; // Successful insertion
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false; // Failed insertion
        }
    }
}

if (!function_exists('archiveBook')) {
    function archiveBook($bookId) {
        // Database connection details (replace with your actual database credentials)
        $host = 'localhost';
        $db_name = 'librarysystem';
        $username_db = 'root';
        $password_db = '';

        try {
            // Connect to the database
            $pdo = new PDO("mysql:host=$host;dbname=$db_name", $username_db, $password_db);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Prepare SQL statement to update book status to 'archived'
            $stmt = $pdo->prepare("UPDATE book SET status = 'archived' WHERE book_id = :book_id");

            // Bind parameter
            $stmt->bindParam(':book_id', $bookId);

            // Execute the statement
            $stmt->execute();

            return true; // Successful update
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false; // Failed update
        }
    }
}

if (!function_exists('unarchiveBook')) {
    function unarchiveBook($bookId) {
        // Database connection details (replace with your actual database credentials)
        $host = 'localhost';
        $db_name = 'librarysystem';
        $username_db = 'root';
        $password_db = '';

        try {
            // Connect to the database
            $pdo = new PDO("mysql:host=$host;dbname=$db_name", $username_db, $password_db);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Prepare SQL statement to update book status to 'available'
            $stmt = $pdo->prepare("UPDATE book SET status = 'available' WHERE book_id = :book_id");

            // Bind parameter
            $stmt->bindParam(':book_id', $bookId);

            // Execute the statement
            $stmt->execute();

            return true; // Successful update
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false; // Failed update
        }
    }
}
// Function to set session alerts
if (!function_exists('setAlert')) {
    function setAlert($message, $type = 'success') {
        // Set session alert message
        $_SESSION['alert'] = ['message' => $message, 'type' => $type];
    }
}
?>
