<?php
/**
 * OrbitCMS - Edit Department
 */
session_start();
error_reporting(0);
include('../includes/config.php');
require_once('../controllers/DepartmentController.php');
require_once('../models/Department.php');

// Authentication check
if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit;
}

// Get the Department ID from the URL
$did = intval($_GET['deptid']);

// Update Handling
if(isset($_POST['update'])){
    $departmentController = new DepartmentController();
    $departmentController->update();
}

// Data Fetching
$departmentModel = new Department();
$department = $departmentModel->findById($did);

if(!$department) {
    $_SESSION['error'] = "Department not found.";
    header('location:managedepartments.php');
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin | Edit Department</title>
    <?php include('includes/head.php'); ?>
</head>
<body>
    <?php include('includes/header.php'); ?>
    <?php include('includes/sidebar.php'); ?>
    <main class="mn-inner">
        <div class="row">
            <div class="col s12">
                <div class="page-title">Edit Department</div>
            </div>
            <div class="col s12 m12 l6">
                <div class="card">
                    <div class="card-content">
                        <span class="card-title">Department Details</span><br>
                        <form method="post">
                            <div class="row">
                                <div class="input-field col s12">
                                    <input id="departmentname" type="text" value="<?php echo htmlentities($department->DepartmentName);?>" name="departmentname" required>
                                    <label for="departmentname" class="active">Department Name</label>
                                </div>
                                <div class="input-field col s12">
                                    <input id="departmentshortname" type="text" value="<?php echo htmlentities($department->DepartmentShortName);?>" name="departmentshortname" required>
                                    <label for="departmentshortname" class="active">Department Short Name</label>
                                </div>
                                <div class="input-field col s12">
                                    <input id="deptcode" name="deptcode" type="text" value="<?php echo htmlentities($department->DepartmentCode);?>" required>
                                    <label for="deptcode" class="active">Department Code</label>
                                </div>
                                <div class="input-field col s12">
                                    <button type="submit" name="update" class="waves-effect waves-light btn indigo m-b-xs">UPDATE</button>
                                    <a href="managedepartments.php" class="waves-effect waves-light btn grey m-b-xs">CANCEL</a>
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