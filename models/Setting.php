<?php

require_once(dirname(__FILE__) . '/../includes/config.php');

class Setting {
    private $dbh;

    public function __construct() {
        global $dbh;
        $this->dbh = $dbh;
    }

    /**
     * Fetches a specific setting value by its key.
     * @param string $key The SettingKey.
     * @return string|null The SettingValue or null if not found.
     */
    public function getValue($key) {
        $stmt = $this->dbh->prepare("SELECT SettingValue FROM tblsettings WHERE SettingKey = ?");
        $stmt->execute([$key]);
        return $stmt->fetchColumn();
    }

    /**
     * Fetches all settings as an associative array.
     * @return array
     */
    public function getAll() {
        $stmt = $this->dbh->query("SELECT SettingKey, SettingValue FROM tblsettings");
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }
}
