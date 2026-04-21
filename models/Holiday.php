<?php

require_once(dirname(__FILE__) . '/../includes/config.php');

class Holiday {
    private $dbh;

    public function __construct() {
        global $dbh;
        $this->dbh = $dbh;
    }

    /**
     * Fetches all holiday dates.
     * @return array An array of holiday dates in 'Y-m-d' format.
     */
    public function getHolidayDates() {
        $stmt = $this->dbh->query("SELECT HolidayDate FROM tblholidays");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
