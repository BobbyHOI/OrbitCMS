<?php
// Simple Router
$page = isset($_GET['page']) ? $_GET['page'] : 'welcome';

function render_header() {
    echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OrbitCMS Setup</title>
    <link rel="stylesheet" href="../assets/plugins/materialize/css/materialize.min.css">
    <style>
        body {
            background-color: #f5f5f5;
        }
        .container {
            margin-top: 50px;
        }
        .card-panel {
            padding: 40px;
        }
        .logo {
            max-width: 150px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col m8 offset-m2">
                <div class="card-panel z-depth-2 center-align">
                    <img src="../assets/images/logo.png" alt="OrbitCMS Logo" class="logo">
                    <h4 class="grey-text text-darken-2">OrbitCMS Installation</h4>
HTML;
}

function render_footer() {
    echo <<<HTML
                </div>
            </div>
        </div>
    </div>
    <script src="../assets/plugins/jquery/jquery-2.2.0.min.js"></script>
    <script src="../assets/plugins/materialize/js/materialize.min.js"></script>
</body>
</html>
HTML;
}

function welcome_page() {
    render_header();
    echo <<<'HTML'
<p class="flow-text">Welcome to the OrbitCMS setup wizard. This will guide you through the installation process.</p>
<p>Before you begin, please make sure you have your database credentials and API keys ready.</p>
<a href="?page=form" class="btn waves-effect waves-light">Start Setup</a>
HTML;
    render_footer();
}

function form_page() {
    render_header();
    echo <<<'HTML'
<form action="?page=process" method="post">
    <div class="row">
        <div class="input-field col s12">
            <input id="db_host" type="text" name="DB_HOST" value="localhost" required>
            <label for="db_host">Database Host</label>
        </div>
        <div class="input-field col s12">
            <input id="db_name" type="text" name="DB_NAME" required>
            <label for="db_name">Database Name</label>
        </div>
        <div class="input-field col s12">
            <input id="db_user" type="text" name="DB_USER" required>
            <label for="db_user">Database User</label>
        </div>
        <div class="input-field col s12">
            <input id="db_pass" type="password" name="DB_PASS">
            <label for="db_pass">Database Password</label>
        </div>
        <hr/>
        <p>You can leave the following fields blank and fill them in the `.env` file later.</p>
        <div class="input-field col s12">
            <input id="calendarific_key" type="text" name="CALENDARIFIC_API_KEY">
            <label for="calendarific_key">Calendarific API Key</label>
        </div>
        <div class="input-field col s12">
            <input id="geo_api_key" type="text" name="GEO_API_KEY">
            <label for="geo_api_key">GEO API Key</label>
        </div>
    </div>
    <button type="submit" class="btn waves-effect waves-light">Submit & Install</button>
</form>
HTML;
    render_footer();
}

function process_page() {
    render_header();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo '<p class="red-text">Invalid request.</p><a href="?page=form" class="btn">Go Back</a>';
        render_footer();
        return;
    }

    $env_content = "";
    $required_fields = ['DB_HOST', 'DB_NAME', 'DB_USER'];
    foreach ($_POST as $key => $value) {
        if (in_array($key, $required_fields) && empty($value)) {
             echo '<p class="red-text">Please fill in all required fields.</p><a href="?page=form" class="btn">Go Back</a>';
             render_footer();
             return;
        }
        $env_content .= "$key=\"$value\"\n";
    }
    
    // Add other env variables with default empty values
    $other_vars = [
        'GEO_API_URL' => 'https://api.countrystatecity.in/v1',
        'CALENDARIFIC_API_URL' => 'https://calendarific.com/api/v2',
        'SMTP_HOST' => '',
        'SMTP_USERNAME' => '',
        'SMTP_PASSWORD' => '',
        'SMTP_PORT' => '',
        'SMTP_FROM_EMAIL' => '',
        'SMTP_FROM_NAME' => 'OrbitCMS',
        'BASE_URL' => 'http://' . $_SERVER['HTTP_HOST']
    ];

    foreach ($other_vars as $key => $value) {
        if (!isset($_POST[$key])) {
            $env_content .= "$key=\"$value\"\n";
        }
    }

    // Write .env file
    if (file_put_contents(__DIR__ . '/../.env', $env_content)) {
        echo '<p class="green-text">✓ Successfully created .env file.</p>';
    } else {
        echo '<p class="red-text">✗ Error creating .env file. Please check file permissions.</p>';
        render_footer();
        return;
    }

    // Database Setup
    $db_host = $_POST['DB_HOST'];
    $db_name = $_POST['DB_NAME'];
    $db_user = $_POST['DB_USER'];
    $db_pass = $_POST['DB_PASS'];

    try {
        $dbh = new PDO("mysql:host=$db_host", $db_user, $db_pass);
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $dbh->exec("CREATE DATABASE IF NOT EXISTS `$db_name`;");
        $dbh->exec("USE `$db_name`;");
        echo '<p class="green-text">✓ Database created successfully.</p>';

        $sql = file_get_contents('OrbitCMS.sql');
        $dbh->exec($sql);
        echo '<p class="green-text">✓ Tables imported successfully.</p>';
        
        // Update passwords
        $admin_pass = password_hash('admin123', PASSWORD_DEFAULT);
        $manager_pass = password_hash('manager123', PASSWORD_DEFAULT);

        $stmt = $dbh->prepare("UPDATE `admin` SET `Password` = :pass WHERE `UserName` = 'admin'");
        $stmt->execute([':pass' => $admin_pass]);

        $stmt = $dbh->prepare("UPDATE `tblemployees` SET `Password` = :pass WHERE `EmailId` = 'manager@gmail.com'");
        $stmt->execute([':pass' => $manager_pass]);

        echo '<p class="green-text">✓ Default admin and manager passwords have been set.</p>';
        echo '<p class="flow-text">Installation Complete!</p>';
        echo '<p>You can now <a href="../index.php">log in</a> with the default credentials.</p>';
        echo '<p><b>Admin:</b> admin / admin123</p>';
        echo '<p><b>Manager:</b> manager@gmail.com / manager123</p>';


    } catch (PDOException $e) {
        echo '<p class="red-text">✗ Database error: ' . $e->getMessage() . '</p>';
    }

    render_footer();
}

// Route to the correct page
switch ($page) {
    case 'form':
        form_page();
        break;
    case 'process':
        process_page();
        break;
    default:
        welcome_page();
        break;
}
