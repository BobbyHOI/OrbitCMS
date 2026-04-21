<?php 
require_once("../includes/config.php");
if(!empty($_POST["empcode"])) {
    $q = $dbh->prepare("SELECT EmpId FROM tblemployees WHERE EmpId=?");
    $q->execute([$_POST["empcode"]]);
    if($q->rowCount() > 0) {
        echo "<span style='color:red'> ID already exists.</span><script>$('#add').prop('disabled',true);</script>";
    } else {
        echo "<span style='color:green'> ID available.</span><script>$('#add').prop('disabled',false);</script>";
    }
}
if(!empty($_POST["emailid"])) {
    $q = $dbh->prepare("SELECT EmailId FROM tblemployees WHERE EmailId=?");
    $q->execute([$_POST["emailid"]]);
    if($q->rowCount() > 0) {
        echo "<span style='color:red'> Email exists.</span><script>$('#add').prop('disabled',true);</script>";
    } else {
        echo "<span style='color:green'> Email available.</span><script>$('#add').prop('disabled',false);</script>";
    }
}
?>