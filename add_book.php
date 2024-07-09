<?php
require 'functions.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $publication_date = $_POST['publication_date'];
    $category = $_POST['category'];

    // Call the addBook function
    if (addBook($title, $publication_date, $category)) {
        $message = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        Book added successfully.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
    } else {
        $message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                        Failed to add book.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
    }
}
?>

<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title">Add New Book</h3>
    </div>
    <?php echo $message; ?>

    <form action="index.php?page=add_book" method="post">
        <div class="card-body">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" class="form-control" name="title" placeholder="Enter title" required>
            </div>
            <div class="form-group">
                <label for="publication_date">Publication Date</label>
                <input type="date" class="form-control" name="publication_date" value="<?php echo htmlspecialchars($publication_date); ?>" required>
            </div>

            <div class="form-group">
                <label for="category">Category</label>
                <select class="form-control" name="category" required>
                    <option value="Fiction">Fiction</option>
                    <option value="Non-Fiction">Non-Fiction</option>
                </select>
            </div>

        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Add Book</button>
        </div>
    </form>
</div>
