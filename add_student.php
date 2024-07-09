<?php
require 'functions.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = $_POST['student_id'];
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $password = $_POST['password']; // Store plain text password

    if (addStudent($student_id, $fname, $lname, $password)) {
        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                Student added successfully.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
              </div>';
    } else {
        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                Failed to add student.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
              </div>';
    }
}
?>

<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title">Add New Student</h3>
    </div>
    <form action="index.php?page=add_student" method="post">
        <div class="card-body">
            <div class="form-group">
                <label for="student_id">Student ID</label>
                <input type="text" class="form-control" name="student_id" placeholder="Enter student id" required>
            </div>
            <div class="form-group">
                <label for="fname">First Name</label>
                <input type="text" class="form-control" name="fname" placeholder="Enter first name" required>
            </div>

            <div class="form-group">
                <label for="lname">Last Name</label>
                <input type="text" class="form-control" name="lname" placeholder="Enter last name" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" name="password" placeholder="Enter password" required>
            </div>

        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Add Student</button>
        </div>
    </form>
</div>
