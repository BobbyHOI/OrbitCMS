<?php
/**
 * OrbitCMS - Secure Admin Portal
 */
session_start();
include('../includes/config.php');

// If a session is already active, redirect to the dashboard
if(!empty($_SESSION['alogin'])) {
    header('location:dashboard.php');
    exit;
}

if(isset($_POST['signin'])) {
    $uname = $_POST['username'];
    $password = $_POST['password'];
    
    // The admin portal is accessible to Managers (2) and Administrators (3)
    $sql = "SELECT * FROM tblemployees WHERE EmailId=:uname AND (role=2 OR role=3)";
    $query = $dbh->prepare($sql);
    $query->execute([':uname' => $uname]);
    $result = $query->fetch();

    if($result && password_verify($password, $result->Password)) {
        if($result->Status == 0) {
            $msg = "Your account is currently inactive. Please contact support.";
            echo "<script>alert('". $msg . "');</script>";
        } else {
            // Establish the session
            $_SESSION['alogin'] = $result->EmailId;
            $_SESSION['eid'] = $result->id;
            $_SESSION['role'] = $result->role;
            header('location:dashboard.php');
            exit;
        }
    } else {
        // Use a generic error to prevent revealing valid user accounts
        echo "<script>alert('Invalid credentials. Please try again.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin | Secure Login</title>
    <?php include('includes/head.php');?>
    <style>
        body { background-color: #f5f5f5; display: flex; min-height: 100vh; flex-direction: column; }
        .mn-header nav { background-color: #212121 !important; }
        main { flex: 1 0 auto; display: flex; align-items: center; justify-content: center; }
        .login-box { width: 100%; max-width: 400px; }
    </style>
</head>
<body class="signin-page">
    <header class="mn-header navbar-fixed">
        <nav class="grey darken-4">
            <div class="nav-wrapper row">
                <div class="header-title col s12 center-align" style="display: flex; align-items: center; justify-content: center;">      
                    <img src="../assets/images/logo.png" alt="OrbitCMS" style="height: 40px; margin-right: 10px;">
                    <span class="chapter-title">Admin Access</span>
                </div>
            </div>
        </nav>
    </header>
    <main>
        <div class="container login-box">
            <div class="card white">
                <div class="card-content">
                    <span class="card-title center-align" style="margin-bottom: 30px;">Admin Sign In</span>
                    <form method="post">
                        <div class="input-field col s12">
                            <input id="username" type="text" name="username" class="validate" required autocomplete="off">
                            <label for="username">Email</label>
                        </div>
                        <div class="input-field col s12">
                            <input id="password" type="password" name="password" class="validate" required autocomplete="off">
                            <label for="password">Password</label>
                        </div>
                        <div class="center-align" style="margin-top: 30px;">
                            <button type="submit" name="signin" class="waves-effect waves-light btn blue" style="width: 100%;">Sign in</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
    <?php include('includes/footer.php');?>
</body>
</html>