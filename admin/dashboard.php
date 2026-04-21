<?php
session_start();
include('../includes/config.php');
if(strlen($_SESSION['alogin']) == 0) { 
    header('location:../index.php'); 
    exit; 
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin | Dashboard</title>
    <?php include('includes/head.php'); ?>
    <style> 
        .stats-box { 
            padding: 20px; 
            border-radius: 8px; 
            color: #fff; 
            margin-bottom: 20px; 
            transition: 0.3s; 
        } 
        .stats-box:hover { 
            transform: translateY(-5px); 
        } 
    </style>
</head>
<body>
    <?php include('includes/header.php');?>
    <?php include('includes/sidebar.php');?>
    <main class="mn-inner">
        <div class="middle-content">
            <div class="row">
                <div class="col s12 m4">
                    <div class="stats-box blue darken-2">
                        <small>EMPLOYEES</small>
                        <h3><?php echo $dbh->query("SELECT count(id) FROM tblemployees")->fetchColumn(); ?></h3>
                    </div>
                </div>
                <div class="col s12 m4">
                    <div class="stats-box green darken-2">
                        <small>DEPARTMENTS</small>
                        <h3><?php echo $dbh->query("SELECT count(id) FROM tbldepartments")->fetchColumn(); ?></h3>
                    </div>
                </div>
                <div class="col s12 m4">
                    <div class="stats-box amber darken-2">
                        <small>LEAVE TYPES</small>
                        <h3><?php echo $dbh->query("SELECT count(id) FROM tblleavetype")->fetchColumn(); ?></h3>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col s12">
                    <div class="card">
                        <div class="card-content">
                            <span class="card-title">Recent Activity</span>
                            <table class="display responsive-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Employee</th>
                                        <th>Type</th>
                                        <th>Posting Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $q = $dbh->query("SELECT l.*, e.FirstName, e.LastName FROM tblleaves l JOIN tblemployees e ON l.empid=e.id ORDER BY l.id DESC LIMIT 5");
                                    $cnt = 1;
                                    while($r = $q->fetch()){ ?>
                                        <tr>
                                            <td><?php echo $cnt; ?></td>
                                            <td><?php echo $r->FirstName . ' ' . $r->LastName; ?></td>
                                            <td><?php echo $r->LeaveType; ?></td>
                                            <td><?php echo $r->PostingDate; ?></td>
                                            <td><?php echo ($r->Status==1?'Approved':($r->Status==2?'Rejected':'Pending')); ?></td>
                                        </tr>
                                    <?php $cnt++; }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php include('includes/footer.php'); ?>
</body>
</html>