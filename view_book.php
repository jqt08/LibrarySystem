<?php
require 'functions.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['message'])) {
    echo $_SESSION['message'];
    unset($_SESSION['message']); 
}


$books = getBook(); // Assuming getBook() retrieves all books from your database
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">All Books</h3>
    </div>

    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Book ID</th>
                    <th>Title</th>
                    <th>Publication Date</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($books as $book): ?>
                    <tr>
                        <td><?= htmlspecialchars($book['book_id']) ?></td>
                        <td><?= htmlspecialchars($book['title']) ?></td>
                        <td><?= htmlspecialchars($book['publication_date']) ?></td>
                        <td><?= htmlspecialchars($book['category']) ?></td>
                        <td><?= htmlspecialchars($book['status']) ?></td>
                        <td>
                            <?php if ($book['status'] === 'borrowed'): ?>
                                <button class='btn btn-danger' disabled>Borrowed</button>
                                <a href='index.php?page=view_single_book&id=<?= $book['book_id'] ?>' class='btn btn-primary'>View Details</a>
                            <?php elseif ($book['status'] === 'archived'): ?>
                                <a href='archive_book.php?action=unarchive&id=<?= $book['book_id'] ?>' class='btn btn-success'>Unarchive</a>
                                <a href='index.php?page=view_single_book&id=<?= $book['book_id'] ?>' class='btn btn-primary'>View Details</a>
                            <?php else: ?>
                                <a href='index.php?page=edit_book&id=<?= $book['book_id'] ?>' class='btn btn-info'>Edit</a>
                                <a href='index.php?page=delete_book&id=<?= $book['book_id'] ?>' class='btn btn-danger'>Delete</a>
                                <a href='index.php?page=view_single_book&id=<?= $book['book_id'] ?>' class='btn btn-primary'>View Details</a>
                                <a href='archive_book.php?action=archive&id=<?= $book['book_id'] ?>' class='btn btn-warning'>Archive</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
