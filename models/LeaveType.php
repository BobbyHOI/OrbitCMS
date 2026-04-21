<?php

require_once(dirname(__FILE__) . '/../includes/config.php');

class LeaveType {
    private $dbh;

    public function __construct() {
        global $dbh;
        $this->dbh = $dbh;
    }

    /**
     * Fetches all leave types from the database.
     * @return array
     */
    public function getAll() {
        $stmt = $this->dbh->query("SELECT * FROM tblleavetype ORDER BY LeaveType");
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Fetches a single leave type by its ID.
     * @param int $id
     * @return object|null
     */
    public function findById($id) {
        $stmt = $this->dbh->prepare("SELECT * FROM tblleavetype WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Fetches a single leave type by its name.
     * @param string $name
     * @return object|null
     */
    public function getByName($name) {
        $stmt = $this->dbh->prepare("SELECT * FROM tblleavetype WHERE LeaveType = ?");
        $stmt->execute([$name]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Creates a new leave type.
     * @param string $leaveType
     * @param string $description
     * @param int $limit
     * @return bool
     */
    public function create($leaveType, $description, $limit) {
        $stmt = $this->dbh->prepare("INSERT INTO tblleavetype(LeaveType, Description, LeaveLimit) VALUES(?, ?, ?)");
        return $stmt->execute([$leaveType, $description, $limit]);
    }

    /**
     * Updates an existing leave type.
     * @param int $id
     * @param string $leaveType
     * @param string $description
     * @param int $limit
     * @return bool
     */
    public function update($id, $leaveType, $description, $limit) {
        $stmt = $this->dbh->prepare("UPDATE tblleavetype SET LeaveType = ?, Description = ?, LeaveLimit = ? WHERE id = ?");
        return $stmt->execute([$leaveType, $description, $limit, $id]);
    }

    /**
     * Deletes a leave type by its ID.
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $stmt = $this->dbh->prepare("DELETE FROM tblleavetype WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
