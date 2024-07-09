<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'functions.php'; 

$message = '';

$message_success = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                    Student deleted successfully.
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    </div>';

// Check if a valid student ID is passed and deletion has not yet been confirmed
if (isset($_GET['id']) && !isset($_GET['confirm'])) {
    $studentId = htmlspecialchars($_GET['id']);
    $student = getStudentById($studentId); // Retrieve student details

    if ($student) {
        // Display confirmation message
?>
        <div class="container mt-3">
            <h1>Delete Student</h1>
            <p>Are you sure you want to delete this student?</p>
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Student ID: <?= htmlspecialchars($student['student_id']) ?></h5>
                    <p class="card-text">First Name: <?= htmlspecialchars($student['fname']) ?></p>
                    <p class="card-text">Last Name: <?= htmlspecialchars($student['lname']) ?></p>
                </div>
            </div>
            <div>
                <a href="delete_student.php?confirm=yes&id=<?= $studentId; ?>" class="btn btn-danger">Delete</a>
                <a href="index.php?page=view" class="btn btn-secondary">Cancel</a>
            </div>
        </div>
<?php
    } else {
        setAlert("Student not found.", "danger");
        header('Location: index.php?page=view');
        exit();
    }
} elseif (isset($_GET['confirm']) && $_GET['confirm'] == 'yes' && isset($_GET['id'])) {
    // Confirm deletion
    if (deleteStudent($_GET['id'])) {
        $_SESSION['message'] = $message_success;
        setAlert('Student deleted successfully.', 'success');
    } else {
        setAlert('Failed to delete student. Student not found.', 'danger');
    }
    header('Location: index.php?page=view_student'); // Redirect to the student list page
    exit();
} else {
    // No ID was provided or confirmation not 'yes'
    setAlert('No student ID specified or confirmation not provided.', 'danger');
    header('Location: index.php?page=view');
    exit();
}
?>
