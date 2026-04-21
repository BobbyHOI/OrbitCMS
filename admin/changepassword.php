<?php
session_start();
include('../includes/config.php');

// Ensure an admin or manager is logged in
if(strlen($_SESSION['alogin']) == 0) { 
    header('location:../index.php'); 
    exit; 
}

if(isset($_POST['change'])) {
    // 1. First, check if the new password and confirmation match
    if($_POST['newpassword'] !== $_POST['confirm']) {
        $_SESSION['error'] = "The new password and confirmation do not match. Please try again.";
    } else {
        $email = $_SESSION['alogin']; // The email is stored in the session
        $current_password = $_POST['password'];

        // 2. Fetch the current user's data from the database
        $stmt = $dbh->prepare("SELECT Password FROM tblemployees WHERE EmailId = :email");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        // 3. Verify the current password is correct
        if($user && password_verify($current_password, $user->Password)) {
            
            // 4. Hash the new password for secure storage
            $new_password_hash = password_hash($_POST['newpassword'], PASSWORD_DEFAULT);
            
            // 5. Update the password in the database
            $update_stmt = $dbh->prepare("UPDATE tblemployees SET Password = :password WHERE EmailId = :email");
            $update_stmt->execute([
                ':password' => $new_password_hash, 
                ':email' => $email
            ]);

            $_SESSION['msg'] = "Your password has been updated successfully.";

        } else {
            $_SESSION['error'] = "The current password you entered is incorrect.";
        }
    }
    // Redirect back to the same page to show the message
    header('location: changepassword.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin | Change Password</title>
    <?php include('includes/head.php'); ?>
    <script>
        // Client-side validation to ensure passwords match before submission
        function validateForm() {
            var newPass = document.forms["passwordForm"]["newpassword"].value;
            var confirmPass = document.forms["passwordForm"]["confirm"].value;
            if (newPass !== confirmPass) {
                alert("New password and confirm password do not match.");
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
    <?php include('includes/header.php');?>
    <?php include('includes/sidebar.php');?>
    <main class="mn-inner">
        <div class="row">
            <div class="col s12">
                <div class="page-title">Change Password</div>
            </div>
            <div class="col s12 m8 l6">
                <div class="card">
                    <div class="card-content">
                        <form name="passwordForm" method="post" onsubmit="return validateForm();">
                            <div class="row">
                                <div class="input-field col s12">
                                    <input id="password" name="password" type="password" class="validate" required>
                                    <label for="password">Current Password</label>
                                </div>
                                <div class="input-field col s12">
                                    <input id="newpassword" name="newpassword" type="password" class="validate" required>
                                    <label for="newpassword">New Password</label>
                                </div>
                                <div class="input-field col s12">
                                    <input id="confirm" name="confirm" type="password" class="validate" required>
                                    <label for="confirm">Confirm New Password</label>
                                </div>
                                <div class="input-field col s12">
                                    <button type="submit" name="change" class="waves-effect waves-light btn indigo m-b-xs">Update Password</button>
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