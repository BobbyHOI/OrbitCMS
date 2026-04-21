<?php
/**
 * OrbitCMS - Secure Password Reset
 */
session_start();
error_reporting(0);
include('includes/config.php');

if(isset($_POST['change'])) {
    $newpassword = password_hash($_POST['newpassword'], PASSWORD_DEFAULT);
    $empid = $_SESSION['reset_empid'];
    try {
        $dbh->prepare("UPDATE tblemployees SET Password=:pw WHERE id=:id")->execute([':pw'=>$newpassword, ':id'=>$empid]);
        $msg = "Password reset successful. You can now login.";
        unset($_SESSION['reset_empid']);
    } catch (PDOException $e) { $error = "Process failed."; }
}

if(isset($_POST['submit'])) {
    $empid = $_POST['empid']; $email = $_POST['emailid'];
    $query = $dbh->prepare("SELECT id FROM tblemployees WHERE EmailId=:email AND EmpId=:empid");
    $query->execute([':email' => $email, ':empid' => $empid]);
    $res = $query->fetch();
    if($res) { $_SESSION['reset_empid'] = $res->id; $showReset = true; } 
    else { $error = "Invalid credentials provided."; }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>OrbitCMS | Recovery</title>
    <?php include('includes/head.php');?>
    <style>
        body { background-color: #f5f5f5; display: flex; min-height: 100vh; flex-direction: column; }
        main { flex: 1 0 auto; display: flex; align-items: center; justify-content: center; }
        .reset-box { width: 100%; max-width: 450px; }
    </style>
</head>
<body>
    <header class="mn-header navbar-fixed">
        <nav class="grey darken-4">
            <div class="nav-wrapper row">
                <div class="header-title col s12 center-align" style="display: flex; align-items: center; justify-content: center;">      
                    <img src="assets/images/logo.png" alt="Logo" style="height: 40px; margin-right: 10px;">
                    <span class="chapter-title">Account Recovery</span>
                </div>
            </div>
        </nav>
    </header>

    <main>
        <div class="container reset-box">
            <div class="card white">
                <div class="card-content">
                    <?php if($error){?><div class="errorWrap"><?php echo $error; ?></div><?php } ?>
                    <?php if($msg){?><div class="succWrap"><?php echo $msg; ?></div><?php }?>

                    <?php if(!isset($showReset)): ?>
                        <span class="card-title center-align">Identity Verification</span>
                        <form method="post">
                            <div class="input-field col s12"><input name="empid" type="text" required><label>Employee ID</label></div>
                            <div class="input-field col s12"><input name="emailid" type="email" required><label>Email Address</label></div>
                            <div class="center-align" style="margin-top:20px;">
                                <a href="index.php" class="btn-flat">Back</a>
                                <button type="submit" name="submit" class="btn blue">Verify</button>
                            </div>
                        </form>
                    <?php else: ?>
                        <span class="card-title center-align">New Password</span>
                        <form method="post">
                            <div class="input-field col s12"><input name="newpassword" type="password" required><label>New Password</label></div>
                            <div class="input-field col s12"><input name="confirmpassword" type="password" required><label>Confirm Password</label></div>
                            <div class="center-align" style="margin-top:20px;">
                                <button type="submit" name="change" class="btn blue">Update Account</button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>
    <?php include('includes/footer.php');?>
</body>
</html>