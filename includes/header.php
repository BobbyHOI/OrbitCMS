<?php
/**
 * OrbitCMS - Dynamic Master Header (Optimized)
 */
$h_prefix = (strpos($_SERVER['SCRIPT_NAME'], '/admin/') !== false) ? '../' : '';
$h_title = $header_title ?? '| Portal';
$h_color = $header_color ?? 'grey darken-4';
$h_logo  = $header_logo ?? 'assets/images/logo.png';

// Get unread count from the session. This is pre-loaded on login and updated via AJAX.
$h_unread = $_SESSION['unread_leaves'] ?? 0;

/* --- OLD INEFFICIENT QUERY (COMMENTED OUT) ---
<?php 
$h_unread = 0;
try { if(isset($dbh)) { $h_unread = $dbh->query("SELECT count(id) FROM tblleaves WHERE IsRead=0")->fetchColumn(); } } catch(Exception $e){}
?>
*/
?>
<div class="loader-bg"></div>
<div class="loader">
    <div class="preloader-wrapper big active">
        <div class="spinner-layer spinner-blue"><div class="circle-clipper left"><div class="circle"></div></div><div class="gap-patch"><div class="circle"></div></div><div class="circle-clipper right"><div class="circle"></div></div></div>
        <div class="spinner-layer spinner-spinner-teal lighten-1"><div class="circle-clipper left"><div class="circle"></div></div><div class="gap-patch"><div class="circle"></div></div><div class="circle-clipper right"><div class="circle"></div></div></div>
        <div class="spinner-layer spinner-yellow"><div class="circle-clipper left"><div class="circle"></div></div><div class="gap-patch"><div class="circle"></div></div><div class="circle-clipper right"><div class="circle"></div></div></div>
        <div class="spinner-layer spinner-green"><div class="circle-clipper left"><div class="circle"></div></div><div class="gap-patch"><div class="circle"></div></div><div class="circle-clipper right"><div class="circle"></div></div></div>
    </div>
</div>
<div class="mn-content fixed-sidebar">
    <header class="mn-header navbar-fixed">
        <nav class="<?php echo $h_color; ?>">
            <div class="nav-wrapper row">
                <section class="material-design-hamburger navigation-toggle">
                    <a href="javascript:void(0)" data-activates="slide-out" class="button-collapse show-on-large material-design-hamburger__icon">
                        <span class="material-design-hamburger__layer"></span>
                    </a>
                </section>
                <div class="header-title col s3" style="display: flex; align-items: center;">      
                    <img src="<?php echo $h_prefix . $h_logo; ?>" alt="Logo" style="height: 40px; margin-right: 10px;">
                    <span class="chapter-title"><?php echo $h_title; ?></span>
                </div>
                
                <ul class="right col s9 m3 nav-right-menu">
                    <li class="hide-on-small-and-down">
                        <a href="<?php echo $h_prefix; ?>refresh_notifications.php" title="Refresh Notifications">
                            <i class="material-icons">refresh</i>
                        </a>
                    </li>
                    <li class="hide-on-small-and-down">
                        <a href="javascript:void(0)" data-activates="dropdown1" class="dropdown-button dropdown-right show-on-large">
                            <i class="material-icons">notifications_none</i>
                            <span id="unread-leaves-badge" class="badge amber black-text" style="border-radius: 50%;"><?php echo $h_unread; ?></span>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>

<script>
document.addEventListener('visibilitychange', function() {
    if (document.visibilityState === 'visible') {
        // Use Fetch API to call the refresh script
        fetch('<?php echo $h_prefix; ?>refresh_notifications.php', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            },
        })
        .then(response => response.json())
        .then(data => {
            // Update the badge count on the screen
            document.getElementById('unread-leaves-badge').textContent = data.unread_count;
        })
        .catch(error => console.error('Error refreshing notifications:', error));
    }
});
</script>