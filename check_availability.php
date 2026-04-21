<?php 
/**
 * OrbitCMS - Check Availability
 */
require_once("includes/config.php");

// 1. Employee Code Check
if(!empty($_POST["empcode"])) {
    $empid = $_POST["empcode"];
    $query = $dbh->prepare("SELECT EmpId FROM tblemployees WHERE EmpId=:empid");
    $query->execute([':empid' => $empid]);
    
    if($query->rowCount() > 0) {
        echo "<span style='color:red'> Employee ID already exists.</span>";
        echo "<script>$('#add').prop('disabled', true);</script>";
    } else {
        echo "<span style='color:green'> Employee ID available.</span>";
        echo "<script>$('#add').prop('disabled', false);</script>";
    }
}

// 2. Email Availability Check
if(!empty($_POST["emailid"])) {
    $email = $_POST["emailid"];
    $query = $dbh->prepare("SELECT EmailId FROM tblemployees WHERE EmailId=:email");
    $query->execute([':email' => $email]);
    
    if($query->rowCount() > 0) {
        echo "<span style='color:red'> Email already exists.</span>";
        echo "<script>$('#add').prop('disabled', true);</script>";
    } else {
        echo "<span style='color:green'> Email available.</span>";
        echo "<script>$('#add').prop('disabled', false);</script>";
    }
}
?>