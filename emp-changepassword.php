<?php
/**
 * OrbitCMS - Employee Security
 */
session_start();
error_reporting(0);
include('includes/config.php');

if(strlen($_SESSION['emplogin']) == 0) {   
    header('location:index.php');
    exit;
}

if(isset($_POST['change'])) {
    $password = $_POST['password'];
    $newpassword = password_hash($_POST['newpassword'], PASSWORD_DEFAULT);
    $email = $_SESSION['emplogin'];

    try {
        // Professional Verification
        $stmt = $dbh->prepare("SELECT Password FROM tblemployees WHERE EmailId=:email");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        if($user && password_verify($password, $user->Password)) {
            $dbh->prepare("UPDATE tblemployees SET Password=:pw WHERE EmailId=:email")->execute([':pw'=>$newpassword, ':email'=>$email]);
            $msg = "Password updated successfully";
        } else {
            $error = "The current password entered is incorrect.";
        }
    } catch (PDOException $e) { $error = "Update failed: " . $e->getMessage(); }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Employee | Security</title>
    <?php include('includes/head.php');?>
</head>
<body>
    <?php include('includes/header.php');?>
    <?php include('includes/sidebar.php');?>
    
    <main class="mn-inner">
        <div class="row">
            <div class="col s12"><div class="page-title">Account Security</div></div>
            <div class="col s12 m12 l6">
                <div class="card">
                    <div class="card-content">
                        <span class="card-title">Update Your Password</span>
                        <?php if($error){?><div class="errorWrap"><strong>Error</strong>: <?php echo htmlentities($error); ?> </div><?php } 
                        else if($msg){?><div class="succWrap"><strong>Success</strong>: <?php echo htmlentities($msg); ?> </div><?php }?>

                        <form method="post" name="chngpwd" onsubmit="return valid();">
                            <div class="row">
                                <div class="input-field col s12">
                                    <input id="password" type="password" name="password" required>
                                    <label for="password">Current Password</label>
                                </div>
                                <div class="input-field col s12">
                                    <input id="newpassword" type="password" name="newpassword" required>
                                    <label for="newpassword">New Password</label>
                                </div>
                                <div class="input-field col s12">
                                    <input id="confirmpassword" type="password" name="confirmpassword" required>
                                    <label for="confirmpassword">Confirm New Password</label>
                                </div>
                                <div class="input-field col s12">
                                    <button type="submit" name="change" class="btn blue waves-effect waves-light">UPDATE SECURITY</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include('includes/footer.php');?>
    <script>
        function valid() {
            if(document.chngpwd.newpassword.value != document.chngpwd.confirmpassword.value) {
                alert("The new passwords do not match!");
                return false;
            }
            return true;
        }
    </script>
</body>
</html>