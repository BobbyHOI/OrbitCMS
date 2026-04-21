<?php
/**
 * OrbitCMS - Edit Leave Type
 */
session_start();
error_reporting(0);
include('../includes/config.php');
require_once('../controllers/LeaveTypeController.php');
require_once('../models/LeaveType.php');

// Authentication check
if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit;
}

// Get Leave Type ID from URL
$lid = intval($_GET['lid']);

// Handle form submission
if(isset($_POST['update'])){
    $leaveTypeController = new LeaveTypeController();
    $leaveTypeController->update();
}

// Fetch current data
$leaveTypeModel = new LeaveType();
$leaveType = $leaveTypeModel->findById($lid);

if(!$leaveType) {
    $_SESSION['error'] = "Leave type not found.";
    header('location:manageleavetype.php');
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin | Edit Leave Type</title>
    <?php include('includes/head.php'); ?>
</head>
<body>
    <?php include('includes/header.php'); ?>
    <?php include('includes/sidebar.php'); ?>
    <main class="mn-inner">
        <div class="row">
            <div class="col s12">
                <div class="page-title">Edit Leave Type</div>
            </div>
            <div class="col s12 m12 l6">
                <div class="card">
                    <div class="card-content">
                        <span class="card-title">Leave Type Details</span><br>
                        <form method="post">
                            <div class="row">
                                <div class="input-field col s12">
                                    <input id="leavetype" type="text" value="<?php echo htmlentities($leaveType->LeaveType);?>" name="leavetype" required>
                                    <label for="leavetype" class="active">Leave Type Name</label>
                                </div>
                                <div class="input-field col s12">
                                    <textarea id="description" name="description" class="materialize-textarea"><?php echo htmlentities($leaveType->Description);?></textarea>
                                    <label for="description" class="active">Description</label>
                                </div>
                                <div class="input-field col s12">
                                    <input id="limit" name="limit" type="number" min="0" value="<?php echo htmlentities($leaveType->LeaveLimit);?>" required>
                                    <label for="limit" class="active">Leave Limit (days)</label>
                                    <span class="helper-text">Enter 0 for an unlimited number of days.</span>
                                </div>
                                <div class="input-field col s12">
                                    <button type="submit" name="update" class="waves-effect waves-light btn indigo m-b-xs">UPDATE</button>
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