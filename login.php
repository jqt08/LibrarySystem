<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

$login_error = '';

// Check if the user is already logged in
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header('Location: index.php');
    exit;
}

// Define administrator librarian IDs and passwords
$admin_accounts = [
    '1' => 'adminpassword', // Example plain text password for admin1_id
];

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if role, ID, and password are set
    if (!isset($_POST['role'], $_POST['password'])) {
        $login_error = 'Incomplete form data.';
    } else {
        // Database connection details 
        $host = 'localhost';
        $db_name = 'librarysystem';
        $username_db = 'root';
        $password_db = '';

        // Connect to the database
        try {
            $pdo = new PDO("mysql:host=$host;dbname=$db_name", $username_db, $password_db);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Determine the role and prepare SQL accordingly
            $role = $_POST['role'];
            $password = $_POST['password'];

            if ($role === 'student' && isset($_POST['student_id'])) {
                $student_id = $_POST['student_id'];
                $stmt = $pdo->prepare("SELECT * FROM student WHERE student_id = :id");
                $stmt->bindParam(':id', $student_id);
            } elseif ($role === 'librarian' && isset($_POST['librarian_id'])) {
                $librarian_id = $_POST['librarian_id'];
                $stmt = $pdo->prepare("SELECT * FROM librarian WHERE librarian_id = :id");
                $stmt->bindParam(':id', $librarian_id);
            } else {
                $login_error = 'Incomplete form data.';
                throw new Exception($login_error);
            }

            // Execute the statement and fetch the user data
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verify password if user exists
            if ($user && $password === $user['password']) {
                // Check if librarian is an admin based on predefined admin IDs and passwords
                if ($role === 'librarian' && array_key_exists($librarian_id, $admin_accounts) && $password === $admin_accounts[$librarian_id]) {
                    // This librarian is an admin
                    $_SESSION['admin'] = true;
                }

                $_SESSION['loggedin'] = true;
                $_SESSION['role'] = $role;
                $_SESSION['id'] = ($role === 'student') ? $student_id : $librarian_id;

                // Set student's first name in session
                if ($role === 'student') {
                    $_SESSION['fname'] = $user['fname'];
                    $_SESSION['student_id'] = $user['student_id'];
                }

                // Redirect based on role
                if ($role === 'student') {
                    header('Location: student.php');
                } else {
                    header('Location: index.php');
                }
                exit;
            } else {
                $login_error = 'Incorrect ID or password.';
            }
        } catch (PDOException $e) {
            $login_error = "Database error: " . $e->getMessage();
        } catch (Exception $e) {
            $login_error = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/css/adminlte.min.css">
    <style>
        .hidden {
            display: none;
        }
    </style>
</head>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="login-logo">
        <a href="#"><b>Login</b> Page</a>
    </div>
    <!-- Login form -->
    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg">Sign in to start your session</p>

            <?php if ($login_error != ''): ?>
            <p class="text-danger"><?= $login_error ?></p>
            <?php endif; ?>

            <form action="login.php" method="post">
                <div class="form-group">
                    <label for="role">Select Role</label>
                    <select id="role" name="role" class="form-control" required onchange="toggleRoleInputs()">
                        <option value="">Select Role</option>
                        <option value="librarian">Librarian</option>
                        <option value="student">Student</option>
                    </select>
                </div>
                <div class="input-group mb-3 hidden" id="librarian_id_input">
                    <input type="text" name="librarian_id" class="form-control" placeholder="Librarian ID">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-id-card"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3 hidden" id="student_id_input">
                    <input type="text" name="student_id" class="form-control" placeholder="Student ID">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-id-card"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                <div class="col-6">
                        <a href="signup.php" class="btn btn-primary btn-block">Sign Up</a>
                    </div>
                    <div class="col-6">
                        <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/js/adminlte.min.js"></script>
<script>
    function toggleRoleInputs() {
        var role = document.getElementById('role').value;
        var librarianIdInput = document.getElementById('librarian_id_input');
        var studentIdInput = document.getElementById('student_id_input');

        if (role === 'student') {
            studentIdInput.classList.remove('hidden');
            studentIdInput.querySelector('input').required = true;
            librarianIdInput.classList.add('hidden');
            librarianIdInput.querySelector('input').required = false;
        } else if (role === 'librarian') {
            librarianIdInput.classList.remove('hidden');
            librarianIdInput.querySelector('input').required = true;
            studentIdInput.classList.add('hidden');
            studentIdInput.querySelector('input').required = false;
        } else {
            librarianIdInput.classList.add('hidden');
            librarianIdInput.querySelector('input').required = false;
            studentIdInput.classList.add('hidden');
            studentIdInput.querySelector('input').required = false;
        }
    }
</script>
</body>
</html>
