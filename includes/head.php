<?php
/**
 * OrbitCMS - Modular Head Partial
 * Contains all meta tags, CSS links, and global styles.
 */

// Dynamically determine the path prefix for assets based on the current directory
$head_prefix = (strpos($_SERVER['REQUEST_URI'], '/admin/') !== false) ? '../' : '';
?>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
<meta name="description" content="OrbitCMS - Professional Leave Management System" />
<meta name="author" content="OrbitCMS Team" />

<!-- External Plugins & Fonts -->
<link type="text/css" rel="stylesheet" href="<?php echo $head_prefix; ?>assets/plugins/materialize/css/materialize.min.css"/>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<link href="<?php echo $head_prefix; ?>assets/plugins/material-preloader/css/materialPreloader.min.css" rel="stylesheet"> 
<link href="<?php echo $head_prefix; ?>assets/plugins/datatables/css/jquery.dataTables.min.css" rel="stylesheet">

<!-- Core Theme Styles -->
<link href="<?php echo $head_prefix; ?>assets/css/alpha.min.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo $head_prefix; ?>assets/css/custom.css" rel="stylesheet" type="text/css"/>

<style>
    /* Professional Theme Overrides */
    .mn-header nav { 
        background-color: #212121 !important; 
    }
    
    .header-title { 
        display: flex; 
        align-items: center; 
    }

    .errorWrap { 
        padding: 12px; 
        margin: 0 0 20px 0; 
        background: #fff; 
        border-left: 4px solid #dd3d36; 
        box-shadow: 0 1px 2px 0 rgba(0,0,0,.1); 
    }

    .succWrap { 
        padding: 12px; 
        margin: 0 0 20px 0; 
        background: #fff; 
        border-left: 4px solid #5cb85c; 
        box-shadow: 0 1px 2px 0 rgba(0,0,0,.1); 
    }

    .page-title { 
        font-weight: 300 !important; 
        color: #444; 
        margin-bottom: 30px; 
        font-size: 2.2rem;
    }

    /* Responsive Mobile Tweaks */
    @media only screen and (max-width: 600px) {
        .page-title { font-size: 1.6rem; }
    }
</style>