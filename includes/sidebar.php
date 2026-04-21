<?php
/**
 * OrbitCMS - Professional Unified Sidebar (Optimized)
 */
$base_path = (strpos($_SERVER['SCRIPT_NAME'], '/admin/') !== false) ? '../' : '';

// Get all user data from the pre-loaded session variable
$user_session_data = $_SESSION['user_data'] ?? [];

$sidebar_user_name = $user_session_data['name'] ?? "User";
$sidebar_emp_id = $user_session_data['empid'] ?? "";
$sidebar_img = $user_session_data['img'] ?? "assets/images/profile-image.png";
$sidebar_role = $user_session_data['role'] ?? 0;

/* --- OLD INEFFICIENT QUERY (COMMENTED OUT) ---
<?php
if (!isset($dbh)) {
    include_once(dirname(__FILE__) . '/config.php');
}

$sidebar_user_name = "User";
$sidebar_emp_id = "";
$sidebar_img = "assets/images/profile-image.png";
$sidebar_role = 0;

if(isset($_SESSION['eid'])) {
    try {
        $stmt = $dbh->prepare("SELECT FirstName, LastName, EmpId, role, imageFileName FROM tblemployees WHERE id = ?");
        $stmt->execute([$_SESSION['eid']]);
        $user_data = $stmt->fetch(PDO::FETCH_OBJ);

        if($user_data) {
            $sidebar_user_name = htmlentities($user_data->FirstName . " " . $user_data->LastName);
            $sidebar_emp_id = htmlentities($user_data->EmpId);
            $sidebar_role = (int)$user_data->role;
            if(!empty($user_data->imageFileName) && $user_data->imageFileName !== 'profile-image.png') {
                $sidebar_img = "uploads/" . $user_data->imageFileName;
            }
        }
    } catch (Exception $e) { error_log($e->getMessage()); }
}
?>
*/
?>
<aside id="slide-out" class="side-nav white fixed">
    <div class="side-nav-wrapper">
        <div class="sidebar-profile">
            <div class="sidebar-profile-image">
                <img src="<?php echo $base_path . $sidebar_img; ?>" class="circle" alt="Profile">
            </div>
            <div class="sidebar-profile-info">
                <p><?php echo $sidebar_user_name; ?></p>
                <span><?php echo $sidebar_emp_id; ?></span>
            </div>
        </div>

        <ul class="sidebar-menu collapsible collapsible-accordion" data-collapsible="accordion">
            <li class="no-padding"><a class="waves-effect waves-grey" href="<?php echo $base_path; ?>myprofile.php"><i class="material-icons">account_box</i>My Profile</a></li>
            <li class="no-padding"><a class="waves-effect waves-grey" href="<?php echo $base_path; ?>emp-changepassword.php"><i class="material-icons">lock_outline</i>Security</a></li>
            <li class="no-padding"><a class="waves-effect waves-grey" href="<?php echo $base_path; ?>apply-leave.php"><i class="material-icons">add_circle_outline</i>Apply Leave</a></li>
            <li class="no-padding"><a class="waves-effect waves-grey" href="<?php echo $base_path; ?>leavehistory.php"><i class="material-icons">history</i>Leave History</a></li>

            <?php if($sidebar_role === 3): // ADMIN ONLY ?>
            <li class="no-padding">
                <a class="collapsible-header waves-effect waves-grey"><i class="material-icons">settings_applications</i>System Policy<i class="nav-drop-icon material-icons">keyboard_arrow_right</i></a>
                <div class="collapsible-body">
                    <ul>
                        <li><a href="<?php echo $base_path; ?>admin/system-settings.php">Leave Rules</a></li>
                        <li><a href="<?php echo $base_path; ?>admin/addleavetype.php">Add Leave Type</a></li>
                        <li><a href="<?php echo $base_path; ?>admin/manageleavetype.php">Manage Leave Types</a></li>
                    </ul>
                </div>
            </li>
            <li class="no-padding">
                <a class="collapsible-header waves-effect waves-grey"><i class="material-icons">apps</i>Organization<i class="nav-drop-icon material-icons">keyboard_arrow_right</i></a>
                <div class="collapsible-body">
                    <ul>
                        <li><a href="<?php echo $base_path; ?>admin/adddepartment.php">Add Department</a></li>
                        <li><a href="<?php echo $base_path; ?>admin/managedepartments.php">Manage Departments</a></li>
                        <li><a href="<?php echo $base_path; ?>admin/addemployee.php">Add Employee</a></li>
                        <li><a href="<?php echo $base_path; ?>admin/manageemployee.php">Manage Employees</a></li>
                    </ul>
                </div>
            </li>
            <?php endif; ?>

            <?php if($sidebar_role === 2 || $sidebar_role === 3): // MANAGER OR ADMIN ?>
            <li class="no-padding">
                <a class="collapsible-header waves-effect waves-grey"><i class="material-icons">assignment_turned_in</i>Leave Management<i class="nav-drop-icon material-icons">keyboard_arrow_right</i></a>
                <div class="collapsible-body">
                    <ul>
                        <li><a href="<?php echo $base_path; ?>admin/leaves.php">All Leaves</a></li>
                        <li><a href="<?php echo $base_path; ?>admin/pending-leaves.php">Pending Leaves</a></li>
                        <li><a href="<?php echo $base_path; ?>admin/approved-leaves.php">Approved Leaves</a></li>
                        <li><a href="<?php echo $base_path; ?>admin/not-approved-leaves.php">Not Approved Leaves</a></li>
                    </ul>
                </div>
            </li>
            <?php endif; ?>

            <li class="no-padding">
                <a class="waves-effect waves-grey" href="<?php echo $base_path; ?>logout.php"><i class="material-icons">exit_to_app</i>Sign Out</a>
            </li>
        </ul>
    </div>
</aside>