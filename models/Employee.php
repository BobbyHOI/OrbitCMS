<?php
/**
 * Employee Model
 * Represents an employee in the system and handles all database interactions 
 * for the tblemployees table.
 */
class Employee {
    private $conn;
    private $table_name = "tblemployees";

    // Model properties corresponding to database columns
    public $id, $empid, $fname, $lname, $email, $password, $gender, $dob, $department, $address, $city, $state, $country, $mobileno, $hash, $status, $role, $imagefilename, $extraleaves, $leavesTaken;

    /**
     * Constructor with database connection
     * @param $db The database connection object.
     */
    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Finds a single employee by their primary key (id).
     * @param int $id The employee's unique ID.
     * @return stdClass|null The employee data as an object, or null if not found.
     */
    public function findById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM " . $this->table_name . " WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
    
    /**
     * Creates a new employee record from the admin panel.
     * Hashes the password and generates a verification hash.
     * @return bool True on successful creation, false otherwise.
     */
    public function createByAdmin() {
        $query = "INSERT INTO " . $this->table_name . "
                  SET
                    EmpId = :empid,
                    FirstName = :fname,
                    LastName = :lname,
                    EmailId = :email,
                    Password = :password,
                    Gender = :gender,
                    Dob = :dob,
                    Department = :department,
                    Address = :address,
                    City = :city,
                    State = :state,
                    Country = :country,
                    Phonenumber = :mobileno,
                    Status = :status,
                    Role = :role,
                    ExtraLeaves = :extraleaves,
                    hash = :hash";

        $stmt = $this->conn->prepare($query);

        // Hash password and generate verification token before saving
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);
        $this->hash = bin2hex(random_bytes(32));
        $this->status = 1; // Set status to active by default

        $stmt->bindParam(":empid", $this->empid);
        $stmt->bindParam(":fname", $this->fname);
        $stmt->bindParam(":lname", $this->lname);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":gender", $this->gender);
        $stmt->bindParam(":dob", $this->dob);
        $stmt->bindParam(":department", $this->department);
        $stmt->bindParam(":address", $this->address);
        $stmt->bindParam(":city", $this->city);
        $stmt->bindParam(":state", $this->state);
        $stmt->bindParam(":country", $this->country);
        $stmt->bindParam(":mobileno", $this->mobileno);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":role", $this->role);
        $stmt->bindParam(":extraleaves", $this->extraleaves);
        $stmt->bindParam(":hash", $this->hash);

        return $stmt->execute();
    }

    /**
     * Updates an employee's record from the admin panel.
     * @param int $id The ID of the employee to update.
     * @param array $data The sanitized data from the form.
     * @return bool True if the record was updated, false otherwise.
     */
    public function updateByAdmin($id, $data) {
        $query = "UPDATE " . $this->table_name . "
                  SET
                    FirstName = :fname,
                    LastName = :lname,
                    Gender = :gender,
                    Dob = :dob,
                    Department = :department,
                    Address = :address,
                    City = :city,
                    State = :state,
                    Country = :country,
                    Phonenumber = :mobileno,
                    Role = :role,
                    ExtraLeaves = :extraleaves
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(":fname", $data['firstName']);
        $stmt->bindParam(":lname", $data['lastName']);
        $stmt->bindParam(":gender", $data['gender']);
        $stmt->bindParam(":dob", $data['dob']);
        $stmt->bindParam(":department", $data['department']);
        $stmt->bindParam(":address", $data['address']);
        $stmt->bindParam(":city", $data['city']);
        $stmt->bindParam(":state", $data['state']);
        $stmt->bindParam(":country", $data['country']);
        $stmt->bindParam(":mobileno", $data['mobileno']);
        $stmt->bindParam(":role", $data['role']);
        $stmt->bindParam(":extraleaves", $data['extraLeaves']);

        // rowCount() ensures that a change was actually made
        return $stmt->execute() && $stmt->rowCount() > 0;
    }

    /**
     * Deletes an employee record by their ID.
     * @param int $id The ID of the employee to delete.
     * @return bool True on successful deletion, false otherwise.
     */
    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM " . $this->table_name . " WHERE id = ?");
        $stmt->bindParam(1, $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Updates the running total of leaves taken by an employee.
     * @param int $employeeId The employee's ID.
     * @param int $daysToDeduct The number of days to add to the taken count.
     * @return bool True if the update was successful, false otherwise.
     */
    public function updateLeaveBalance($employeeId, $daysToDeduct) {
        $query = "UPDATE " . $this->table_name . "
                  SET LeavesTaken = LeavesTaken + :daysToDeduct
                  WHERE id = :employeeId";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':daysToDeduct', $daysToDeduct, PDO::PARAM_INT);
        $stmt->bindParam(':employeeId', $employeeId, PDO::PARAM_INT);

        return $stmt->execute() && $stmt->rowCount() > 0;
    }
    
    /**
     * Finds an employee by their email and a specific account status.
     * Used for verification and password reset.
     * @param string $email The employee's email.
     * @param int $status The account status to check for.
     * @return array|false The employee data as an associative array, or false if not found.
     */
    public function findByEmailAndStatus($email, $status) {
        $query = "SELECT EmpId, FirstName, LastName, Status, Phonenumber 
                  FROM " . $this->table_name . " 
                  WHERE EmailId = :email AND Status = :status 
                  LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':status', $status, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Validates an employee's account by updating their status.
     * @param string $email The employee's email.
     * @param int $newStatus The new status to set.
     * @return bool True on successful update, false otherwise.
     */
    public function validate($email, $newStatus) {
        $query = "UPDATE " . $this->table_name . " 
                  SET Status = :newStatus 
                  WHERE EmailId = :email";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':newStatus', $newStatus, PDO::PARAM_INT);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);

        return $stmt->execute() && $stmt->rowCount() > 0;
    }
}
?>