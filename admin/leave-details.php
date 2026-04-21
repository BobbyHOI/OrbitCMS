<?php
/**
 * OrbitCMS - Admin | Leave Details
 */
session_start();
error_reporting(0); // Note: Should be E_ALL in development

require_once(__DIR__ . '/../includes/config.php');
require_once(__DIR__ . '/../models/Leave.php');
require_once(__DIR__ . '/../controllers/LeaveController.php');

// 1. Authentication & Authorization
if (strlen($_SESSION['alogin']) == 0) {   
    header('location:../index.php');
    exit;
}

$leaveId = filter_input(INPUT_GET, 'leaveid', FILTER_VALIDATE_INT);
if (!$leaveId) {
    header('location:leaves.php');
    exit;
}

// 2. Instantiate Model & Controller
$leaveModel = new Leave();
$leaveController = new LeaveController();

// 3. Handle Form Submission (POST request)
if (isset($_POST['update'])) {
    $status = filter_input(INPUT_POST, 'status', FILTER_VALIDATE_INT);
    $remark = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);

    if ($status && $remark) {
        $result = $leaveController->reviewLeaveRequest($leaveId, $status, $remark);
        if ($result['success']) {
            $msg = $result['message'];
        } else {
            $error = $result['message'];
        }
    } else {
        $error = "Invalid form submission.";
    }
}

// 4. Fetch Data for View
$leaveModel->markLeaveAsReadById($leaveId); // Mark notification as read
$leaveDetails = $leaveModel->findById($leaveId); // Fetch the full leave details

// Redirect if leave not found
if (!$leaveDetails) {
    header('location:leaves.php');
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin | Leave Details #<?php echo $leaveId; ?></title>
    <?php include('includes/head.php');?>
    <style>
        .data-item { padding: 15px 0; border-bottom: 1px solid #f0f0f0; }
        .data-label { font-weight: bold; color: #777; font-size: 13px; text-transform: uppercase; }
        .data-value { font-size: 16px; color: #333; }
        .remark-box { background: #f9f9f9; padding: 20px; border-radius: 4px; margin-top: 20px; border-left: 4px solid #2196F3; }
    </style>
</head>
<body>
    <?php include('includes/header.php');?>
    <?php include('includes/sidebar.php');?>
    <main class="mn-inner">
        <div class="row">
            <div class="col s12"><div class="page-title">Application #<?php echo htmlentities($leaveDetails->lid); ?></div></div>
            <div class="col s12 m12 l10 offset-l1">
                <div class="card">
                    <div class="card-content">
                        <?php if(isset($msg)){?><div class="succWrap"><strong>Success</strong>: <?php echo htmlentities($msg); ?></div><?php } ?>
                        <?php if(isset($error)){?><div class="errorWrap"><strong>Error</strong>: <?php echo htmlentities($error); ?></div><?php } ?>

                        <div class="row">
                            <div class="col s12 m6 data-item"><div class="data-label">Employee</div><div class="data-value"><?php echo htmlentities($leaveDetails->FirstName . " " . $leaveDetails->LastName); ?> (<?php echo htmlentities($leaveDetails->EmpId); ?>)</div></div>
                            <div class="col s12 m6 data-item"><div class="data-label">Leave Type</div><div class="data-value"><?php echo htmlentities($leaveDetails->LeaveType); ?></div></div>
                            <div class="col s12 m6 data-item"><div class="data-label">Duration</div><div class="data-value"><?php echo htmlentities($leaveDetails->FromDate); ?> to <?php echo htmlentities($leaveDetails->ToDate); ?> (<?php echo htmlentities($leaveDetails->DaysRequested); ?> days)</div></div>
                            <div class="col s12 m6 data-item"><div class="data-label">Status</div><div class="data-value">
                                <?php if($leaveDetails->Status==1) echo '<span class="green-text">APPROVED</span>'; elseif($leaveDetails->Status==2) echo '<span class="red-text">REJECTED</span>'; else echo '<span class="blue-text">PENDING</span>'; ?>
                            </div></div>
                            <div class="col s12 data-item" style="border:none;"><div class="data-label">Reason</div><div class="data-value" style="margin-top:10px;"><?php echo nl2br(htmlentities($leaveDetails->Description)); ?></div></div>
                        </div>

                        <?php if($leaveDetails->AdminRemark) { ?>
                            <div class="remark-box">
                                <div class="data-label">Admin Remark</div>
                                <p><?php echo nl2br(htmlentities($leaveDetails->AdminRemark)); ?></p>
                                <small style="color:#999;">Processed on: <?php echo htmlentities($leaveDetails->AdminRemarkDate); ?></small>
                            </div>
                        <?php } ?>

                        <?php if($leaveDetails->Status == 0) { // Show form only if pending ?>
                            <div class="row" style="margin-top:40px;">
                                <form method="post">
                                    <div class="col s12 m4"><label>Set Decision</label><select name="status" class="browser-default" required><option value="" disabled selected>Select...</option><option value="1">Approve</option><option value="2">Reject</option></select></div>
                                    <div class="input-field col s12"><textarea name="description" class="materialize-textarea" placeholder="Provide a reason for this decision (required)" required></textarea></div>
                                    <div class="col s12 right-align"><button type="submit" name="update" class="btn blue">SUBMIT RESPONSE</button></div>
                                </form>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php include('includes/footer.php');?>
</body>
</html>
