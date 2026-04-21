<?php

require_once(dirname(__FILE__) . '/../models/Leave.php');
require_once(dirname(__FILE__) . '/../models/Employee.php');
require_once(dirname(__FILE__) . '/../models/Setting.php');
require_once(dirname(__FILE__) . '/../models/Holiday.php');
require_once(dirname(__FILE__) . '/../models/LeaveType.php');

class LeaveController {
    private $leaveModel;
    private $conn;

    /**
     * Constructor for the LeaveController.
     * Establishes the database connection for transaction management.
     */
    public function __construct() {
        global $dbh;
        $this->conn = $dbh;
        $this->leaveModel = new Leave($this->conn);
    }

    /**
     * Processes a new leave application from an employee.
     * This method contains the full business logic for validation.
     */
    public function apply() {
        try {
            $empid = $_SESSION['eid'];
            $leaveType = $_POST['leavetype'];
            $fromDateStr = $_POST['fromdate'];
            $toDateStr = $_POST['todate'];
            $description = $_POST['description'];

            // -- 1. Fetch all necessary data --
            $employeeModel = new Employee($this->conn);
            $settingModel = new Setting();
            $holidayModel = new Holiday();
            $leaveTypeModel = new LeaveType();

            $employee = $employeeModel->findById($empid);
            $holidays = $holidayModel->getHolidayDates();
            $workingDays = explode(',', $settingModel->getValue('working_days'));
            $leaveTypeDef = $leaveTypeModel->getByName($leaveType);
            $existingLeaves = $this->leaveModel->getByEmployeeId($empid);
            
            $baseMaxLeaves = (int)$settingModel->getValue('base_max_leaves');
            $totalLeave = $baseMaxLeaves + $employee->ExtraLeaves;
            $leaveBalance = $totalLeave - $employee->LeavesTaken;

            // -- 2. Date and working day calculation --
            $fromDate = new DateTime($fromDateStr);
            $toDate = new DateTime($toDateStr);
            if ($toDate < $fromDate) {
                return "<div class='errorWrap'>Error: The 'To Date' must be after the 'From Date'.</div>";
            }
            
            $requestedDays = 0;
            $currentDate = clone $fromDate;
            while ($currentDate <= $toDate) {
                $dayOfWeek = $currentDate->format('N'); // 1 (Mon) - 7 (Sun)
                $dateStr = $currentDate->format('Y-m-d');
                if (in_array($dayOfWeek, $workingDays) && !in_array($dateStr, $holidays)) {
                    $requestedDays++;
                }
                $currentDate->modify('+1 day');
            }

            if ($requestedDays <= 0) {
                 return "<div class='errorWrap'>Error: The selected date range contains no working days.</div>";
            }

            // -- 3. Validation Checks --

            // Check for overlapping leave dates
            foreach ($existingLeaves as $leave) {
                $existingFrom = new DateTime($leave->FromDate);
                $existingTo = new DateTime($leave->ToDate);
                if (($fromDate <= $existingTo) && ($toDate >= $existingFrom)) {
                    return "<div class='errorWrap'>Error: You already have a leave request in this period.</div>";
                }
            }

            // Check leave type limit
            if ($leaveTypeDef->LeaveLimit > 0 && $requestedDays > $leaveTypeDef->LeaveLimit) {
                return "<div class='errorWrap'>Error: Exceeds the maximum of " . $leaveTypeDef->LeaveLimit . " days for this leave type.</div>";
            }

            // Check available balance
            if ($requestedDays > $leaveBalance) {
                 return "<div class='errorWrap'>Error: You do not have enough leave balance. Available: $leaveBalance days.</div>";
            }

            // -- 4. Create the leave request --
            $this->leaveModel->create($empid, $leaveType, $fromDateStr, $toDateStr, $requestedDays, $description);
            
            echo "<script>alert('Leave application submitted successfully.'); window.location.href='leave-history.php';</script>";
            exit;

        } catch (Exception $e) {
            return "<div class='errorWrap'>An unexpected error occurred: " . $e->getMessage() . "</div>";
        }
    }

    /**
     * Processes a leave request decision (approve/deny) using a database transaction.
     *
     * @param int $leaveId The ID of the leave request.
     * @param int $status The new status (1 for approved, 2 for denied).
     * @param string $remark An optional comment from the administrator.
     * @return array An array with 'success' (bool) and 'message' (string).
     */
    public function reviewLeaveRequest($leaveId, $status, $remark) {
        try {
            $this->conn->beginTransaction();

            // Update the leave status and remark
            $this->leaveModel->updateStatus($leaveId, $status, $remark);

            // If approved, deduct the days from the employee's leave balance
            if ($status == 1) {
                $leave = $this->leaveModel->findById($leaveId);
                if ($leave) {
                    $employeeModel = new Employee($this->conn);
                    $employeeModel->updateLeaveBalance($leave->empid, $leave->DaysRequested);
                }
            }
            
            $this->conn->commit();
            return ['success' => true, 'message' => 'Leave decision recorded successfully.'];

        } catch (Exception $e) {
            $this->conn->rollBack();
            return ['success' => false, 'message' => 'An error occurred while processing the request. Please try again.'];
        }
    }
}
