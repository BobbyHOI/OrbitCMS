<?php
session_start();
$error = null;
$msg = null;
require_once(__DIR__ . '/../controllers/EmployeeController.php');

if (strlen($_SESSION['alogin']) == 0) {
    header('location:../index.php');
    exit;
}

// Handle form submission
$employeeController = new EmployeeController();
$employeeController->create();

global $dbh;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin | Add Employee</title>
    <?php include('includes/head.php'); ?>
</head>
<body>
    <?php include('includes/header.php'); ?>
    <?php include('includes/sidebar.php'); ?>
    <main class="mn-inner">
        <div class="row">
            <div class="col s12">
                <div class="page-title">Add Employee</div>
            </div>
            <div class="col s12 m12 l12">
                <div class="card">
                    <div class="card-content">
                        <form id="example-form" method="post" name="addemp">
                            <div class="wizard-content">
                                <?php if (isset($_SESSION['error'])) { ?><div class="errorWrap"><strong>ERROR</strong>: <?php echo htmlentities($_SESSION['error']); unset($_SESSION['error']); ?></div><?php } ?>
                                <?php if (isset($_SESSION['msg'])) { ?><div class="succWrap"><strong>SUCCESS</strong>: <?php echo htmlentities($_SESSION['msg']); unset($_SESSION['msg']); ?></div><?php } ?>

                                <div class="row">
                                    <div class="col m6 s12">
                                        <div class="row">
                                            <div class="input-field col s12">
                                                <input name="empcode" id="empcode" onblur="checkAvailabilityEmpid()" type="text" autocomplete="off" required>
                                                <label for="empcode">Employee Code</label>
                                                <span id="empid-availability" style="font-size:12px;"></span>
                                            </div>
                                            <div class="input-field col m6 s12">
                                                <input id="firstName" name="firstName" type="text" required>
                                                <label for="firstName">First Name</label>
                                            </div>
                                            <div class="input-field col m6 s12">
                                                <input id="lastName" name="lastName" type="text" autocomplete="off" required>
                                                <label for="lastName">Last Name</label>
                                            </div>
                                            <div class="input-field col s12">
                                                <input name="email" type="email" id="email" onblur="checkAvailabilityEmailid()" autocomplete="off" required>
                                                <label for="email">Email</label>
                                                <span id="emailid-availability" style="font-size:12px;"></span>
                                            </div>
                                            <div class="input-field col s12">
                                                <input id="password" name="password" type="password" autocomplete="off" required>
                                                <label for="password">Password</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col m6 s12">
                                        <div class="row">
                                            <div class="input-field col m6 s12">
                                                <select name="gender" autocomplete="off">
                                                    <option value="" disabled selected>Gender...</option>
                                                    <option value="Male">Male</option>
                                                    <option value="Female">Female</option>
                                                </select>
                                            </div>
                                            <div class="input-field col m6 s12">
                                                <input id="birthdate" name="dob" type="text" class="datepicker" autocomplete="off">
                                                <label for="birthdate">Birthdate</label>
                                            </div>
                                            <div class="input-field col s12">
                                                <select name="department" autocomplete="off">
                                                    <option value="" disabled selected>Department...</option>
                                                    <?php
                                                    // This is acceptable here, as it's a simple read for a form that doesn't change often.
                                                    $sql = "SELECT DepartmentName from tbldepartments";
                                                    $query = $dbh->prepare($sql);
                                                    $query->execute();
                                                    $results = $query->fetchAll(PDO::FETCH_OBJ);
                                                    if ($query->rowCount() > 0) {
                                                        foreach ($results as $result) {
                                                            echo '<option value="' . htmlentities($result->DepartmentName) . '">' . htmlentities($result->DepartmentName) . '</option>';
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                             <div class="input-field col s12">
                                                <select name="role" autocomplete="off">
                                                    <option value="" disabled selected>Assign Role...</option>
                                                    <?php
                                                    $sql = "SELECT id, RoleName from tblroles";
                                                    $query = $dbh->prepare($sql);
                                                    $query->execute();
                                                    $results = $query->fetchAll(PDO::FETCH_OBJ);
                                                    if ($query->rowCount() > 0) {
                                                        foreach ($results as $result) {
                                                            echo '<option value="' . $result->id . '">' . htmlentities($result->RoleName) . '</option>';
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="input-field col m6 s12">
                                                 <select name="country" id="country" required>
                                                    <option value="">Select Country</option>
                                                </select>
                                            </div>
                                            <div class="input-field col m6 s12">
                                                <select name="state" id="state" required>
                                                    <option value="">Select State</option>
                                                </select>
                                            </div>
                                             <div class="input-field col s12">
                                                <select name="city" id="city" required>
                                                    <option value="">Select City</option>
                                                </select>
                                            </div>
                                            <div class="input-field col s12">
                                                <input id="address" name="address" type="text" autocomplete="off" required>
                                                <label for="address">Address</label>
                                            </div>
                                            <div class="input-field col m6 s12">
                                                <input id="phone" name="mobileno" type="number" autocomplete="off" required>
                                                <label for="phone">Mobile Number</label>
                                            </div>
                                             <div class="input-field col m6 s12">
                                                <input id="extraLeaves" name="extraLeaves" type="number" autocomplete="off" value="0" required>
                                                <label for="extraLeaves">Bonus Leaves</label>
                                            </div>
                                            <div class="input-field col s12">
                                                <button type="submit" name="add" id="add" class="waves-effect waves-light btn indigo m-b-xs">ADD</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php include('includes/footer.php'); ?>
    <script>
        const geoApiKey = '<?php echo GEO_API_KEY; ?>';
        const geoHeaders = { "X-CSCAPI-KEY": geoApiKey };

        $(document).ready(function() {
            $('select').material_select();
            $('.datepicker').pickadate({ format: 'yyyy-mm-dd', selectMonths: true, selectYears: 80 });

            fetch('https://api.countrystatecity.in/v1/countries', { headers: geoHeaders })
                .then(response => response.json())
                .then(data => {
                    data.sort((a, b) => a.name.localeCompare(b.name));
                    data.forEach(country => {
                        $('#country').append(`<option value="${country.iso2}">${country.name}</option>`);
                    });
                    $('#country').material_select(); // Re-initialize materialize select
                });

            $('#country').change(function() {
                const countryIso = $(this).val();
                $('#state').empty().append('<option value="">Select State</option>');
                $('#city').empty().append('<option value="">Select City</option>');

                if (countryIso) {
                    fetch(`https://api.countrystatecity.in/v1/countries/${countryIso}/states`, { headers: geoHeaders })
                        .then(response => response.json())
                        .then(data => {
                            data.sort((a, b) => a.name.localeCompare(b.name));
                            data.forEach(state => {
                                $('#state').append(`<option value="${state.iso2}">${state.name}</option>`);
                            });
                            $('#state').material_select(); // Re-initialize
                        });
                }
                $('#city').material_select(); // Re-initialize
            });

            $('#state').change(function() {
                const countryIso = $('#country').val();
                const stateIso = $(this).val();
                $('#city').empty().append('<option value="">Select City</option>');

                if (countryIso && stateIso) {
                    fetch(`https://api.countrystatecity.in/v1/countries/${countryIso}/states/${stateIso}/cities`, { headers: geoHeaders })
                        .then(response => response.json())
                        .then(data => {
                            data.sort((a, b) => a.name.localeCompare(b.name));
                            data.forEach(city => {
                                $('#city').append(`<option value="${city.name}">${city.name}</option>`);
                            });
                            $('#city').material_select(); // Re-initialize
                        });
                }
            });
        });

        function checkAvailabilityEmpid() {
            $("#loaderIcon").show();
            jQuery.ajax({
                url: "check_availability.php",
                data: 'empcode=' + $("#empcode").val(),
                type: "POST",
                success: function(data) {
                    $("#empid-availability").html(data);
                    $("#loaderIcon").hide();
                },
                error: function() {}
            });
        }

        function checkAvailabilityEmailid() {
            $("#loaderIcon").show();
            jQuery.ajax({
                url: "check_availability.php",
                data: 'emailid=' + $("#email").val(),
                type: "POST",
                success: function(data) {
                    $("#emailid-availability").html(data);
                    $("#loaderIcon").hide();
                },
                error: function() {}
            });
        }
    </script>
</body>
</html>