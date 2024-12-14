<?php
require_once 'AppointmentTypeController.php';

$controller = new AppointmentTypeController();
$error_message = '';
$success_message = '';
$appointment_name = ''; // Default empty values

// Check if appointmenttype_id is set in the URL for initial loading
if (isset($_GET['id'])) {
    $appointmenttype_id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
    $appointmenttype = $controller->viewAppointmentType($appointmenttype_id);

    if ($appointmenttype) {
        // Populate variables with appointmenttype data
        $appointment_name = $appointmenttype['appointment_name'];
    } else {
        $error_message = "Appointment Type not found.";
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process the form submission to update appointmenttype
    $appointmenttype_id = filter_var($_POST['appointmenttype_id'], FILTER_SANITIZE_NUMBER_INT);
    $appointment_name = filter_var($_POST['appointment_name'], FILTER_SANITIZE_STRING);

    if ($controller->updateAppointmentType($appointmenttype_id, $appointment_name)) {
        $success_message = "Appointment Type updated successfully!";
    } else {
        $error_message = "Failed to update appointment type.";
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
    <title>Clinic | Edit Appointment Type</title>
    <link rel="icon" href="images/icon.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="editStyle.css">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="text-center mb-3">
            <a href="addAppointmentType.php" class="btn btn-primary">Add New Appointment Type</a>
            <a href='appointmenttypeList.php' class='btn btn-secondary'>Back to Appointment Types</a>
        </div>

        <div class="card">
            <div class="card-body">
                <h2 class="card-title text-center mb-4">Update Appointment Type</h2>

                <?php if (!empty($error_message)): ?>
                    <p class="text-danger text-center"><?php echo $error_message; ?></p>
                <?php elseif (!empty($success_message)): ?>
                    <p class="text-success text-center"><?php echo $success_message; ?></p>
                <?php else: ?>
                    <form method="POST" action="editAppointmentType.php">
                        <input type="hidden" name="_token" value="<?php echo $_SESSION['_token']; ?>">
                        <input type="hidden" name="appointmenttype_id" value="<?php echo htmlspecialchars($appointmenttype_id); ?>">

                        <div class="mb-3">
                            <label for="appointment_name" class="form-label">Appointment Name:</label>
                            <input type="text" id="appointment_name" name="appointment_name" class="form-control" required
                                value="<?php echo htmlspecialchars($appointment_name); ?>">
                        </div>

                        <div class="text-center">
                            <input type="submit" class="btn btn-success" value="Update Appointment Type">
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>