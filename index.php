<?php
/**
 * OrbitCMS - Portal Entry
 */
session_start();
include('includes/config.php');

// Redirect if already logged in based on role
if(isset($_SESSION['alogin'])) { // Admin is already logged in
    header('location:admin/dashboard.php');
    exit;
} elseif(isset($_SESSION['emplogin'])) { // Employee/Manager is logged in
    header('location:leavehistory.php');
    exit;
}

if(isset($_POST['signin'])) {
    $uname = $_POST['username'];
    $password = $_POST['password']; 
    
    $sql = "SELECT id, Password, Status, role, FirstName, LastName, EmpId, imageFileName FROM tblemployees WHERE EmailId=:uname";
    $query = $dbh->prepare($sql);
    $query->execute([':uname' => $uname]);
    $result = $query->fetch(PDO::FETCH_OBJ);

    if($result && password_verify($password, $result->Password)) {
        if($result->Status == 0) {
            echo "<script>alert('Your account is not active. Please contact the administrator.');</script>";
        } else {
            // Session details for immediate use
            $_SESSION['eid'] = $result->id;
            $_SESSION['emplogin'] = $uname;

            // Pre-load all necessary user data into the session to avoid repeated queries
            $_SESSION['user_data'] = [
                'name' => htmlentities($result->FirstName . " " . $result->LastName),
                'empid' => htmlentities($result->EmpId),
                'role' => (int)$result->role,
                'img' => !empty($result->imageFileName) && $result->imageFileName !== 'profile-image.png' ? "uploads/" . $result->imageFileName : "assets/images/profile-image.png"
            ];
            
            // Pre-load notification count for the header
            $_SESSION['unread_leaves'] = 0;
            try {
                $_SESSION['unread_leaves'] = $dbh->query("SELECT count(id) FROM tblleaves WHERE IsRead=0")->fetchColumn();
            } catch(Exception $e) { /* Ignore errors, default is 0 */ }

            // Redirect based on user role
            if($result->role == 3) { // ADMIN
                $_SESSION['alogin'] = $uname; // Set admin-specific session
                header('location:admin/dashboard.php');
            } else { // MANAGER (2) or EMPLOYEE (1)
                header('location:myprofile.php'); // Redirect non-admins to their profile
            }
            exit;
        }
    } else {
        echo "<script>alert('Invalid Credentials. Please try again.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>OrbitCMS | Login</title>
    <?php include('includes/head.php');?>
    <style>
        body { background-color: #f5f5f5; display: flex; min-height: 100vh; flex-direction: column; }
        main { flex: 1 0 auto; display: flex; align-items: center; justify-content: center; }
        .login-box { width: 100%; max-width: 400px; }
    </style>
</head>
<body class="signin-page">
    <header class="mn-header navbar-fixed">
        <nav class="grey darken-4">
            <div class="nav-wrapper row">
                <div class="header-title col s12 center-align" style="display: flex; align-items: center; justify-content: center;">      
                    <img src="assets/images/logo.png" alt="Logo" style="height: 40px; margin-right: 10px;">
                    <span class="chapter-title">Portal Access</span>
                </div>
            </div>
        </nav>
    </header>
    <main>
        <div class="container login-box">
            <div class="card white z-depth-1">
                <div class="card-content">
                    <span class="card-title center-align">Sign In</span>
                    <form method="post" autocomplete="off">
                        <div class="input-field col s12">
                            <input id="username" type="email" name="username" class="validate" required>
                            <label for="username">Email ID</label>
                        </div>
                        <div class="input-field col s12">
                            <input id="password" type="password" name="password" class="validate" required>
                            <label for="password">Password</label>
                        </div>
                        <div class="center-align" style="margin-top: 20px;">
                            <button type="submit" name="signin" class="waves-effect waves-light btn-large blue" style="width: 100%;">Sign in</button>
                        </div>
                        <div class="center-align" style="margin-top: 20px; font-size: 13px;">
                            <a href="register.php">Create account</a> | <a href="forgot-password.php">Forgot Password?</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
    <?php include('includes/footer.php');?>
</body>
</html>