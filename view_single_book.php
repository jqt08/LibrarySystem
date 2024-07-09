<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_GET['id'])) {
    $book = getBookById($_GET['id']);
    if ($book !== null) {
?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Book Details</h3>
    </div>
    <div class="card-body">
        <p><strong>Title:</strong> <?php echo htmlspecialchars($book['title']); ?></p>
        <p><strong>Publication Date:</strong> <?php echo htmlspecialchars($book['publication_date']); ?></p>
        <p><strong>Category:</strong> <?php echo htmlspecialchars($book['category']); ?></p>


    </div>
    <div class="card-footer">
        <button type="button" class="btn btn-secondary" onclick="history.back();">Back</button>
    </div>
</div>
<?php
    } else {
        echo '<div class="alert alert-warning">Book not found.</div>';
    }
} else {
    echo '<div class="alert alert-danger">No book ID specified.</div>';
}
?>