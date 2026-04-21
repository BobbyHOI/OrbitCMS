<?php

require_once(dirname(__FILE__) . '/../models/LeaveType.php');

class LeaveTypeController {
    private $leaveTypeModel;

    public function __construct() {
        $this->leaveTypeModel = new LeaveType();
    }

    /**
     * Handles the creation of a new leave type.
     */
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
            $leaveType = trim($_POST['leavetype']);
            $description = trim($_POST['description']);
            $limit = filter_input(INPUT_POST, 'limit', FILTER_VALIDATE_INT, ["options" => ["min_range" => 0]]);

            if (empty($leaveType)) {
                $_SESSION['error'] = "The leave type name is required.";
                header('location: addleavetype.php');
                exit;
            }

            try {
                if ($this->leaveTypeModel->create($leaveType, $description, $limit)) {
                    $_SESSION['msg'] = "New leave type '" . htmlentities($leaveType) . "' was created successfully.";
                    header('location: manageleavetype.php');
                    exit;
                }
            } catch (PDOException $e) {
                error_log("LeaveType Creation Error: " . $e->getMessage());
                if ($e->errorInfo[1] == 1062) { // MySQL duplicate entry
                    $_SESSION['error'] = "This leave type already exists.";
                } else {
                    $_SESSION['error'] = "A database error occurred.";
                }
                header('location: addleavetype.php');
                exit;
            }
        }
    }

    /**
     * Handles the update of a leave type.
     */
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
            $id = intval($_GET['lid']);
            $leaveType = trim($_POST['leavetype']);
            $description = trim($_POST['description']);
            $limit = filter_input(INPUT_POST, 'limit', FILTER_VALIDATE_INT, ["options" => ["min_range" => 0]]);

            if ($id <= 0 || empty($leaveType)) {
                $_SESSION['error'] = "Invalid data. Please check all fields.";
                header("location: editleavetype.php?lid=$id");
                exit;
            }

            try {
                if ($this->leaveTypeModel->update($id, $leaveType, $description, $limit)) {
                    $_SESSION['msg'] = "Leave type updated successfully.";
                    header('location: manageleavetype.php');
                    exit;
                }
            } catch (PDOException $e) {
                error_log("LeaveType Update Error: " . $e->getMessage());
                $_SESSION['error'] = "A database error occurred. The leave type name may already exist.";
                header("location: editleavetype.php?lid=$id");
                exit;
            }
        }
    }

    /**
     * Handles the deletion of a leave type.
     */
    public function delete() {
        if (isset($_GET['delid'])) {
            $id = intval($_GET['delid']);
            if ($this->leaveTypeModel->delete($id)) {
                $_SESSION['msg'] = "Leave type deleted successfully.";
            } else {
                $_SESSION['error'] = "Failed to delete leave type. It might be in use.";
            }
            header('location: manageleavetype.php');
            exit;
        }
    }
}
