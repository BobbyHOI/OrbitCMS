<?php
/**
 * OrbitCMS - Manage Employees
 */
session_start();
error_reporting(0);
include('../includes/config.php');

if(strlen($_SESSION['alogin']) == 0) {   
    header('location:../index.php');
    exit;
}

// Toggle Status
if(isset($_GET['inid'])) {
    $dbh->prepare("UPDATE tblemployees SET Status=0 WHERE id=?")->execute([$_GET['inid']]);
    header('location:manageemployee.php');
    exit;
}
if(isset($_GET['id'])) {
    $dbh->prepare("UPDATE tblemployees SET Status=1 WHERE id=?")->execute([$_GET['id']]);
    header('location:manageemployee.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin | Manage Employees</title>
    <?php include('includes/head.php');?>
    <link href="../assets/plugins/datatables/css/jquery.dataTables.min.css" rel="stylesheet">
</head>
<body>
    <?php include('includes/header.php');?>
    <?php include('includes/sidebar.php');?>
    
    <main class="mn-inner">
        <div class="row">
            <div class="col s12"><div class="page-title">Personnel Records</div></div>
            <div class="col s12">
                <div class="card">
                    <div class="card-content">
                        <table id="example" class="display responsive-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Emp ID</th>
                                    <th>Name</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $sql = "SELECT e.*, r.RoleName FROM tblemployees e LEFT JOIN tblroles r ON e.role = r.id";
                                $query = $dbh->query($sql);
                                $cnt = 1;
                                while($row = $query->fetch()) { ?>  
                                    <tr>
                                        <td><?php echo $cnt++; ?></td>
                                        <td><?php echo htmlentities($row->EmpId); ?></td>
                                        <td><?php echo htmlentities($row->FirstName . " " . $row->LastName); ?></td>
                                        <td><?php echo htmlentities($row->RoleName ?? 'Employee'); ?></td>
                                        <td><?php echo ($row->Status == 1) ? '<span class="green-text">Active</span>' : '<span class="red-text">Inactive</span>'; ?></td>
                                        <td>
                                            <a href="editemployee.php?empid=<?php echo $row->id; ?>" class="blue-text"><i class="material-icons">edit</i></a>
                                            <?php if($row->Status == 1): ?>
                                                <a href="manageemployee.php?inid=<?php echo $row->id; ?>" class="red-text" onclick="return confirm('Deactivate account?');"><i class="material-icons">block</i></a>
                                            <?php else: ?>
                                                <a href="manageemployee.php?id=<?php echo $row->id; ?>" class="green-text" onclick="return confirm('Activate account?');"><i class="material-icons">check_circle</i></a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php include('includes/footer.php');?>
    <script src="../assets/plugins/datatables/js/jquery.dataTables.min.js"></script>
    <script src="../assets/js/pages/table-data.js"></script>
</body>
</html>