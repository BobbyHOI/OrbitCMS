<?php
session_start();
error_reporting(0);
include('includes/config.php');
include('sendmail.php');

global $dbh, $msg, $error;

if(isset($_GET['email']) && isset($_GET['hash'])) {
    $email = $_GET['email'];
    $hash = $_GET['hash'];

    try {
        // Fetch the employee record
        $stmt = $dbh->prepare("SELECT id, Status, FirstName, LastName FROM tblemployees WHERE EmailId=? AND hash=?");
        $stmt->execute([$email, $hash]);
        $employee = $stmt->fetch(PDO::FETCH_OBJ);

        if($employee) {
            // If status is 4 (Pending user verification), proceed with activation
            if($employee->Status == 4) {
                // Update employee status to 0 (Pending admin approval)
                $dbh->prepare("UPDATE tblemployees SET Status=0 WHERE id=?")->execute([$employee->id]);
                
                $msg = "Email verified successfully. An administrator has been notified to approve your account.";

                // === ADMIN NOTIFICATION LOGIC ===

                // 1. Fetch email settings from the database
                $settings = $dbh->query("SELECT SettingKey, SettingValue FROM tblsettings WHERE SettingKey IN ('admin_email', 'manager_emails', 'cc_emails', 'company_name')")->fetchAll(PDO::FETCH_KEY_PAIR);
                
                $adminEmail = $settings['admin_email'] ?? 'admin@orbitcms.com';
                $managerBcc = $settings['manager_emails'] ?? null;
                $ccList = $settings['cc_emails'] ?? null;
                $companyName = $settings['company_name'] ?? 'OrbitCMS';

                // 2. Create the approval and rejection links using BASE_URL
                $approveLink = BASE_URL . 'admin/validate_employee.php?email=' . urlencode($email) . '&new_status=1';
                $rejectLink = BASE_URL . 'admin/validate_employee.php?email=' . urlencode($email) . '&new_status=2';

                // 3. Construct the email body
                $htmlStr = "";
                $htmlStr .= "Hi Admin,<br /><br />";
                $htmlStr .= "A new employee has registered and verified their email address.<br /><br />";
                $htmlStr .= "<b>Name:</b> " . htmlspecialchars($employee->FirstName) . " " . htmlspecialchars($employee->LastName) . "<br />";
                $htmlStr .= "<b>Email:</b> " . htmlspecialchars($email) . "<br /><br />";
                $htmlStr .= "Please take an action by clicking one of the buttons below:<br /><br />";
                $htmlStr .= "<a href='{$approveLink}' target='_blank' style='padding:1em; font-weight:bold; background-color:green; color:#fff; text-decoration:none; margin-right: 10px;'>APPROVE REGISTRATION</a>";
                $htmlStr .= "<a href='{$rejectLink}' target='_blank' style='padding:1em; font-weight:bold; background-color:red; color:#fff; text-decoration:none;'>REJECT REGISTRATION</a><br /><br />";
                $htmlStr .= "Kind regards,<br />";
                $htmlStr .= "The {$companyName} Team";

                // 4. Send the email
                sendmail(
                    "New Employee Registration Pending Approval", 
                    $adminEmail, 
                    "no-reply@orbitcms.com", 
                    $htmlStr, 
                    "Admin",
                    $ccList,
                    $managerBcc
                );

            } elseif ($employee->Status == 0) {
                $msg = "This email has already been verified. Please await administrator approval.";
            } else {
                $msg = "This account has already been activated or is invalid.";
            }
        } else {
            $error = "Invalid verification link. Please check your link or contact support.";
        }
    } catch (PDOException $e) {
        $error = "A database error occurred. Please try again later.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>OrbitCMS | Email Verification</title>
    <?php include 'includes/head.php'; ?>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header"><h2>Account Verification</h2></div>
                    <div class="card-body">
                        <?php if($msg){?><div class="alert alert-success"><strong>Success:</strong> <?php echo htmlentities($msg); ?></div><?php } ?>
                        <?php if($error){?><div class="alert alert-danger"><strong>Error:</strong> <?php echo htmlentities($error); ?></div><?php } ?>
                        <p>You can now close this window or return to the login page.</p>
                        <a href="index.php" class="btn btn-primary">Login Page</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include 'includes/footer.php'; ?>
</body>
</html>