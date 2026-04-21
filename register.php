<?php
session_start();
require_once(__DIR__ . '/includes/config.php');
require_once(__DIR__ . '/sendmail.php');

global $dbh, $msg, $error;

if (isset($_POST['add'])) {
    $empid = $_POST['empcode'];
    $fname = $_POST['firstName'];
    $lname = $_POST['lastName'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $gender = $_POST['gender'];
    $dob = $_POST['dob'];
    $department = $_POST['department'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $country = $_POST['country'];
    $mobileno = $_POST['mobileno'];
    $hash = bin2hex(random_bytes(32));

    try {
        $sql = "INSERT INTO tblemployees(EmpId, FirstName, LastName, EmailId, Password, Gender, Dob, Department, Address, City, State, Country, Phonenumber, hash, role, Status, imageFileName) 
                VALUES(:eid, :fn, :ln, :email, :pw, :gen, :dob, :dept, :addr, :city, :state, :country, :phone, :hash, 1, 4, 'profile-image.png')";
        $dbh->prepare($sql)->execute([
            ':eid' => $empid,
            ':fn' => $fname,
            ':ln' => $lname,
            ':email' => $email,
            ':pw' => $password,
            ':gen' => $gender,
            ':dob' => $dob,
            ':dept' => $department,
            ':addr' => $address,
            ':city' => $city,
            ':state' => $state,
            ':country' => $country,
            ':phone' => $mobileno,
            ':hash' => $hash
        ]);

        $vLink = BASE_URL . "verify_email.php?email=$email&hash=$hash";
        $subject = "OrbitCMS Account Verification";
        $body = "Hi {$fname},<br><br>Thank you for registering. Please click the link below to verify your email address:<br><br><a href='{$vLink}' target='_blank'>Verify Your Account</a><br><br>Kind regards,<br>OrbitCMS";
        
        sendmail($subject, $email, "no-reply@orbitcms.com", $body, $fname);
        
        $msg = "Success! A verification link has been sent to your email address.";

    } catch (PDOException $e) {
        $error = "Registration failed. The Employee Code or Email may already be in use.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>OrbitCMS | Register</title>
    <?php include('includes/head.php'); ?>
    <style>
        body { background-color: #f5f5f5; display: flex; min-height: 100vh; flex-direction: column; }
        main { flex: 1 0 auto; display: flex; align-items: center; justify-content: center; padding: 20px 0; }
        .registration-box { width: 100%; max-width: 900px; }
    </style>
</head>
<body>
    <main>
        <div class="registration-box">
            <div class="card">
                <div class="card-content">
                    <h2 class="card-title center-align" style="font-weight: 300; margin-bottom: 30px;">Create New Account</h2>
                    
                    <?php if($error){?><div class="errorWrap"><strong>ERROR</strong>: <?php echo htmlentities($error); ?></div><?php } ?>
                    <?php if($msg){?><div class="succWrap"><strong>SUCCESS</strong>: <?php echo htmlentities($msg); ?></div><?php }?>

                    <form method="post" name="addemp" onsubmit="return validatePassword();">
                        <div class="row">
                            <div class="col m6 s12">
                                <div class="row">
                                    <div class="input-field col s12"><input name="empcode" type="text" required autocomplete="off"><label for="empcode">Employee Code</label></div>
                                    <div class="input-field col m6 s12"><input name="firstName" type="text" required><label for="firstName">First Name</label></div>
                                    <div class="input-field col m6 s12"><input name="lastName" type="text" required><label for="lastName">Last Name</label></div>
                                    <div class="input-field col s12"><input name="email" type="email" required autocomplete="off"><label for="email">Email Address</label></div>
                                    <div class="input-field col m6 s12"><input id="password" name="password" type="password" required><label for="password">Password</label></div>
                                    <div class="input-field col m6 s12"><input name="confirmpassword" type="password" required><label for="confirmpassword">Confirm Password</label></div>
                                </div>
                            </div>
                            <div class="col m6 s12">
                                <div class="row">
                                    <div class="input-field col m6 s12"><input id="mobileno" name="mobileno" type="number" required autocomplete="off"><label for="mobileno">Mobile Number</label></div>
                                    <div class="input-field col m6 s12"><input name="dob" type="text" class="datepicker" required><label for="dob">Date of Birth</label></div>
                                    <div class="input-field col s12">
                                        <select name="gender" required>
                                            <option value="" disabled selected>Select Gender...</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                        </select>
                                    </div>
                                    <div class="input-field col s12">
                                        <select name="department" required>
                                            <option value="" disabled selected>Select Department...</option>
                                            <?php
                                            $q = $dbh->query("SELECT DepartmentName FROM tbldepartments ORDER BY DepartmentName");
                                            while($r = $q->fetch(PDO::FETCH_OBJ)) {
                                                echo '<option value="' . htmlentities($r->DepartmentName) . '">' . htmlentities($r->DepartmentName) . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="input-field col m4 s12">
                                <select name="country" id="country" required>
                                    <option value="" disabled selected>Select Country</option>
                                </select>
                            </div>
                            <div class="input-field col m4 s12">
                                <select name="state" id="state" required>
                                    <option value="" disabled selected>Select State</option>
                                </select>
                            </div>
                            <div class="input-field col m4 s12">
                                <select name="city" id="city" required>
                                    <option value="" disabled selected>Select City</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                             <div class="input-field col s12">
                                <input name="address" type="text" required autocomplete="off">
                                <label for="address">Address</label>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col s12 center-align" style="margin-top: 20px;">
                                <a href="index.php" class="waves-effect waves-grey btn-flat">Login Instead</a>
                                <button type="submit" name="add" id="add" class="waves-effect waves-light btn indigo">Register</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
    <?php include('includes/footer.php'); ?>
    <script>
        $(document).ready(function() {
            // Initialize static elements first
            $('select:not(#country, #state, #city)').material_select();
            $('.datepicker').pickadate({ format: 'yyyy-mm-dd', selectMonths: true, selectYears: 80, max: true });

            const geoApiKey = '<?php echo GEO_API_KEY; ?>';
            if (!geoApiKey) {
                console.error('GEO_API_KEY is not defined. Country dropdowns will not work.');
                return; // Stop if the key is missing
            }
            const geoHeaders = { "X-CSCAPI-KEY": geoApiKey };

            // --- Country Loading ---
            fetch('https://api.countrystatecity.in/v1/countries', { headers: geoHeaders })
                .then(response => response.ok ? response.json() : Promise.reject('Failed to load countries'))
                .then(data => {
                    data.sort((a, b) => a.name.localeCompare(b.name));
                    data.forEach(country => {
                        $('#country').append(`<option value="${country.iso2}">${country.name}</option>`);
                    });
                    $('#country').material_select(); // Initialize after populating
                })
                .catch(error => console.error('Country Fetch Error:', error));

            // --- State Loading ---
            $('#country').change(function() {
                const countryIso = $(this).val();
                $('#state, #city').empty().append('<option value="" disabled selected>Select...</option>');

                fetch(`https://api.countrystatecity.in/v1/countries/${countryIso}/states`, { headers: geoHeaders })
                    .then(response => response.ok ? response.json() : Promise.reject('Failed to load states'))
                    .then(data => {
                        data.sort((a, b) => a.name.localeCompare(b.name));
                        data.forEach(state => {
                            $('#state').append(`<option value="${state.iso2}">${state.name}</option>`);
                        });
                        $('#state, #city').material_select(); // Re-initialize
                    })
                    .catch(error => console.error('State Fetch Error:', error));
            });

            // --- City Loading ---
            $('#state').change(function() {
                const countryIso = $('#country').val();
                const stateIso = $(this).val();
                $('#city').empty().append('<option value="" disabled selected>Select City</option>');

                fetch(`https://api.countrystatecity.in/v1/countries/${countryIso}/states/${stateIso}/cities`, { headers: geoHeaders })
                    .then(response => response.ok ? response.json() : Promise.reject('Failed to load cities'))
                    .then(data => {
                        data.sort((a, b) => a.name.localeCompare(b.name));
                        data.forEach(city => {
                            $('#city').append(`<option value="${city.name}">${city.name}</option>`);
                        });
                        $('#city').material_select(); // Re-initialize
                    })
                    .catch(error => console.error('City Fetch Error:', error));
            });
        });

        function validatePassword() {
            if ($('#password').val() !== $('input[name="confirmpassword"]').val()) {
                alert("Passwords do not match! Please try again.");
                return false;
            }
            return true;
        }
    </script>
</body>
</html>