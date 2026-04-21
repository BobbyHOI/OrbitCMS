<?php

require_once(dirname(__FILE__) . '/../includes/config.php');

class Leave {
    private $dbh;

    public function __construct() {
        global $dbh;
        $this->dbh = $dbh;
    }

    /**
     * Creates a new leave request.
     * @param int $empid
     * @param string $leaveType
     * @param string $fromDate
     * @param string $toDate
     * @param int $daysRequested
     * @param string $description
     * @return bool
     */
    public function create($empid, $leaveType, $fromDate, $toDate, $daysRequested, $description) {
        $sql = "INSERT INTO tblleaves(LeaveType, FromDate, ToDate, DaysRequested, Description, empid) VALUES(?,?,?,?,?,?)";
        $stmt = $this->dbh->prepare($sql);
        return $stmt->execute([$leaveType, $fromDate, $toDate, $daysRequested, $description, $empid]);
    }

    /**
     * Fetches all leaves for a specific employee.
     * @param int $empid
     * @return array
     */
    public function getByEmployeeId($empid) {
        $stmt = $this->dbh->prepare("SELECT * FROM tblleaves WHERE empid = ? ORDER BY PostingDate DESC");
        $stmt->execute([$empid]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Fetches all leaves, joining with employee details.
     * @return array
     */
    public function getAll() {
        $sql = "SELECT l.id as lid, e.FirstName, e.LastName, e.EmpId, e.id as empid, l.LeaveType, l.PostingDate, l.Status FROM tblleaves l JOIN tblemployees e ON l.empid = e.id ORDER BY l.PostingDate DESC";
        $stmt = $this->dbh->query($sql);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    
    /**
     * Fetches all leaves with a specific status.
     * @param int $status
     * @return array
     */
    public function getByStatus($status) {
        $sql = "SELECT l.id as lid, e.FirstName, e.LastName, e.EmpId, e.id as empid, l.LeaveType, l.PostingDate, l.Status FROM tblleaves l JOIN tblemployees e ON l.empid = e.id WHERE l.Status = ? ORDER BY l.PostingDate DESC";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute([$status]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Fetches a single leave record by its ID, joining with employee details.
     * @param int $id
     * @return object|null
     */
    public function findById($id) {
        $sql = "SELECT l.*, e.FirstName, e.LastName, e.EmpId, e.Gender, e.Dob, e.Department, e.Address, e.City, e.State, e.Country, e.Phonenumber, e.RegDate FROM tblleaves l JOIN tblemployees e ON l.empid = e.id WHERE l.id = ?";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Updates the status of a leave request (approve, deny).
     * @param int $id
     * @param int $status
     * @param string $remark
     * @return bool
     */
    public function updateStatus($id, $status, $remark) {
        $adminRemarkDate = date('Y-m-d H:i:s');
        $sql = "UPDATE tblleaves SET Status = ?, AdminRemark = ?, AdminRemarkDate = ? WHERE id = ?";
        $stmt = $this->dbh->prepare($sql);
        return $stmt->execute([$status, $remark, $adminRemarkDate, $id]);
    }

    /**
     * Marks a single leave request as read.
     * @param int $id
     * @return bool
     */
    public function markLeaveAsReadById($id) {
        $stmt = $this->dbh->prepare("UPDATE tblleaves SET IsRead = 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Marks all leaves of a certain status as read.
     * Used to dismiss notifications.
     * @param int $status
     * @return bool
     */
    public function markAsRead($status = 0) {
        $stmt = $this->dbh->prepare("UPDATE tblleaves SET IsRead = 1 WHERE Status = ?");
        return $stmt->execute([$status]);
    }
    
    /**
     * Counts unread leave applications.
     * @return int
     */
    public function countUnread() {
        return $this->dbh->query("SELECT count(id) FROM tblleaves WHERE IsRead=0")->fetchColumn();
    }
}
