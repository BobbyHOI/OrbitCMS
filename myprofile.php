<?php
/**
 * OrbitCMS - Employee Profile
 */
session_start();
error_reporting(0);
include('includes/config.php');

if(strlen($_SESSION['emplogin']) == 0) {   
    header('location:index.php');
    exit;
}

$email = $_SESSION['emplogin'];

// Settings & Update Logic
$stmtS = $dbh->query("SELECT SettingValue FROM tblsettings WHERE SettingKey='base_max_leaves'");
$baseMax = (int)$stmtS->fetchColumn();

if(isset($_POST['update'])) {
    try {
        $sql = "UPDATE tblemployees SET FirstName=:fn, LastName=:ln, Gender=:gn, Dob=:db, Department=:dp, Address=:ad, Phonenumber=:ph WHERE EmailId=:em";
        $dbh->prepare($sql)->execute([':fn'=>$_POST['firstName'], ':ln'=>$_POST['lastName'], ':gn'=>$_POST['gender'], ':db'=>$_POST['dob'], ':dp'=>$_POST['department'], ':ad'=>$_POST['address'], ':ph'=>$_POST['mobileno'], ':em'=>$email]);
        
        $img = $_FILES["file"]["name"];
        if(!empty($img)) {
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
            $extension = strtolower(pathinfo($img, PATHINFO_EXTENSION));
            
            // Validate extension
            if(!in_array($extension, $allowed_extensions)) {
                throw new Exception("Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.");
            }

            // Validate MIME type for extra security
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $_FILES["file"]["tmp_name"]);
            finfo_close($finfo);
            $allowed_mimes = ['image/jpeg', 'image/png', 'image/gif'];
            if(!in_array($mime, $allowed_mimes)) {
                throw new Exception("Invalid image content.");
            }

            // Generate a unique filename to prevent overwrites and hide original names
            $new_img_name = md5(time() . $img) . "." . $extension;
            
            if(!is_dir("uploads")) mkdir("uploads", 0755, true);
            
            if(move_uploaded_file($_FILES["file"]["tmp_name"], "uploads/" . $new_img_name)) {
                $dbh->prepare("UPDATE tblemployees SET imageFileName=? WHERE EmailId=?")->execute([$new_img_name, $email]);
            } else {
                throw new Exception("Failed to upload image.");
            }
        }

        // === REFRESH SESSION DATA AFTER UPDATE ===
        $user_stmt = $dbh->prepare("SELECT id, FirstName, LastName, EmpId, role, imageFileName FROM tblemployees WHERE EmailId = ?");
        $user_stmt->execute([$email]);
        $user_data = $user_stmt->fetch(PDO::FETCH_ASSOC);

        if ($user_data) {
            $_SESSION['user_data'] = [
                'name' => $user_data['FirstName'] . ' ' . $user_data['LastName'],
                'empid' => $user_data['EmpId'],
                'role' => (int)$user_data['role'],
                'img' => (!empty($user_data['imageFileName']) && $user_data['imageFileName'] !== 'profile-image.png') ? 'uploads/' . $user_data['imageFileName'] : 'assets/images/profile-image.png'
            ];
            $_SESSION['eid'] = $user_data['id'];
        }
        // =========================================

        $msg = "Profile updated successfully";
    } catch (Exception $e) { $error = "Update failed: " . $e->getMessage(); }
}

$user = $dbh->prepare("SELECT * FROM tblemployees WHERE EmailId=?");
$user->execute([$email]);
$row = $user->fetch();

$total = $baseMax + $row->ExtraLeaves;
$avail = $total - $row->LeavesTaken;
$imgSrc = (!empty($row->imageFileName) && $row->imageFileName !== 'profile-image.png') ? 'uploads/'.$row->imageFileName : "assets/images/profile-image.png";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Employee | My Profile</title>
    <?php include('includes/head.php'); ?>
    <style> .stats-box { text-align: center; padding: 20px; border-radius: 8px; color: #fff; margin-bottom: 20px; } </style>
</head>
<body>
    <?php include('includes/header.php');?>
    <?php include('includes/sidebar.php');?>
    <main class="mn-inner">
        <div class="row">
            <div class="col s12"><div class="page-title">Personal Profile</div></div>
            <div class="col s12 m4"><div class="stats-box blue-grey darken-2"><small>QUOTA</small><h3><?php echo $total; ?></h3></div></div>
            <div class="col s12 m4"><div class="stats-box orange darken-2"><small>USED</small><h3><?php echo $row->LeavesTaken; ?></h3></div></div>
            <div class="col s12 m4"><div class="stats-box green darken-2"><small>BALANCE</small><h3><?php echo $avail; ?></h3></div></div>
            <div class="col s12">
                <div class="card"><div class="card-content">
                    <form method="post" enctype="multipart/form-data">
                        <span class="card-title">Profile Information</span>
                        <?php if(isset($msg)) echo "<div class='succWrap'>$msg</div>"; ?>
                        <?php if(isset($error)) echo "<div class='errorWrap'>$error</div>"; ?>
                        <div class="row">
                            <div class="col m4 s12 center-align">
                                <img src="<?php echo $imgSrc; ?>" class="circle" style="width:120px; height:120px; border:2px solid #eee;">
                                <div class="file-field input-field"><div class="btn blue btn-small"><span>Photo</span><input type="file" name="file"></div><div class="file-path-wrapper"><input class="file-path" type="text"></div></div>
                            </div>
                            <div class="col m8 s12">
                                <div class="row">
                                    <div class="input-field col m6 s12"><input value="<?php echo htmlentities($row->EmpId); ?>" readonly><label class="active">Emp ID</label></div>
                                    <div class="input-field col m6 s12"><input value="<?php echo htmlentities($row->EmailId); ?>" readonly><label class="active">Email</label></div>
                                    <div class="input-field col m6 s12"><input name="firstName" value="<?php echo htmlentities($row->FirstName); ?>" required><label class="active">First Name</label></div>
                                    <div class="input-field col m6 s12"><input name="lastName" value="<?php echo htmlentities($row->LastName); ?>" required><label class="active">Last Name</label></div>
                                    <div class="input-field col m6 s12"><input name="gender" value="<?php echo htmlentities($row->Gender); ?>" required><label class="active">Gender</label></div>
                                    <div class="input-field col m6 s12"><input name="dob" type="text" class="datepicker" value="<?php echo htmlentities($row->Dob); ?>"><label class="active">DOB</label></div>
                                    <div class="col m6 s12"><label>Department</label><select name="department" class="browser-default">
                                        <?php $qD=$dbh->query("SELECT DepartmentName FROM tbldepartments"); while($rD=$qD->fetch()){ $sel=($row->Department==$rD->DepartmentName)?'selected':''; echo "<option value='".htmlentities($rD->DepartmentName)."' $sel>".htmlentities($rD->DepartmentName)."</option>"; } ?>
                                    </select></div>
                                    <div class="input-field col s12"><input name="address" value="<?php echo htmlentities($row->Address); ?>" required><label class="active">Address</label></div>
                                    <div class="input-field col s12"><input name="mobileno" value="<?php echo htmlentities($row->Phonenumber); ?>" required><label class="active">Phone</label></div>
                                    <div class="col s12 right-align"><button type="submit" name="update" class="btn blue">Update Profile</button></div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div></div>
            </div>
        </div>
    </main>
    <?php include('includes/footer.php'); ?>
    <script>$('.datepicker').pickadate({ format: 'yyyy-mm-dd' });</script>
</body>
</html>