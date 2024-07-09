<?php
require 'functions.php';

// Handle archive action if ID is provided
if (isset($_GET['id'])) {
    $bookId = $_GET['id'];
    $action = $_GET['action'] ?? '';

    if ($action === 'archive') {
        if (archiveBook($bookId)) {
            setAlert('Book archived successfully.', 'success');
        } else {
            setAlert('Failed to archive book.', 'danger');
        }
    } elseif ($action === 'unarchive') {
        if (unarchiveBook($bookId)) {
            setAlert('Book unarchived successfully.', 'success');
        } else {
            setAlert('Failed to unarchive book.', 'danger');
        }
    }

    header('Location: index.php?page=view_book');
    exit();
}
?>
