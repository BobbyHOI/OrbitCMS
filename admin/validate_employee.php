<?php
// The controller will handle all the logic, session start, and includes.
require_once('employee_validation_controller.php'); 

// The rest of this file is purely for presentation (the "View").
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>OrbitCMS | Employee Validation</title>
    <?php require_once('includes/head.php'); ?>
</head>
<body>
    <div class="mn-content fixed-sidebar">
        <?php require_once('includes/header.php'); ?>
        <?php require_once('includes/sidebar.php'); ?>

        <main class="mn-inner">
            <div class="row">
                <div class="col s12">
                    <div class="page-title">Employee Validation</div>
                </div>
                <div class="col s12 m12 l12">
                    <div class="card">
                        <div class="card-content">

                            <?php if ($msg) { ?>
                                <div class="succWrap"><strong>SUCCESS</strong>: <?php echo htmlentities($msg); ?></div>
                            <?php } elseif ($error) { ?>
                                <div class="errorWrap"><strong>ERROR</strong>: <?php echo htmlentities($error); ?></div>
                            <?php } ?>

                            <?php if ($employeeData) { ?>
                                <form method="post" name="validation_form">
                                    <h3>Review Employee Registration</h3>
                                    <hr>
                                    <table class="display responsive-table ">
                                        <tbody>
                                            <tr>
                                                <td style="font-size:16px;"><b>Employee Name:</b></td>
                                                <td><?php echo htmlentities($employeeData['FirstName'] . ' ' . $employeeData['LastName']); ?></td>
                                            </tr>
                                            <tr>
                                                <td style="font-size:16px;"><b>Employee ID:</b></td>
                                                <td><?php echo htmlentities($employeeData['EmpId']); ?></td>
                                            </tr>
                                            <tr>
                                                <td style="font-size:16px;"><b>Contact No.:</b></td>
                                                <td><?php echo htmlentities($employeeData['Phonenumber']); ?></td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <br>
                                    <div class="input-field col s12">
                                        <?php if ($newStatus == 1) { // Show Accept button ?>
                                            <p>Clicking "ACCEPT" will activate the employee's account and send them a welcome email.</p>
                                            <button type="submit" name="action" value="accept" class="waves-effect waves-light btn indigo m-b-xs">ACCEPT</button>
                                        <?php } elseif ($newStatus == 2) { // Show Reject button ?>
                                            <p>Clicking "REJECT" will mark the registration as rejected and send the user a notification.</p>
                                            <button type="submit" name="action" value="reject" class="waves-effect waves-light btn red m-b-xs">REJECT</button>
                                        <?php } ?>
                                    </div>
                                </form>
                            <?php } else { ?>
                                <p>This validation link has been processed or is no longer valid. Please check the employee management page for current status.</p>
                                <a href="manageemployee.php" class="waves-effect waves-light btn indigo m-b-xs">Go to Employee List</a>
                           <?php } ?>

                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <div class="left-sidebar-hover"></div>
    <?php require_once('includes/footer.php'); ?>
</body>
</html>
