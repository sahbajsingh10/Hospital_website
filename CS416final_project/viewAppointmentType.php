<?php
require_once 'AppointmentTypeController.php';

$error_message = '';
$success_message = '';
$appointment_name = ''; // Default empty values

// Check if appointmenttype_id is set in the URL for initial loading
if (isset($_GET['id'])) {
    $appointmenttype_id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
    $controller = new AppointmentTypeController();
    $appointmenttype = $controller->viewAppointmentType($appointmenttype_id);

    if ($appointmenttype) {
        // Populate variables with appointmenttype data
        $appointment_name = $appointmenttype['appointment_name'];
    } else {
        $error_message = "Appointment Type not found.";
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
    <title>Clinic | Appointment Type Details</title> <link rel="icon" href="images/icon.png" type="image/png">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="viewStyle.css">
</head>

<body class="bg-light">

    <div class="container my-5">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h1 class="card-title mb-0">Appointment Type Details</h1>
            </div>
            <div class="card-body">
                <?php if ($error_message): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>

                <p><strong>ID:</strong> <?php echo htmlspecialchars($appointmenttype['id']); ?></p>
                <p><strong>Appointment Name:</strong> <?php echo htmlspecialchars($appointmenttype['appointment_name']); ?></p>
            </div>
            <div class="card-footer text-end">
                <a href="editAppointmentType.php?id=<?php echo $appointmenttype_id; ?>" class="btn btn-warning">Edit</a>
                <a href="appointmenttypeList.php" class="btn btn-secondary">Back to Appointment Types</a>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>

</html>