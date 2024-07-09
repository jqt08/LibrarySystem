<?php
session_start();

// Database connection details 
$host = 'localhost';
$db_name = 'librarysystem';
$username_db = 'root';
$password_db = '';

// Initialize variables
$fname = '';
$lname = '';
$password = '';
$student_id = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input
    $student_id = filter_input(INPUT_POST, 'student_id', FILTER_SANITIZE_STRING);
    $fname = filter_input(INPUT_POST, 'fname', FILTER_SANITIZE_STRING);
    $lname = filter_input(INPUT_POST, 'lname', FILTER_SANITIZE_STRING);
    $password = $_POST['password']; // Store plain text password

    if (empty($student_id) || empty($fname) || empty($lname) || empty($password)) {
        echo "All fields are required.";
        exit;
    }

    try {
        // Connect to the database
        $pdo = new PDO("mysql:host=$host;dbname=$db_name", $username_db, $password_db);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Insert user into the student table
        $stmt = $pdo->prepare("INSERT INTO student (student_id, fname, lname, password) VALUES (:student_id, :fname, :lname, :password)");
        
        // Bind parameters and execute the statement
        $stmt->bindParam(':student_id', $student_id);
        $stmt->bindParam(':fname', $fname);
        $stmt->bindParam(':lname', $lname);
        $stmt->bindParam(':password', $password);

        $stmt->execute();

        // Redirect to login page after successful signup
        header('Location: login.php');
        exit;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sign Up</title>
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
        <a href="#"><b>Sign Up</b> Page</a>
    </div>
    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg">Create your account</p>

            <form action="signup.php" method="post">
                <div class="input-group mb-3">
                    <input type="text" name="student_id" class="form-control" placeholder="Student ID" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-id-card"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="text" name="fname" class="form-control" placeholder="First Name" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-user"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="text" name="lname" class="form-control" placeholder="Last Name" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-user"></span>
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
                    <!-- Login link -->
                    <div class="col">
                        <a href="login.php" class="btn btn-primary btn-block">Login</a>
                    </div>
                    <div class="col">
                        <button type="submit" class="btn btn-primary btn-block">Sign Up</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/js/adminlte.min.js"></script>
</body>
</html>

