<?php
/**
 * OrbitCMS - Apply for Leave
 */
session_start();
error_reporting(0);

// Essential includes
include('includes/config.php');
require_once('controllers/LeaveController.php');
require_once('models/Employee.php');
require_once('models/Leave.php');
require_once('models/LeaveType.php');
require_once('models/Setting.php');
require_once('models/Holiday.php');

// Authentication
if (strlen($_SESSION['emplogin']) == 0) {
    header('location:index.php');
    exit;
}

// The controller handles all POST logic.
$leaveController = new LeaveController();
if (isset($_POST['apply'])) {
    $message = $leaveController->apply();
}

// -- Data Fetching for UI --
$empid = $_SESSION['eid'];

// Models
$employeeModel = new Employee($dbh);
$leaveModel = new Leave($dbh);
$leaveTypeModel = new LeaveType();
$settingModel = new Setting();
$holidayModel = new Holiday();

// Get Employee Data & Calculate Balance
$employee = $employeeModel->findById($empid);
$baseMaxLeaves = (int)$settingModel->getValue('base_max_leaves');
$totalLeave = $baseMaxLeaves + $employee->ExtraLeaves;
$leavesTaken = $employee->LeavesTaken;
$leaveBalance = $totalLeave - $leavesTaken;

// Get Data for Form & Datepicker
$leaveTypes = $leaveTypeModel->getAll();
$holidays = $holidayModel->getHolidayDates();
$workingDays = explode(',', $settingModel->getValue('working_days'));

// Get existing leave dates to disable in datepicker
$existingLeaveRecords = $leaveModel->getByEmployeeId($empid);
$disabledDates = [];
foreach ($existingLeaveRecords as $record) {
    $period = new DatePeriod(new DateTime($record->FromDate), new DateInterval('P1D'), (new DateTime($record->ToDate))->modify('+1 day'));
    foreach ($period as $date) { $disabledDates[] = $date->format('Y-m-d'); }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Employee | Apply for Leave</title>
    <?php include('includes/head.php'); ?>
    <style>
        .stats-card { padding: 15px; border-radius: 5px; color: #fff; margin-bottom: 15px; text-align: center; }
        .stats-card h5 { margin: 0; font-weight: bold; }
        .stats-card p { margin: 0; font-size: 12px; text-transform: uppercase; }
        .leave-type-card { background-color: #757575; }
        .days-display { font-size: 1.2rem; font-weight: bold; color: #4CAF50; text-align: center; margin-top: 10px; }
    </style>
</head>
<body>
    <?php include('includes/header.php'); ?>
    <?php include('includes/sidebar.php'); ?>

    <main class="mn-inner">
        <div class="row">
            <div class="col s12">
                <div class="page-title">Apply for Leave</div>
            </div>

            <!-- Leave Balance Cards -->
            <div class="col s12 m4">
                <div class="card stats-card blue-grey darken-2"><p>Total Quota</p><h5><?php echo $totalLeave; ?> Days</h5></div>
            </div>
            <div class="col s12 m4">
                <div class="card stats-card orange darken-2"><p>Leaves Taken</p><h5><?php echo $leavesTaken; ?> Days</h5></div>
            </div>
            <div class="col s12 m4">
                <div class="card stats-card green darken-2"><p>Balance</p><h5><?php echo $leaveBalance; ?> Days</h5></div>
            </div>

            <!-- Leave Type Limit Cards -->
            <div class="col s12">
                <div class="row">
                    <?php foreach ($leaveTypes as $type) { 
                        if ($type->LeaveLimit > 0) { ?>
                            <div class="col s12 m3">
                                <div class="card stats-card leave-type-card"><p><?php echo htmlentities($type->LeaveType); ?></p><h5>Max <?php echo $type->LeaveLimit; ?> Days</h5></div>
                            </div>
                    <?php } } ?>
                </div>
            </div>

            <div class="col s12">
                <div class="card">
                    <div class="card-content">
                        <form id="leave-form" method="post">
                            <?php if(isset($message)) { echo $message; } ?>
                            <div class="row">
                                <div class="input-field col s12">
                                    <select id="leavetype" name="leavetype" required>
                                        <option value="" disabled selected>Select Leave Type...</option>
                                        <?php foreach ($leaveTypes as $type) { ?>
                                            <option value="<?php echo htmlentities($type->LeaveType); ?>"><?php echo htmlentities($type->LeaveType); ?></option>
                                        <?php } ?>
                                    </select>
                                    <label>Leave Type</label>
                                </div>

                                <div class="input-field col m6 s12">
                                    <input id="fromdate" name="fromdate" type="text" class="datepicker" required>
                                    <label for="fromdate">From Date</label>
                                </div>
                                <div class="input-field col m6 s12">
                                    <input id="todate" name="todate" type="text" class="datepicker" required>
                                    <label for="todate">To Date</label>
                                </div>

                                <div class="input-field col s12">
                                    <textarea id="description" name="description" class="materialize-textarea" required></textarea>
                                    <label for="description">Description</label>
                                </div>

                                <div class="col s12">
                                    <div id="days-display-container" class="days-display" style="display: none;">
                                        Calculated Days: <span id="calculated-days">0</span>
                                    </div>
                                </div>

                                <div class="input-field col s12">
                                    <button type="submit" name="apply" class="waves-effect waves-light btn indigo m-b-xs">APPLY</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include('includes/footer.php'); ?>

    <script>
        $(document).ready(function() {
            const holidays = <?php echo json_encode($holidays); ?>;
            const existingLeaves = <?php echo json_encode($disabledDates); ?>;
            const workingDays = <?php echo json_encode($workingDays); ?>.map(Number); // [1,2,3,4,5]
            
            const combinedDisabledDates = [...holidays, ...existingLeaves];

            $('.datepicker').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                disableDayFn: function(date) {
                    const day = date.getDay();
                    const dateString = date.getFullYear() + '-' + ('0' + (date.getMonth() + 1)).slice(-2) + '-' + ('0' + date.getDate()).slice(-2);
                    
                    // Disable weekends
                    if (workingDays.indexOf(day) === -1) return true;

                    // Disable holidays and existing leaves
                    if (combinedDisabledDates.indexOf(dateString) !== -1) return true;

                    return false;
                },
                onSelect: function() {
                    calculateDays();
                }
            });

            function calculateDays() {
                const fromDateStr = $('#fromdate').val();
                const toDateStr = $('#todate').val();

                if (fromDateStr && toDateStr) {
                    let fromDate = new Date(fromDateStr);
                    let toDate = new Date(toDateStr);
                    
                    if(toDate < fromDate){
                        $('#days-display-container').hide();
                        return;
                    }

                    let workingDaysCount = 0;
                    let currentDate = fromDate;

                    while (currentDate <= toDate) {
                        const day = currentDate.getDay();
                        const dateString = currentDate.getFullYear() + '-' + ('0' + (currentDate.getMonth() + 1)).slice(-2) + '-' + ('0' + currentDate.getDate()).slice(-2);

                        // Recalculate working days
                        if (workingDays.indexOf(day) !== -1 && holidays.indexOf(dateString) === -1) {
                            workingDaysCount++;
                        }

                        currentDate.setDate(currentDate.getDate() + 1);
                    }

                    $('#calculated-days').text(workingDaysCount);
                    $('#days-display-container').show();
                }
            }
            
            $('#fromdate, #todate').on('change', calculateDays);
        });
    </script>
</body>
</html>
