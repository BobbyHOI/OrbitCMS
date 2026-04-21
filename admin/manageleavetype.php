<?php
/**
 * OrbitCMS - Manage Leave Types
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

// Handle deletion requests
if (isset($_GET['delid'])) {
    $leaveTypeController = new LeaveTypeController();
    $leaveTypeController->delete();
}

// Fetch all leave types for display
$leaveTypeModel = new LeaveType();
$leaveTypes = $leaveTypeModel->getAll();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin | Manage Leave Types</title>
    <?php include('includes/head.php'); ?>
</head>
<body>
    <?php include('includes/header.php'); ?>
    <?php include('includes/sidebar.php'); ?>
    <main class="mn-inner">
        <div class="row">
            <div class="col s12">
                <div class="page-title">Manage Leave Types</div>
            </div>
            <div class="col s12 m12 l12">
                <div class="card">
                    <div class="card-content">
                        <span class="card-title">Leave Types</span>
                        <a href="addleavetype.php" class="waves-effect waves-light btn indigo m-b-xs right">Add New</a>
                        <table id="example" class="display responsive-table ">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Leave Type</th>
                                    <th>Description</th>
                                    <th>Leave Limit</th>
                                    <th>Creation Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $cnt = 1;
                                if($leaveTypes) {
                                    foreach($leaveTypes as $result) { ?>
                                <tr>
                                    <td><?php echo htmlentities($cnt);?></td>
                                    <td><?php echo htmlentities($result->LeaveType);?></td>
                                    <td><?php echo htmlentities($result->Description);?></td>
                                    <td><?php echo ($result->LeaveLimit > 0) ? htmlentities($result->LeaveLimit) : 'Unlimited'; ?></td>
                                    <td><?php echo htmlentities($result->CreationDate);?></td>
                                    <td>
                                        <a href="editleavetype.php?lid=<?php echo htmlentities($result->id);?>"><i class="material-icons">mode_edit</i></a>
                                        <a href="manageleavetype.php?delid=<?php echo htmlentities($result->id);?>" onclick="return confirm('Are you sure you want to delete this leave type?');"><i class="material-icons">delete_forever</i></a>
                                    </td>
                                </tr>
                                <?php $cnt++;} }?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php include('includes/footer.php'); ?>
</body>
</html>