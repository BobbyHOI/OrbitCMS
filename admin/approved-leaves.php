<?php
/**
 * OrbitCMS - Admin | Approved Leave Requests
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

// Fetch approved leave records using the model
$leaveModel = new Leave();
$leaves = $leaveModel->getByStatus(1); // 1 = Approved

// Check for and clear any session messages
if (isset($_SESSION['msg'])) {
    $msg = $_SESSION['msg'];
    unset($_SESSION['msg']);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin | Approved Leaves</title>
    <?php include('includes/head.php');?>
</head>
<body>
    <?php include('includes/header.php');?>
    <?php include('includes/sidebar.php');?>
    
    <main class="mn-inner">
        <div class="row">
            <div class="col s12">
                <div class="page-title">Approved Leaves</div>
            </div>
           
            <div class="col s12 m12 l12">
                <div class="card">
                    <div class="card-content">
                        <span class="card-title">Approved Leave History</span>
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
                                            <td><span style="color: green;">Approved</span></td>
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
</body>
</html>
