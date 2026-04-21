<?php

session_start();
require_once(__DIR__ . '/../includes/config.php');
require_once(__DIR__ . '/../sendmail.php');

// Check if the user is logged in as an administrator
if (empty($_SESSION['alogin'])) {
    header('location: ../index.php');
    exit;
}

$msg = null;
$error = null;

// Validate the request parameters
if (isset($_GET['email'], $_GET['new_status'])) {
    $email = $_GET['email'];
    $newStatus = filter_var($_GET['new_status'], FILTER_VALIDATE_INT);

    if ($newStatus === false || !in_array($newStatus, [1, 2])) {
        $error = "Invalid status provided.";
    } else {
        global $dbh;
        try {
            // Fetch the user's name for the email notification
            $userStmt = $dbh->prepare("SELECT FirstName, LastName FROM tblemployees WHERE EmailId = ?");
            $userStmt->execute([$email]);
            $user = $userStmt->fetch(PDO::FETCH_OBJ);

            if ($user) {
                // Update the user's status
                $updateStmt = $dbh->prepare("UPDATE tblemployees SET Status = ? WHERE EmailId = ?");
                $updateStmt->execute([$newStatus, $email]);

                if ($updateStmt->rowCount() > 0) {
                    $statusText = ($newStatus == 1) ? 'Approved' : 'Rejected';
                    $msg = "Employee account has been successfully {$statusText}.";

                    // Prepare and send the notification email
                    $loginLink = BASE_URL;
                    $subject = "Your OrbitCMS Account has been {$statusText}";
                    
                    $htmlStr = "";
                    $htmlStr .= "Hi " . htmlspecialchars($user->FirstName) . ",<br /><br />";
                    $htmlStr .= "An administrator has reviewed your registration.<br /><br />";
                    $htmlStr .= "<b>Status:</b> Account " . strtoupper($statusText) . "<br /><br />";

                    if ($newStatus == 1) {
                        $htmlStr .= "You may now log in to your account using the link below.<br /><br />";
                        $htmlStr .= "<a href='{$loginLink}' target='_blank' style='padding:1em; font-weight:bold; background-color:#0d47a1; color:#fff; text-decoration:none;'>LOGIN TO YOUR ACCOUNT</a><br /><br />";
                    } else {
                        $htmlStr .= "If you believe this is an error, please contact support.<br /><br />";
                    }
                    
                    $htmlStr .= "Kind regards,<br />";
                    $htmlStr .= "Orbit CMS";

                    sendmail($subject, $email, "no-reply@orbitcms.com", $htmlStr, $user->FirstName);
                } else {
                    $error = "Could not update the employee status. The user may not exist or the status is already set.";
                }
            } else {
                $error = "No employee found with that email address.";
            }
        } catch (PDOException $e) {
            // Log the error in a production environment
            // error_log('Database error in validation controller: ' . $e->getMessage());
            $error = "A database error occurred. Please try again.";
        }
    }
} else {
    $error = "Invalid request. Missing required parameters.";
}

// Set session messages and redirect back to the employee management page
$_SESSION['msg'] = $msg;
$_SESSION['error'] = $error;
header('location: manageemployee.php');
exit;

?>