<?php
require 'functions.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['message'])) {
    echo $_SESSION['message'];
    unset($_SESSION['message']); 
}
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">All Students</h3>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Student Number</th>
                    <th>Full Name</th>
            </thead>
            <tbody>
                <?php
                $students = getStudent();
                if (count($students) > 0) {
                    foreach ($students as $student) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($student['student_id']) . "</td>";
                        echo "<td>" . htmlspecialchars(($student['fname']) . ' ' . ($student['lname'])) . "</td>";
                        echo "<td>
                            <a href='index.php?page=edit_student&id={$student['student_id']}' class='btn btn-info'>Edit</a>
                            <a href='index.php?page=delete_student&id={$student['student_id']}' class='btn btn-danger'>Delete</a>
                            <a href='index.php?page=view_single_student&id={$student['student_id']}' class='btn btn-primary'>View Details</a>
                            </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4' class='text-center'>No books found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>