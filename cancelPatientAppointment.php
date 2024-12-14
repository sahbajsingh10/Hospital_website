<?php
session_start(); // Start the session

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'PatientAppointmentController.php';

$controller = new PatientAppointmentController();
$error_message = '';
$success_message = '';

// Check if appointment_id is set in the POST request
if (isset($_POST['appointment_id'])) {
    $appointment_id = filter_var($_POST['appointment_id'], FILTER_SANITIZE_NUMBER_INT);

    // Attempt to cancel the appointment
    $result = $controller->cancelAppointment($appointment_id);
    if (strpos($result, 'Error:') === false) {
        $success_message = "Appointment canceled successfully.";
    } else {
        $error_message = $result;
    }
} else {
    $error_message = "Invalid appointment ID.";
}
$user_type = $_SESSION['user_type'];

if (!isset($user_type) || $user_type != 'patient') {
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
    <title>Clinic | Cancel Appointment</title>
    <link rel="icon" href="images/icon.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container my-5">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h1 class="card-title mb-0">Cancel Appointment</h1>
            </div>
            <div class="card-body">
                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo htmlspecialchars($error_message); ?>
                    </div>
                <?php elseif (!empty($success_message)): ?>
                    <div class="alert alert-success" role="alert">
                        <?php echo htmlspecialchars($success_message); ?>
                    </div>
                <?php endif; ?>
                <a href="patientappointmentList.php" class="btn btn-secondary">Back to Appointments</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
