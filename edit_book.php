<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require 'functions.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $book_id = $_POST['book_id'];
    $title = $_POST['title'];
    $publication_date = $_POST['publication_date'];
    $category = $_POST['category'];

    $message_success = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                            Book updated successfully.
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>';

    // Call editBook function
    $success = editBook($book_id, $title, $publication_date, $category);

    if ($success) {
        $_SESSION['message'] = $message_success;
        header("Location: index.php?page=view_book");
        exit(); // Ensure no further execution
    } else {
        echo '<div class="alert alert-danger">Failed to update book. Please try again.</div>';
    }
}


if (isset($_GET['edit_book_success']) && $_GET['edit_book_success'] == 'true') {
     echo '<div class="alert alert-success">Book updated successfully.</div>';
     }

// Display form for editing book
if (isset($_GET['id'])) {
    $book = getBookById($_GET['id']);
    if ($book !== null) {
?>
<div class="card card-info">
    <div class="card-header">
        <h3 class="card-title">Edit Book</h3>
    </div>
    <?php echo $message; ?> 
    
    <form action="edit_book.php?id=<?php echo $book['book_id']; ?>" method="post">
        <div class="card-body">
            <input type="hidden" name="book_id" value="<?php echo $book['book_id']; ?>">
            
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" class="form-control" name="title" placeholder="Enter title" value="<?php echo htmlspecialchars($book['title']); ?>" required>
            </div>
            <div class="form-group">
                <label for="publication_date">Publication Date</label>
                <input type="date" class="form-control" name="publication_date" value="<?php echo htmlspecialchars($book['publication_date']); ?>" required>
            </div>
            <div class="form-group">
                <label for="category">Category</label>
                <select class="form-control" name="category" required>
                    <option value="Fiction" <?php if ($book['category'] == 'Fiction') echo 'selected'; ?>>Fiction</option>
                    <option value="Non-Fiction" <?php if ($book['category'] == 'Non-Fiction') echo 'selected'; ?>>Non-Fiction</option>
                </select>
            </div>

            <div class="card-footer">
                <button type="submit" name="submit" class="btn btn-info">Update Book</button>
                <a class="btn btn-default" href="index.php" role="button">Cancel</a>
            </div>
        </div>
    </form>
</div>
<?php
    } else {
        echo '<div class="alert alert-warning">Book not found.</div>';
    }
} else {
    echo '<div class="alert alert-danger">No book ID specified.</div>';
}
?>
