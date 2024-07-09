<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'functions.php'; 

$message = '';
$message_success = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        Book deleted successfully.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';

// Check if a valid book ID is passed and deletion has not yet been confirmed
if (isset($_GET['id']) && !isset($_GET['confirm'])) {
    $bookId = htmlspecialchars($_GET['id']);
    $book = getBookById($bookId); // Retrieve book details deleteBook

    if ($book) {
?>
        <div class="container mt-3">
            <h1>Delete Book</h1>
            <p>Are you sure you want to delete this book?</p>
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><strong>Title: </strong><?= htmlspecialchars($book['title']) ?></h5>
                    <p class="card-text"><strong>Publication Date: </strong><?= htmlspecialchars($book['publication_date']) ?></p>
                    <p class="card-text"><strong>Category: </strong><?= htmlspecialchars($book['category']) ?></p>
                    <?php if ($book['status'] === 'borrowed'): ?>
                        <p class="text-danger">This book is currently borrowed by a student and cannot be deleted.</p>
                    <?php endif; ?>
                </div>
            </div>
            <div>
                <?php if ($book['status'] !== 'borrowed'): ?>
                    <a href="delete_book.php?confirm=yes&id=<?= $bookId; ?>" class="btn btn-danger">Delete</a>
                <?php endif; ?>
                <a href="index.php?page=view_book" class="btn btn-secondary">Cancel</a>
            </div>
        </div>
<?php
    } else {
        setAlert("Book not found.", "danger");
        header('Location: index.php?page=view_book');
        exit();
    }
} elseif (isset($_GET['confirm']) && $_GET['confirm'] == 'yes' && isset($_GET['id'])) {
    // Confirm deletion
    $bookId = $_GET['id'];
    $book = getBookById($bookId); // Retrieve book details again to verify status

    if ($book && $book['status'] !== 'borrowed') {
        if (deleteBook($bookId)) {

            $_SESSION['message'] = $message_success;
            
            setAlert('Book deleted successfully.', 'success');
        } else {
            setAlert('Failed to delete book. Please try again.', 'danger');
        }
    } else {
        setAlert('Cannot delete the book because it is currently borrowed by a student.', 'danger');
    }
    header('Location: index.php?page=view_book'); // Redirect to the book list page
    exit();
} else {
    // No ID was provided 
    setAlert('No book ID specified.', 'danger');
    header('Location: index.php?page=view_book');
    exit();
}
?>
