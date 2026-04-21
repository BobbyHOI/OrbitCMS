<?php

require_once(dirname(__FILE__) . '/../models/Employee.php');
require_once(dirname(__FILE__) . '/../includes/config.php');
require_once(dirname(__FILE__) . '/../sendmail.php');

class EmployeeController {
    private $employeeModel;

    public function __construct() {
        global $dbh;
        $this->employeeModel = new Employee($dbh);
    }

    /**
     * Fetches a single employee record for the view.
     * @param int $id The employee ID.
     * @return Employee|null The employee object or null if not found.
     */
    public function getEmployee($id) {
        return $this->employeeModel->findById($id);
    }

    /**
     * Gathers, validates, and sanitizes all possible employee data from a POST request.
     * This is a private helper used by create() and update().
     * @return array|null The sanitized data array or null if validation fails.
     */
    private function getEmployeeDataFromPost() {
        // Basic validation for fields that are always required.
        if (empty($_POST['firstName']) || empty($_POST['lastName'])) {
            return null;
        }

        $data = [
            'empcode'     => filter_input(INPUT_POST, 'empcode', FILTER_DEFAULT),
            'firstName'   => filter_input(INPUT_POST, 'firstName', FILTER_DEFAULT),
            'lastName'    => filter_input(INPUT_POST, 'lastName', FILTER_DEFAULT),
            'email'       => filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL),
            'password'    => filter_input(INPUT_POST, 'password', FILTER_DEFAULT), // Only used for creation
            'gender'      => filter_input(INPUT_POST, 'gender', FILTER_DEFAULT),
            'dob'         => filter_input(INPUT_POST, 'dob', FILTER_DEFAULT),
            'department'  => filter_input(INPUT_POST, 'department', FILTER_DEFAULT),
            'address'     => filter_input(INPUT_POST, 'address', FILTER_DEFAULT),
            'city'        => filter_input(INPUT_POST, 'city', FILTER_DEFAULT),
            'state'       => filter_input(INPUT_POST, 'state', FILTER_DEFAULT),
            'country'     => filter_input(INPUT_POST, 'country', FILTER_DEFAULT),
            'mobileno'    => filter_input(INPUT_POST, 'mobileno', FILTER_DEFAULT),
            'role'        => filter_input(INPUT_POST, 'role', FILTER_VALIDATE_INT),
            'extraLeaves' => filter_input(INPUT_POST, 'extraLeaves', FILTER_VALIDATE_INT),
        ];

        if (!$data['email']) {
            $_SESSION['error'] = 'Invalid email format.';
            return null;
        }

        return $data;
    }

    /**
     * Handles the creation of a new employee.
     */
    public function create() {
        if (isset($_POST['add'])) {
            $data = $this->getEmployeeDataFromPost();
            if (!$data || empty($data['password'])) {
                $_SESSION['error'] = 'Invalid data submitted. Please check all fields.';
                header('location: addemployee.php');
                exit;
            }

            // Map form data to the model properties
            $this->employeeModel->empid = $data['empcode'];
            $this->employeeModel->fname = $data['firstName'];
            $this->employeeModel->lname = $data['lastName'];
            $this->employeeModel->email = $data['email'];
            $this->employeeModel->password = $data['password'];
            $this->employeeModel->gender = $data['gender'];
            $this->employeeModel->dob = $data['dob'];
            $this->employeeModel->department = $data['department'];
            $this->employeeModel->address = $data['address'];
            $this->employeeModel->city = $data['city'];
            $this->employeeModel->country = $data['country'];
            $this->employeeModel->mobileno = $data['mobileno'];
            $this->employeeModel->role = $data['role'];
            $this->employeeModel->extraleaves = $data['extraLeaves'];

            if ($this->employeeModel->createByAdmin()) {
                $this->sendVerificationEmail();
                $_SESSION['msg'] = "Employee record added successfully. Verification email sent.";
            } else {
                $_SESSION['error'] = "Operation failed. The Employee ID or Email might already exist.";
            }
            header('location: manageemployee.php');
            exit;
        }
    }

    /**
     * Handles updating an existing employee.
     */
    public function update() {
        if (isset($_POST['update'])) {
            $id = intval($_GET['empid']);
            $data = $this->getEmployeeDataFromPost();

            if (!$data) {
                $_SESSION['error'] = 'Invalid data submitted. Please check all fields.';
                header('location: editemployee.php?empid=' . $id);
                exit;
            }

            // **Security Best Practice**: Never update password from the general edit form.
            unset($data['password']);

            if ($this->employeeModel->updateByAdmin($id, $data)) {
                $_SESSION['msg'] = "Employee record updated successfully";
            } else {
                $_SESSION['error'] = "Update failed. No changes were made or an error occurred.";
            }
            header('location: manageemployee.php');
            exit;
        }
    }

    /**
     * Handles deleting an employee.
     */
    public function delete() {
        if (isset($_GET['delid'])) {
            $id = intval($_GET['delid']);
            if ($this->employeeModel->delete($id)) {
                $_SESSION['msg'] = "Employee record deleted successfully";
            } else {
                $_SESSION['error'] = "Failed to delete employee record.";
            }
            header('location: manageemployee.php');
            exit;
        }
    }

    /**
     * Sends the welcome/verification email to a new employee.
     */
    private function sendVerificationEmail() {
        $verificationLink = BASE_URL . 'verify_email.php?email=' . urlencode($this->employeeModel->email) . '&hash=' . urlencode($this->employeeModel->hash);
        $htmlStr = "";
        $htmlStr .= "Hi " . htmlspecialchars($this->employeeModel->fname) . ",<br /><br />";
        $htmlStr .= "An administrator has created an account for you.<br /><br />";
        $htmlStr .= "<b>Employee ID:</b> " . htmlspecialchars($this->employeeModel->empid) . "<br />";
        $htmlStr .= "Please click the button below to verify your email address and activate your account.<br /><br />";
        $htmlStr .= "<a href='{$verificationLink}' target='_blank' style='padding:1em; font-weight:bold; background-color:#0d47a1; color:#fff; text-decoration:none;'>VERIFY EMAIL</a><br /><br />";
        $htmlStr .= "Kind regards,<br />";
        $htmlStr .= "Orbit CMS";

        sendmail("OrbitCMS Account Verification", $this->employeeModel->email, "no-reply@orbitcms.com", $htmlStr, $this->employeeModel->fname);
    }
}
