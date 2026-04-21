<?php

require_once(dirname(__FILE__) . '/../models/Department.php');

class DepartmentController {
    private $departmentModel;

    public function __construct() {
        $this->departmentModel = new Department();
    }

    /**
     * Handle the listing of all departments.
     */
    public function index() {
        // In our refactored approach, the view files will directly call the model's getAll() method.
        // This controller's primary role is for handling create, update, delete actions.
    }

    /**
     * Handle the creation of a new department from a POST request.
     */
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
            $name = trim($_POST['departmentname']);
            $shortName = trim($_POST['departmentshortname']);
            $code = trim($_POST['deptcode']);

            if (empty($name) || empty($shortName) || empty($code)) {
                $_SESSION['error'] = "All department fields are required. Please fill out the entire form.";
                header('location: adddepartment.php');
                exit;
            }

            try {
                if ($this->departmentModel->create($name, $shortName, $code)) {
                    $_SESSION['msg'] = "Department '" . htmlentities($name) . "' was created successfully.";
                    header('location: managedepartments.php');
                    exit;
                } else {
                    throw new Exception("Department creation failed for an unknown reason.");
                }
            } catch (PDOException $e) {
                error_log("Department Creation PDO Error: " . $e->getMessage());
                $_SESSION['error'] = "A database error occurred. The department code or name may already exist.";
                header('location: adddepartment.php');
                exit;
            } catch (Exception $e) {
                error_log("Department Creation General Error: " . $e->getMessage());
                $_SESSION['error'] = "An error occurred while creating the department.";
                header('location: adddepartment.php');
                exit;
            }
        }
    }

    /**
     * Handle the updating of a department from a POST request.
     */
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
            $id = intval($_GET['deptid']);
            $name = trim($_POST['departmentname']);
            $shortName = trim($_POST['departmentshortname']);
            $code = trim($_POST['deptcode']);

            if ($id <= 0 || empty($name) || empty($shortName) || empty($code)) {
                $_SESSION['error'] = "Invalid data provided. Please check all fields.";
                header("location: editdepartment.php?deptid=$id");
                exit;
            }

            try {
                if ($this->departmentModel->update($id, $name, $shortName, $code)) {
                    $_SESSION['msg'] = "Department updated successfully.";
                    header('location: managedepartments.php');
                    exit;
                } else {
                     throw new Exception("Department update failed for an unknown reason.");
                }
            } catch (PDOException $e) {
                 error_log("Department Update PDO Error: " . $e->getMessage());
                $_SESSION['error'] = "A database error occurred. The department code or name may already be in use by another department.";
                header("location: editdepartment.php?deptid=$id");
                exit;
            } catch (Exception $e) {
                error_log("Department Update General Error: " . $e->getMessage());
                $_SESSION['error'] = "An error occurred while updating the department.";
                header("location: editdepartment.php?deptid=$id");
                exit;
            }
        }
    }

    /**
     * Handle the deletion of a department from a GET request.
     */
    public function delete() {
        if (isset($_GET['delid'])) {
            $id = intval($_GET['delid']);
            if ($this->departmentModel->delete($id)) {
                $_SESSION['msg'] = "Department deleted successfully.";
            } else {
                $_SESSION['error'] = "Failed to delete department. It might be in use by employees.";
            }
            header('location: managedepartments.php');
            exit;
        }
    }
}
