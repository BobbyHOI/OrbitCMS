<?php
/**
 * OrbitCMS - Manage Departments
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

// Deletion Handling
if (isset($_GET['delid'])) {
    $departmentController = new DepartmentController();
    $departmentController->delete();
}

// Data Fetching
$departmentModel = new Department();
$departments = $departmentModel->getAll();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin | Manage Departments</title>
    <?php include('includes/head.php'); ?>
</head>
<body>
    <?php include('includes/header.php'); ?>
    <?php include('includes/sidebar.php'); ?>
    <main class="mn-inner">
        <div class="row">
            <div class="col s12">
                <div class="page-title">Manage Departments</div>
            </div>
            <div class="col s12 m12 l12">
                <div class="card">
                    <div class="card-content">
                        <span class="card-title">Departments</span>
                        <a href="adddepartment.php" class="waves-effect waves-light btn indigo m-b-xs right">Add New</a>
                        <table id="example" class="display responsive-table ">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Department Name</th>
                                    <th>Department Short Name</th>
                                    <th>Department Code</th>
                                    <th>Creation Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $cnt = 1;
                                if($departments) {
                                    foreach($departments as $result) { ?>
                                <tr>
                                    <td><?php echo htmlentities($cnt);?></td>
                                    <td><?php echo htmlentities($result->DepartmentName);?></td>
                                    <td><?php echo htmlentities($result->DepartmentShortName);?></td>
                                    <td><?php echo htmlentities($result->DepartmentCode);?></td>
                                    <td><?php echo htmlentities($result->CreationDate);?></td>
                                    <td>
                                        <a href="editdepartment.php?deptid=<?php echo htmlentities($result->id);?>"><i class="material-icons">mode_edit</i></a>
                                        <a href="managedepartments.php?delid=<?php echo htmlentities($result->id);?>" onclick="return confirm('Are you sure you want to delete this department? This action cannot be undone.');"><i class="material-icons">delete_forever</i></a>
                                    </td>
                                </tr>
                                <?php $cnt++;} }?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php include('includes/footer.php'); ?>
</body>
</html>