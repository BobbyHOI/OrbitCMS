<?php
/**
 * OrbitCMS - Core Footer Scripts
 */
$f_prefix = (strpos($_SERVER['REQUEST_URI'], '/admin/') !== false) ? '../' : '';
?>
    <!-- Core Jquery and Materialize Plugins -->
    <script src="<?php echo $f_prefix; ?>assets/plugins/jquery/jquery-2.2.0.min.js"></script>
    <script src="<?php echo $f_prefix; ?>assets/plugins/materialize/js/materialize.min.js"></script>
    <script src="<?php echo $f_prefix; ?>assets/plugins/material-preloader/js/materialPreloader.min.js"></script>
    <script src="<?php echo $f_prefix; ?>assets/plugins/datatables/js/jquery.dataTables.min.js"></script>
    
    <!-- Theme JS -->
    <script src="<?php echo $f_prefix; ?>assets/js/alpha.min.js"></script>

    <!-- Global Flash Message Display Handler -->
    <script>
    $(document).ready(function() {
        // This script checks for any flash messages passed from PHP sessions
        // and displays them at the top of the main content area.

        <?php if(isset($_SESSION['msg']) && $_SESSION['msg'] !== '') { ?>
            // Create and prepend the success message
            var successMessage = '<?php echo addslashes($_SESSION['msg']); ?>';
            $('main').prepend('<div class="succWrap"><strong>SUCCESS</strong>: ' + successMessage + '</div>');
            <?php $_SESSION['msg'] = ''; // Clear the message after displaying ?>
        <?php } ?>

        <?php if(isset($_SESSION['error']) && $_SESSION['error'] !== '') { ?>
            // Create and prepend the error message
            var errorMessage = '<?php echo addslashes($_SESSION['error']); ?>';
            $('main').prepend('<div class="errorWrap"><strong>ERROR</strong>: ' + errorMessage + '</div>');
            <?php $_SESSION['error'] = ''; // Clear the message after displaying ?>
        <?php } ?>

        // Auto-hide messages after 5 seconds for a cleaner user experience
        if($('.succWrap, .errorWrap').length) {
            setTimeout(function() {
                $('.succWrap, .errorWrap').fadeOut('slow');
            }, 5000);
        }
    });
    </script>
