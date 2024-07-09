<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require 'functions.php';

// Handling form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $student_id = $_POST['student_id'];
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $password = $_POST['password'];

    $message_success = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                            Student credentials updated successfully.
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>';

    // Call editStudent function to update student details
    if (editStudent($student_id, $fname, $lname, $password)) {
        $_SESSION['message'] = $message_success;
        header("Location: index.php?page=view_student");
        exit();
    } else {
        echo '<div class="alert alert-danger">Failed to update student.</div>';
    }
}

// Check if student ID is provided via GET parameter
if (isset($_GET['id'])) {
    $student_id = $_GET['id'];
    $student = getStudentById($student_id);
    
    // Display the edit form if student is found
    if ($student !== null) {
?>
<div class="card card-info">
    <div class="card-header">
        <h3 class="card-title">Edit Student</h3>
    </div>
    
    <form action="edit_student.php?id=<?php echo $student['student_id']; ?>" method="post">
        <div class="card-body">
            <input type="hidden" name="student_id" value="<?php echo $student['student_id']; ?>">
            
            <div class="form-group">
                <label for="student_id">Student ID</label>
                <input type="text" class="form-control" name="student_id" value="<?php echo htmlspecialchars($student['student_id']); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="fname">First Name</label>
                <input type="text" class="form-control" name="fname" placeholder="Enter first name" value="<?php echo htmlspecialchars($student['fname']); ?>" required>
            </div>
            <div class="form-group">
                <label for="lname">Last Name</label>
                <input type="text" class="form-control" name="lname" placeholder="Enter last name" value="<?php echo htmlspecialchars($student['lname']); ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" name="password" placeholder="Enter new password" required>
            </div>

            <div class="card-footer">
                <button type="submit" name="submit" class="btn btn-info">Update Student</button>
                <a href="index.php?page=view" class="btn btn-default">Cancel</a>
            </div>
        </div>
    </form>
</div>
<?php
    } else {
        echo '<div class="alert alert-warning">Student not found.</div>';
    }
} else {
    echo '<div class="alert alert-danger">No student ID specified.</div>';
}
?>
