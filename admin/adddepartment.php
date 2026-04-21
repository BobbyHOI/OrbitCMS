<?php
/**
 * OrbitCMS - Add Department
 */
session_start();
error_reporting(0);
include('../includes/config.php');
require_once('../controllers/DepartmentController.php');

// Authentication check
if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit;
}

// The controller will handle the POST request, validation, and redirection.
$departmentController = new DepartmentController();
$departmentController->create();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin | Add Department</title>
    <?php include('includes/head.php'); ?>
</head>
<body>
    <?php include('includes/header.php'); ?>
    <?php include('includes/sidebar.php'); ?>
    <main class="mn-inner">
        <div class="row">
            <div class="col s12">
                <div class="page-title">Add Department</div>
            </div>
            <div class="col s12 m12 l6">
                <div class="card">
                    <div class="card-content">
                        <span class="card-title">Department Details</span><br>
                        <form method="post">
                            <div class="row">
                                <div class="input-field col s12">
                                    <input id="departmentname" type="text" name="departmentname" class="validate" required>
                                    <label for="departmentname">Department Name</label>
                                </div>
                                <div class="input-field col s12">
                                    <input id="departmentshortname" type="text" name="departmentshortname" autocomplete="off" required>
                                     <label for="departmentshortname">Department Short Name</label>
                                </div>
                                <div class="input-field col s12">
                                    <input id="deptcode" name="deptcode" type="text" autocomplete="off" required>
                                    <label for="deptcode">Department Code</label>
                                </div>
                                <div class="input-field col s12">
                                    <button type="submit" name="add" class="waves-effect waves-light btn indigo m-b-xs">ADD</button>
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
