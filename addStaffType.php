<?php
require_once 'StaffTypeController.php';

$controller = new StaffTypeController();
$error_message = '';

// Verify user authorization first
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: home.php');
    exit();
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input data
    $stafftype_id = filter_var($_POST['stafftype_id'], FILTER_SANITIZE_NUMBER_INT);
    $staff_type = trim(filter_var($_POST['staff_type'], FILTER_SANITIZE_STRING));
    $take_appointment = trim(filter_var($_POST['take_appointment'], FILTER_SANITIZE_STRING));

    // Check if staff type already exists
    $existing_stafftype = $controller->getStaffTypeByName($staff_type);
    
    if ($existing_stafftype) {
        $error_message = "A staff type with this name already exists.";
    } else {
        // Attempt to add the stafftype
        if ($controller->addStaffType($staff_type, $take_appointment)) {
            $_SESSION['success_message'] = "Staff Type added successfully.";
            header("Location: stafftypeList.php");
            exit();
        } else {
            $error_message = "Failed to add staff type. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clinic | Add Staff Type</title>
    <link rel="icon" href="images/icon.png" type="image/png">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="addStyle.css">
</head>

<body class="bg-light">
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h2 class="mb-0">Add Staff Type</h2>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($error_message)): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo htmlspecialchars($error_message); ?>
                            </div>
                        <?php endif; ?>

                        <form method="post" action="addStaffType.php">
                            <input type="hidden" name="_token" value="<?php echo htmlspecialchars($_SESSION['_token']); ?>">
                            <input type="hidden" name="stafftype_id" 
                                   value="<?php echo isset($_GET['stafftype_id']) ? htmlspecialchars($_GET['stafftype_id']) : ''; ?>">

                            <div class="mb-3">
                                <label for="staff_type" class="form-label">Staff Type</label>
                                <input type="text" class="form-control" id="staff_type" name="staff_type" required
                                       value="<?php echo isset($staff_type) ? htmlspecialchars($staff_type) : ''; ?>">
                            </div>

                            <div class="mb-3">
                                <label for="take_appointment" class="form-label">Takes Appointments:</label>
                                <select class="form-control" id="take_appointment" name="take_appointment" required>
                                    <option value=""></option>
                                    <option value="yes" <?php echo (isset($take_appointment) && $take_appointment == 'yes') ? 'selected' : ''; ?>>Yes</option>
                                    <option value="no" <?php echo (isset($take_appointment) && $take_appointment == 'no') ? 'selected' : ''; ?>>No</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Add Staff Type</button>
                        </form>
                    </div>
                </div>
                <div class="text-center mt-3">
                    <a href="stafftypeList.php" class="btn btn-secondary">Back to Staff Types</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>

</html>