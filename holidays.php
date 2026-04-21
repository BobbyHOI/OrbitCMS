<?php
/**
 * OrbitCMS - Public Holidays
 */
session_start();
error_reporting(0);
include('includes/config.php');

if(strlen($_SESSION['emplogin']) == 0) {   
    header('location:index.php');
    exit;
}

$year = date('Y');
$country = "NG";
$apiUrl = "https://calendarific.com/api/v2/holidays?api_key=" . $holidayApiKey . "&country=" . $country . "&year=" . $year . "&type=national";

$holidays = [];
try {
    $raw = file_get_contents($apiUrl);
    if($raw) { $data = json_decode($raw); $holidays = $data->response->holidays ?? []; }
} catch (Exception $e) { }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Employee | Holidays</title>
    <?php include('includes/head.php');?>
    <link href="assets/plugins/datatables/css/jquery.dataTables.min.css" rel="stylesheet">
</head>
<body>
    <?php include('includes/header.php');?>
    <?php include('includes/sidebar.php');?>
    
    <main class="mn-inner">
        <div class="row">
            <div class="col s12"><div class="page-title">Company Calendar | <?php echo $year; ?></div></div>
            <div class="col s12">
                <div class="card">
                    <div class="card-content">
                        <span class="card-title">National Holidays (Nigeria)</span>
                        <table id="example" class="display responsive-table">
                            <thead><tr><th>#</th><th>Holiday Name</th><th>Date</th><th>Type</th></tr></thead>
                            <tbody>
                                <?php $cnt=1; foreach($holidays as $h) { ?>
                                    <tr>
                                        <td><?php echo $cnt++; ?></td>
                                        <td><b><?php echo htmlentities($h->name); ?></b></td>
                                        <td><?php echo $h->date->iso; ?></td>
                                        <td><?php echo htmlentities(implode(', ', $h->type)); ?></td>
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
    <script src="assets/plugins/datatables/js/jquery.dataTables.min.js"></script>
    <script src="assets/js/pages/table-data.js"></script>
</body>
</html>