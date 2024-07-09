<?php
require 'functions.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $librarian_id = $_POST['librarian_id'];
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $password = $_POST['password'];

    // Call the addLibrarian function
    if (addLibrarian($librarian_id, $fname, $lname, $password)) {
        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                Librarian added successfully.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
              </div>';
    } else {
        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                Failed to add librarian.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
              </div>';
    }
}
?>
<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title">Add New Librarian</h3>
    </div>
    <form action="index.php?page=add_librarian" method="post">
        <div class="card-body">
            <div class="form-group">
                <label for="librarian_id">Librarian ID</label>
                <input type="text" class="form-control" name="librarian_id" placeholder="Enter librarian ID" required>
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
            <button type="submit" class="btn btn-primary">Add Librarian</button>
        </div>
    </form>
</div>
