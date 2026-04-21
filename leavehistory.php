<?php
/**
 * OrbitCMS - Leave History
 */
session_start();
error_reporting(0);
include('includes/config.php');
require_once('models/Leave.php');

// Authentication check for employees
if(strlen($_SESSION['emplogin']) == 0) {   
    header('location:index.php');
    exit;
}

// Fetch the employee's leave history using the model
$leaveModel = new Leave();
$leaves = $leaveModel->getByEmployeeId($_SESSION['eid']);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Employee | History</title>
    <?php include('includes/head.php'); ?>
    <link href="assets/plugins/datatables/css/jquery.dataTables.min.css" rel="stylesheet">
</head>
<body>
    <?php include('includes/header.php');?>
    <?php include('includes/sidebar.php');?>
    
    <main class="mn-inner">
        <div class="row">
            <div class="col s12"><div class="page-title">Personal Leave Record</div></div>
            <div class="col s12">
                <div class="card">
                    <div class="card-content">
                        <table id="example" class="display responsive-table highlight">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Leave Type</th>
                                    <th>From</th>
                                    <th>To</th>
                                    <th>Days</th>
                                    <th>Status</th>
                                    <th>Admin Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $cnt = 1;
                                if($leaves) {
                                    foreach($leaves as $leave) { ?>  
                                    <tr>
                                        <td><?php echo $cnt++; ?></td>
                                        <td><?php echo htmlentities($leave->LeaveType); ?></td>
                                        <td><?php echo $leave->FromDate; ?></td>
                                        <td><?php echo $leave->ToDate; ?></td>
                                        <td><?php echo $leave->DaysRequested; ?></td>
                                        <td>
                                            <?php 
                                            if($leave->Status==1) echo '<span class="green-text">Approved</span>';
                                            else if($leave->Status==2) echo '<span class="red-text">Rejected</span>';
                                            else echo '<span class="blue-text">Pending</span>';
                                            ?>
                                        </td>
                                        <td><?php echo $leave->AdminRemark ? htmlentities($leave->AdminRemark) : "<i>---</i>"; ?></td>
                                    </tr>
                                <?php } } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php include('includes/footer.php'); ?>
    <script src="assets/plugins/datatables/js/jquery.dataTables.min.js"></script>
    <script src="assets/js/pages/table-data.js"></script>
</body>
</html>