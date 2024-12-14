<?php
require_once 'StaffTypeController.php';

$controller = new StaffTypeController();
$error_message = '';
$success_message = '';
$staff_type = $take_appointment = ''; // Default empty values

// Check if stafftype_id is set in the URL for initial loading
if (isset($_GET['id'])) {
    $stafftype_id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
    $stafftype = $controller->viewStaffType($stafftype_id);

    if ($stafftype) {
        // Populate variables with stafftype data
        $staff_type = $stafftype['staff_type'];
        $take_appointment = $stafftype['take_appointment'];
    } else {
        $error_message = "Staff Type not found.";
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process the form submission to update stafftype
    $stafftype_id = filter_var($_POST['stafftype_id'], FILTER_SANITIZE_NUMBER_INT);
    $new_staff_type = filter_var($_POST['staff_type'], FILTER_SANITIZE_STRING);
    $take_appointment = filter_var($_POST['take_appointment'], FILTER_SANITIZE_STRING);

    // Attempt to update with the new values
    if ($controller->updateStaffType($stafftype_id, $new_staff_type, $take_appointment)) {
        $success_message = "Staff Type updated successfully!";
        $staff_type = $new_staff_type;
    } else {
        $error_message = "Failed to update staff type. Please try again.";
    }
}

$user_type = $_SESSION['user_type'];

if (!isset($user_type) || $user_type != 'admin') {
    // Redirect to the home page if user should not have access to this portal
    header('Location: home.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clinic | Edit Staff Type</title>
    <link rel="icon" href="images/icon.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="editStyle.css">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="text-center mb-3">
            <a href="addStaffType.php" class="btn btn-primary">Add New Staff Type</a>
            <a href='stafftypeList.php' class='btn btn-secondary'>Back to Staff Types</a>
        </div>

        <div class="card">
            <div class="card-body">
                <h2 class="card-title text-center mb-4">Update Staff Type</h2>

                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger text-center"><?php echo $error_message; ?></div>
                <?php endif; ?>
                <?php if (!empty($success_message)): ?>
                    <div class="alert alert-success text-center"><?php echo $success_message; ?></div>
                <?php endif; ?>

                <form method="POST" action="editStaffType.php">
                    <input type="hidden" name="_token" value="<?php echo $_SESSION['_token']; ?>">
                    <input type="hidden" name="stafftype_id" value="<?php echo htmlspecialchars($stafftype_id); ?>">

                    <div class="mb-3">
                        <label for="staff_type" class="form-label">Staff Type:</label>
                        <input type="text" id="staff_type" name="staff_type" class="form-control" required
                            value="<?php echo htmlspecialchars($staff_type); ?>">
                    </div>

                    <div class="mb-3">
                        <label for="take_appointment" class="form-label">Takes Appointments:</label>
                        <select class="form-control" id="take_appointment" name="take_appointment" required>
                            <option value=""></option>
                            <option value="yes" <?php echo (isset($take_appointment) && $take_appointment == 'yes') ? 'selected' : ''; ?>>Yes</option>
                            <option value="no" <?php echo (isset($take_appointment) && $take_appointment == 'no') ? 'selected' : ''; ?>>No</option>
                        </select>
                    </div>

                    <div class="text-center">
                        <input type="submit" class="btn btn-success" value="Update Staff Type">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>