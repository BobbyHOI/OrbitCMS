<?php
/**
 * OrbitCMS - Admin | Leave History (Refactored)
 */
session_start();
error_reporting(0);
include('includes/config.php');
require_once('../models/Leave.php');

// Authentication check for admin
if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit;
}

// Fetch all leave records using the model
$leaveModel = new Leave();
$leaves = $leaveModel->getAll();

// Check for and clear any session messages
if (isset($_SESSION['msg'])) {
    $msg = $_SESSION['msg'];
    unset($_SESSION['msg']);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin | Total Leave</title>
    <?php include('includes/head.php');?>
    <!-- Page-specific styles -->
    <link href="../assets/plugins/google-code-prettify/prettify.css" rel="stylesheet" type="text/css"/>  
</head>
<body>
    <?php include('includes/header.php');?>
    <?php include('includes/sidebar.php');?>
    
    <main class="mn-inner">
        <div class="row">
            <div class="col s12">
                <div class="page-title">Leave History</div>
            </div>
           
            <div class="col s12 m12 l12">
                <div class="card">
                    <div class="card-content">
                        <span class="card-title">Leave History</span>
                        <?php if(isset($msg)){?><div class="succWrap"><strong>SUCCESS</strong> : <?php echo htmlentities($msg); ?> </div><?php }?>
                        <table id="example" class="display responsive-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th width="200">Employee Name</th>
                                    <th width="120">Leave Type</th>
                                    <th width="180">Posting Date</th>                 
                                    <th>Status</th>
                                    <th align="center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $cnt = 1;
                                if ($leaves) {
                                    foreach ($leaves as $leave) { ?>  
                                        <tr>
                                            <td><b><?php echo htmlentities($cnt);?></b></td>
                                            <td><a href="editemployee.php?empid=<?php echo htmlentities($leave->empid);?>" target="_blank"><?php echo htmlentities($leave->FirstName." ".$leave->LastName);?>(<?php echo htmlentities($leave->EmpId);?>)</a></td>
                                            <td><?php echo htmlentities($leave->LeaveType);?></td>
                                            <td><?php echo htmlentities($leave->PostingDate);?></td>
                                            <td>
                                                <?php 
                                                if ($leave->Status == 1) { echo '<span style="color: green">Approved</span>'; } 
                                                else if ($leave->Status == 2) { echo '<span style="color: red">Not Approved</span>'; } 
                                                else { echo '<span style="color: blue">waiting for approval</span>'; }
                                                ?>
                                            </td>
                                            <td><a href="leave-details.php?leaveid=<?php echo htmlentities($leave->lid);?>" class="waves-effect waves-light btn blue m-b-xs"> View Details</a></td>
                                        </tr>
                                <?php 
                                        $cnt++;
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <?php include('includes/footer.php');?>
    
    <!-- Page-specific Javascripts -->
    <script src="../assets/plugins/jquery-blockui/jquery.blockui.js"></script>
    <script src="../assets/js/pages/table-data.js"></script>
    <script src="assets/js/pages/ui-modals.js"></script>
    <script src="assets-old/plugins/google-code-prettify/prettify.js"></script>
</body>
</html>
