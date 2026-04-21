<?php
/**
 * OrbitCMS - Edit Employee (Refactored MVC View)
 */
session_start();
require_once(__DIR__ . '/../controllers/EmployeeController.php');
include('../includes/config.php'); // Included for dropdown queries as requested

// Security Check: Ensure user is logged in
if (strlen($_SESSION['alogin']) == 0) {   
    header('location:../index.php');
    exit;
}

// --- Controller Interaction ---
$employeeId = intval($_GET['empid']);
$employeeController = new EmployeeController();
$employeeController->update(); // Handle form submission
$employee = $employeeController->getEmployee($employeeId); // Fetch data for the view

// Redirect if employee not found
if (!$employee) {
    $_SESSION['error'] = "Employee not found.";
    header('location:manageemployee.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin | Edit Employee</title>
    <?php include('includes/head.php');?>
</head>
<body>
    <?php include('includes/header.php');?>
    <?php include('includes/sidebar.php');?>
    <main class="mn-inner">
        <div class="row">
            <div class="col s12">
                <div class="page-title">Edit Employee</div>
            </div>
            <div class="col s12 m12 l12">
                <div class="card">
                    <div class="card-content">
                        <?php if (isset($_SESSION['error'])) { ?><div class="errorWrap"><strong>ERROR</strong>: <?php echo htmlentities($_SESSION['error']); unset($_SESSION['error']); ?></div><?php } ?>
                        <?php if (isset($_SESSION['msg'])) { ?><div class="succWrap"><strong>SUCCESS</strong>: <?php echo htmlentities($_SESSION['msg']); unset($_SESSION['msg']); ?></div><?php } ?>
                        
                        <form method="post">
                            <div class="row">
                                <div class="col m6 s12">
                                    <div class="row">
                                        <div class="input-field col s12">
                                            <input type="text" value="<?php echo htmlentities($employee->EmpId); ?>" readonly>
                                            <label class="active">Employee ID</label>
                                        </div>
                                        <div class="input-field col m6 s12">
                                            <input name="firstName" type="text" value="<?php echo htmlentities($employee->FirstName); ?>" required>
                                            <label class="active">First Name</label>
                                        </div>
                                        <div class="input-field col m6 s12">
                                            <input name="lastName" type="text" value="<?php echo htmlentities($employee->LastName); ?>" required>
                                            <label class="active">Last Name</label>
                                        </div>
                                        <div class="input-field col s12">
                                            <input type="email" value="<?php echo htmlentities($employee->EmailId); ?>" readonly>
                                            <label class="active">Email</label>
                                        </div>
                                        <div class="input-field col s12">
                                            <select name="role" required>
                                                <option value="" disabled>Assign Role...</option>
                                                <?php 
                                                // This direct DB call is preserved as per user request.
                                                $sql = "SELECT * FROM tblroles";
                                                $qR = $dbh->query($sql);
                                                while($rR = $qR->fetch(PDO::FETCH_OBJ)){
                                                    $sel = ($employee->Role == $rR->id) ? 'selected' : '';
                                                    echo "<option value='{$rR->id}' $sel>{$rR->RoleName}</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col m6 s12">
                                    <div class="row">
                                        <div class="input-field col m6 s12">
                                            <select name="gender">
                                                <option value="" disabled>Gender...</option>
                                                <option value="Male" <?php if ($employee->Gender == 'Male') echo 'selected'; ?>>Male</option>
                                                <option value="Female" <?php if ($employee->Gender == 'Female') echo 'selected'; ?>>Female</option>
                                            </select>
                                        </div>
                                        <div class="input-field col m6 s12">
                                            <input name="dob" type="text" class="datepicker" value="<?php echo htmlentities($employee->Dob); ?>">
                                            <label class="active">Birthdate</label>
                                        </div>
                                        <div class="input-field col s12">
                                            <select name="department" required>
                                                <option value="" disabled>Department...</option>
                                                <?php 
                                                // This direct DB call is preserved as per user request.
                                                $sql = "SELECT DepartmentName FROM tbldepartments";
                                                $qD = $dbh->query($sql);
                                                while($rD = $qD->fetch(PDO::FETCH_OBJ)){
                                                    $sel = ($employee->Department == $rD->DepartmentName) ? 'selected' : '';
                                                    echo "<option value='{$rD->DepartmentName}' $sel>{$rD->DepartmentName}</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="input-field col m6 s12">
                                            <select name="country" id="country" required></select>
                                        </div>
                                        <div class="input-field col m6 s12">
                                            <select name="state" id="state" required></select>
                                        </div>
                                        <div class="input-field col s12">
                                            <select name="city" id="city" required></select>
                                        </div>
                                        <div class="input-field col s12">
                                            <input name="address" type="text" value="<?php echo htmlentities($employee->Address); ?>" required>
                                            <label class="active">Address</label>
                                        </div>
                                        <div class="input-field col m6 s12">
                                            <input name="mobileno" type="number" value="<?php echo htmlentities($employee->Phonenumber); ?>" required>
                                            <label class="active">Mobile Number</label>
                                        </div>
                                        <div class="input-field col m6 s12">
                                            <input name="extraLeaves" type="number" value="<?php echo htmlentities($employee->ExtraLeaves); ?>">
                                            <label class="active">Bonus Leaves</label>
                                        </div>
                                        <div class="input-field col s12">
                                            <button type="submit" name="update" class="waves-effect waves-light btn indigo m-b-xs">UPDATE</button>
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
    <?php include('includes/footer.php');?>
    <script>
        // --- Dynamic Location Dropdowns ---
        const geoApiKey = '<?php echo GEO_API_KEY; ?>';
        const geoHeaders = { "X-CSCAPI-KEY": geoApiKey };
        
        const initialCountryIso = '<?php echo $employee->Country; ?>';
        const initialStateIso = '<?php echo $employee->State; ?>';
        const initialCityName = '<?php echo $employee->City; ?>';

        $(document).ready(function() {
            $('select').material_select();
            $('.datepicker').pickadate({ format: 'yyyy-mm-dd', selectMonths: true, selectYears: 80 });

            // 1. Fetch & Populate Countries
            fetch('https://api.countrystatecity.in/v1/countries', { headers: geoHeaders })
                .then(response => response.json())
                .then(data => {
                    data.sort((a, b) => a.name.localeCompare(b.name));
                    let countryOptions = '<option value="" disabled>Select Country</option>';
                    data.forEach(country => {
                        const selected = (country.iso2 === initialCountryIso) ? 'selected' : '';
                        countryOptions += `<option value="${country.iso2}" ${selected}>${country.name}</option>`;
                    });
                    $('#country').html(countryOptions).material_select();
                    if (initialCountryIso) { fetchStates(initialCountryIso); }
                });

            // Event listeners for dropdown changes
            $('#country').change(function() {
                fetchStates($(this).val());
                $('#city').html('<option value="">Select City</option>').material_select();
            });
            $('#state').change(function() {
                fetchCities($('#country').val(), $(this).val());
            });
        });

        function fetchStates(countryIso) {
            fetch(`https://api.countrystatecity.in/v1/countries/${countryIso}/states`, { headers: geoHeaders })
                .then(response => response.json())
                .then(data => {
                    data.sort((a, b) => a.name.localeCompare(b.name));
                    let stateOptions = '<option value="">Select State</option>';
                    data.forEach(state => {
                        const selected = (state.iso2 === initialStateIso) ? 'selected' : '';
                        stateOptions += `<option value="${state.iso2}" ${selected}>${state.name}</option>`;
                    });
                    $('#state').html(stateOptions).material_select();
                    if(initialStateIso) { fetchCities(countryIso, initialStateIso); }
                });
        }

        function fetchCities(countryIso, stateIso) {
            fetch(`https://api.countrystatecity.in/v1/countries/${countryIso}/states/${stateIso}/cities`, { headers: geoHeaders })
                .then(response => response.json())
                .then(data => {
                    data.sort((a, b) => a.name.localeCompare(b.name));
                    let cityOptions = '<option value="">Select City</option>';
                    data.forEach(city => {
                        const selected = (city.name === initialCityName) ? 'selected' : '';
                        cityOptions += `<option value="${city.name}" ${selected}>${city.name}</option>`;
                    });
                    $('#city').html(cityOptions).material_select();
                });
        }
    </script>
</body>
</html>