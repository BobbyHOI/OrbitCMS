<?php

require_once(dirname(__FILE__) . '/../includes/config.php');

class Department {
    private $dbh;

    public function __construct() {
        global $dbh;
        $this->dbh = $dbh;
    }

    /**
     * Fetches all departments from the database.
     * @return array
     */
    public function getAll() {
        $stmt = $this->dbh->query("SELECT * FROM tbldepartments ORDER BY DepartmentName");
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Fetches a single department by its ID.
     * @param int $id
     * @return object|null
     */
    public function findById($id) {
        $stmt = $this->dbh->prepare("SELECT * FROM tbldepartments WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Creates a new department.
     * @param string $name
     * @param string $shortName
     * @param string $code
     * @return bool
     */
    public function create($name, $shortName, $code) {
        $stmt = $this->dbh->prepare("INSERT INTO tbldepartments (DepartmentName, DepartmentShortName, DepartmentCode) VALUES (?, ?, ?)");
        return $stmt->execute([$name, $shortName, $code]);
    }

    /**
     * Updates an existing department.
     * @param int $id
     * @param string $name
     * @param string $shortName
     * @param string $code
     * @return bool
     */
    public function update($id, $name, $shortName, $code) {
        $stmt = $this->dbh->prepare("UPDATE tbldepartments SET DepartmentName = ?, DepartmentShortName = ?, DepartmentCode = ? WHERE id = ?");
        return $stmt->execute([$name, $shortName, $code, $id]);
    }

    /**
     * Deletes a department by its ID.
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $stmt = $this->dbh->prepare("DELETE FROM tbldepartments WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
