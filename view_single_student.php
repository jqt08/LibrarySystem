<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_GET['id'])) {
    $student = getStudentById($_GET['id']);
    if ($student !== null) {
?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Student Details</h3>
    </div>
    <div class="card-body">
        <p><strong>Student Number:</strong> <?php echo htmlspecialchars($student['student_id']); ?></p>
        <p><strong>First Name:</strong> <?php echo htmlspecialchars($student['fname']); ?></p>
        <p><strong>Last Name:</strong> <?php echo htmlspecialchars($student['lname']); ?></p>
        <p><strong>Borrowed Book 1:</strong> <?php echo htmlspecialchars($student['book_id1'] ?? 'None'); ?></p>
        <p><strong>Borrowed Book 2:</strong> <?php echo htmlspecialchars($student['book_id2'] ?? 'None'); ?></p>
        <p><strong>Fine:</strong> <?php echo htmlspecialchars($student['fine']); ?></p>


    </div>
    <div class="card-footer">
        <button type="button" class="btn btn-secondary" onclick="history.back();">Back</button>
    </div>
</div>
<?php
    } else {
        echo '<div class="alert alert-warning">Student not found.</div>';
    }
} else {
    echo '<div class="alert alert-danger">No student ID specified.</div>';
}
?>