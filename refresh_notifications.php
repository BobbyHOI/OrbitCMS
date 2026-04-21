<?php
/**
 * OrbitCMS - Notification Refresher
 */

session_start();
include('includes/config.php');

// Security check: Ensure a user is logged in
if(strlen($_SESSION['emplogin']) == 0) { 
    http_response_code(401); // Unauthorized
    exit;
}

// Recalculate unread leaves
$unread_count = 0;
try {
    if(isset($dbh)) {
        $unread_count = $dbh->query("SELECT count(id) FROM tblleaves WHERE IsRead=0")->fetchColumn();
    }
} catch(Exception $e) {
    // Log error if needed, but don't crash the script
}

// Update the session variable
$_SESSION['unread_leaves'] = $unread_count;

// Check if this is an AJAX request or a manual click
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    // It's an AJAX request, return the new count as JSON
    header('Content-Type: application/json');
    echo json_encode(['unread_count' => $unread_count]);
} else {
    // It's a manual click, redirect back to the previous page
    header('Location: ' . $_SERVER['HTTP_REFERER']);
}

exit;
?>