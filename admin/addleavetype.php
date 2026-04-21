<?php
/**
 * OrbitCMS - Add Leave Type
 */
session_start();
error_reporting(0);
include('../includes/config.php');
require_once('../controllers/LeaveTypeController.php');

// Authentication check
if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit;
}

// The controller will handle the POST request, validation, and redirection.
$leaveTypeController = new LeaveTypeController();
$leaveTypeController->create();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin | Add Leave Type</title>
    <?php include('includes/head.php'); ?>
</head>
<body>
    <?php include('includes/header.php'); ?>
    <?php include('includes/sidebar.php'); ?>
    <main class="mn-inner">
        <div class="row">
            <div class="col s12">
                <div class="page-title">Add Leave Type</div>
            </div>
            <div class="col s12 m12 l6">
                <div class="card">
                    <div class="card-content">
                        <span class="card-title">Leave Type Details</span><br>
                        <form method="post">
                            <div class="row">
                                <div class="input-field col s12">
                                    <input id="leavetype" type="text" name="leavetype" required>
                                    <label for="leavetype">Leave Type Name</label>
                                </div>
                                <div class="input-field col s12">
                                    <textarea id="description" name="description" class="materialize-textarea"></textarea>
                                    <label for="description">Description</label>
                                </div>
                                <div class="input-field col s12">
                                    <input id="limit" name="limit" type="number" min="0" value="0" required>
                                    <label for="limit" class="active">Leave Limit (days)</label>
                                    <span class="helper-text">Enter 0 for an unlimited number of days.</span>
                                </div>
                                <div class="input-field col s12">
                                    <button type="submit" name="add" class="waves-effect waves-light btn indigo m-b-xs">ADD</button>
                                    <a href="manageleavetype.php" class="waves-effect waves-light btn grey m-b-xs">CANCEL</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php include('includes/footer.php'); ?>
</body>
</html>