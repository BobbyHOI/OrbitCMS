<?php
/**
 * OrbitCMS - System Settings & Holiday Engine
 * Modified based on user's clean base file.
 * - Uses config constants for API key/URL.
 * - Adds Email Settings section.
 * - Fixes holiday date display format.
 */
session_start();
error_reporting(0);
include('../includes/config.php');

if(strlen($_SESSION['alogin']) == 0) { header('location:../index.php'); exit; }

// 1. Fetch Current Settings
$settings = $dbh->query("SELECT SettingKey, SettingValue FROM tblsettings")->fetchAll(PDO::FETCH_KEY_PAIR);

// 2. Handle Actions
if(isset($_POST['update_company'])) {
    $dbh->prepare("UPDATE tblsettings SET SettingValue=? WHERE SettingKey='company_name'")->execute([$_POST['cname']]);
    $dbh->prepare("UPDATE tblsettings SET SettingValue=? WHERE SettingKey='company_country_code'")->execute([$_POST['ccode']]);
    $msg = "Company profile updated";
}

// New: Handle Email Settings Update
if(isset($_POST['update_email'])) {
    $dbh->prepare("UPDATE tblsettings SET SettingValue=? WHERE SettingKey='admin_email'")->execute([$_POST['admin_email']]);
    $dbh->prepare("UPDATE tblsettings SET SettingValue=? WHERE SettingKey='manager_emails'")->execute([$_POST['manager_emails']]);
    $dbh->prepare("UPDATE tblsettings SET SettingValue=? WHERE SettingKey='cc_emails'")->execute([$_POST['cc_emails']]);
    $msg = "Email notification settings updated";
}

if(isset($_POST['update_policy'])) {
    $dbh->prepare("UPDATE tblsettings SET SettingValue=? WHERE SettingKey='base_max_leaves'")->execute([$_POST['base_leaves']]);
    $dbh->prepare("UPDATE tblsettings SET SettingValue=? WHERE SettingKey='working_days'")->execute([implode(',', $_POST['working_days'] ?? [])]);
    $msg = "Leave policy updated";
}

// Fetch from Calendarific API - Using Constants
if(isset($_POST['fetch_api'])) {
    if (!defined('CALENDARIFIC_API_KEY') || empty(CALENDARIFIC_API_KEY) || !defined('CALENDARIFIC_API_URL')) {
        $error = "API Error: The CALENDARIFIC_API_KEY or CALENDARIFIC_API_URL is not defined in includes/config.php.";
    } else {
        $apiKey = CALENDARIFIC_API_KEY;
        $apiUrlBase = CALENDARIFIC_API_URL;
        $country = $settings['company_country_code'] ?? 'NG';
        $year = date('Y');
        $apiUrl = "{$apiUrlBase}?api_key={$apiKey}&country={$country}&year={$year}&type=national";
        
        $response = @file_get_contents($apiUrl);
        if($response) {
            $data = json_decode($response);
            if($data && isset($data->response->holidays)) {
                foreach($data->response->holidays as $h) {
                    $stmt = $dbh->prepare("INSERT IGNORE INTO tblholidays(HolidayName, HolidayDate) VALUES(?,?)");
                    $stmt->execute([$h->name, $h->date->iso]);
                }
                $msg = "Holidays fetched and synchronized";
            } else { $error = "API connection failed. Check your key or response data."; }
        } else { $error = "API connection failed. Check your network or the API URL constant."; }
    }
}

// Handle Holiday Delete
if(isset($_GET['del_h'])) {
    $dbh->prepare("DELETE FROM tblholidays WHERE id=?")->execute([$_GET['del_h']]);
    header("Location: system-settings.php#holidays");
    exit;
}

// Re-fetch settings after any potential updates to show new values immediately
$settings = $dbh->query("SELECT SettingKey, SettingValue FROM tblsettings")->fetchAll(PDO::FETCH_KEY_PAIR);
$activeDays = explode(',', $settings['working_days'] ?? '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin | Settings</title>
    <?php include('includes/head.php'); ?>
    <style>
        .tabs .tab a { color: #212121; }
        .tabs .tab a:hover, .tabs .tab a.active { color: #2196F3; }
        .tabs .indicator { background-color: #2196F3; }
        .setting-section { padding: 30px 0; border-top: 1px solid #e0e0e0; margin-top: 20px;}
        .section-title { font-weight: 300; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <?php include('includes/header.php');?>
    <?php include('includes/sidebar.php');?>
    
    <main class="mn-inner">
        <div class="row">
            <div class="col s12"><div class="page-title">System Configuration</div></div>
            
            <div class="col s12">
                <div class="card">
                    <div class="card-content">
                        <?php if(isset($msg)){?><div class="succWrap"><strong>SUCCESS</strong> : <?php echo htmlentities($msg); ?> </div><?php } ?>
                        <?php if(isset($error)){?><div class="errorWrap"><strong>ERROR</strong>: <?php echo htmlentities($error); ?> </div><?php } ?>

                        <!-- Company Profile -->
                        <form method="post" class="row">
                            <div class="col s12"><h5 class="section-title">Company Identity</h5></div>
                            <div class="input-field col s12 m6">
                                <input id="cname" name="cname" type="text" value="<?php echo htmlentities($settings['company_name'] ?? 'OrbitCMS'); ?>" required>
                                <label for="cname" class="active">Company Name</label>
                            </div>
                            <div class="col s12 m4">
                                <label>Operating Country</label>
                                <select name="ccode" class="browser-default">
                                    <option value="NG" <?php if(($settings['company_country_code']??'')=='NG')echo'selected';?>>Nigeria</option>
                                    <option value="GB" <?php if(($settings['company_country_code']??'')=='GB')echo'selected';?>>United Kingdom</option>
                                    <option value="US" <?php if(($settings['company_country_code']??'')=='US')echo'selected';?>>United States</option>
                                </select>
                            </div>
                            <div class="col s12 m2" style="margin-top:20px;"><button type="submit" name="update_company" class="btn blue">Update</button></div>
                        </form>

                        <!-- New: Email & Notifications Section -->
                        <form method="post" class="row setting-section">
                            <div class="col s12"><h5 class="section-title">Email & Notifications</h5></div>
                            <div class="input-field col s12 m6 l4">
                                <textarea id="admin_email" name="admin_email" class="materialize-textarea"><?php echo htmlentities($settings['admin_email'] ?? ''); ?></textarea>
                                <label for="admin_email">Admin Notification Email (To)</label>
                            </div>
                            <div class="input-field col s12 m6 l4">
                                <textarea id="manager_emails" name="manager_emails" class="materialize-textarea"><?php echo htmlentities($settings['manager_emails'] ?? ''); ?></textarea>
                                <label for="manager_emails">Manager Emails (BCC)</label>
                            </div>
                            <div class="input-field col s12 m6 l4">
                                <textarea id="cc_emails" name="cc_emails" class="materialize-textarea"><?php echo htmlentities($settings['cc_emails'] ?? ''); ?></textarea>
                                <label for="cc_emails">Additional Emails (CC)</label>
                            </div>
                            <div class="col s12 right-align" style="margin-top:20px;"><button type="submit" name="update_email" class="btn blue">Save Email Settings</button></div>
                        </form>

                        <!-- Tabs for Policy and Holidays -->
                        <div class="row setting-section">
                            <div class="col s12">
                                <ul class="tabs">
                                    <li class="tab col s6"><a href="#policy" class="active">LEAVE POLICY</a></li>
                                    <li class="tab col s6"><a href="#holidays">HOLIDAY CALENDAR</a></li>
                                </ul>
                            </div>

                            <!-- Policy Tab -->
                            <div id="policy" class="col s12" style="padding-top: 20px;">
                                <form method="post">
                                    <div class="row">
                                        <div class="input-field col s12 m4">
                                            <input name="base_leaves" type="number" value="<?php echo $settings['base_max_leaves'] ?? 20; ?>" required>
                                            <label class="active">Base Yearly Quota</label>
                                        </div>
                                        <div class="col s12 m8"><p style="margin-bottom:10px;"><b>Standard Working Days:</b></p>
                                            <?php $days=[1=>'Mon',2=>'Tue',3=>'Wed',4=>'Thu',5=>'Fri',6=>'Sat',0=>'Sun'];
                                            foreach($days as $v=>$n){ $chk=in_array($v,$activeDays)?'checked':'';
                                            echo "<p style='display:inline-block; margin-right:15px;'><input type='checkbox' name='working_days[]' value='$v' id='d$v' $chk><label for='d$v'>$n</label></p>"; } ?>
                                        </div>
                                        <div class="col s12 right-align" style="margin-top:20px;"><button type="submit" name="update_policy" class="btn blue">Save Rules</button></div>
                                    </div>
                                </form>
                            </div>

                            <!-- Holidays Tab -->
                            <div id="holidays" class="col s12" style="padding-top: 20px;">
                                <div class="row">
                                    <div class="col s12 right-align">
                                        <form method="post"><button type="submit" name="fetch_api" class="btn green waves-effect"><i class="material-icons left">cloud_download</i> Sync Calendar</button></form>
                                    </div>
                                    <div class="col s12" style="margin-top:20px;">
                                        <table class="bordered highlight">
                                            <thead><tr><th>Holiday Event</th><th>Scheduled Date</th><th>Actions</th></tr></thead>
                                            <tbody>
                                                <?php 
                                                // Corrected: Fetch as object and format the date properly to prevent '1970' error.
                                                $hols=$dbh->query("SELECT * FROM tblholidays ORDER BY HolidayDate ASC");
                                                while($h=$hols->fetch(PDO::FETCH_OBJ)){ ?>
                                                    <tr>
                                                        <td><?php echo htmlentities($h->HolidayName); ?></td>
                                                        <td><?php echo date("F d, Y", strtotime($h->HolidayDate)); ?></td>
                                                        <td><a href="?del_h=<?php echo $h->id; ?>" class="red-text" onclick=\"return confirm('Remove?')\"><i class="material-icons">delete</i></a></td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php include('includes/footer.php'); ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var elems = document.querySelectorAll('.tabs');
        M.Tabs.init(elems);
        M.updateTextFields(); // Ensures labels for textareas with content are active
    });
    </script>
</body>
</html>