<?php
require_once 'AppointmentController.php';

$error_message = '';
$success_message = '';
$patient_id = $staff_id = $appointment_type = $date_time = $appointment_status = '';
$appointment_id = null;

$controller = new AppointmentController();

if (isset($_GET['id'])) {
    $appointment_id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
    $appointment = $controller->viewAppointment($appointment_id);

    if ($appointment) {
        $patient_id = $appointment['patient_id'];
        $staff_id = $appointment['staff_id'];
        $appointment_type = $appointment['appointment_type'];
        $date_time = $appointment['date_time'];
        $appointment_status = $appointment['appointment_status'];

        // Format date_time to 12-hour AM/PM format
        $dateTime = new DateTime($date_time);
        $formattedDateTime = $dateTime->format('Y-m-d h:i A');
    } else {
        $error_message = "Appointment not found.";
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appointment_id = filter_var($_POST['appointment_id'], FILTER_SANITIZE_NUMBER_INT);

    if ($controller->deleteAppointment($appointment_id)) {
        $success_message = "Appointment deleted successfully!";
        header("Location: appointmentList.php");
        exit();
    } else {
        $error_message = "Failed to delete appointment.";
    }
}
$user_type = $_SESSION['user_type'];

if (!isset($user_type) || $user_type != 'staff') {
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
    <title>Clinic | Delete Appointment</title>
    <link rel="icon" href="images/icon.png" type="image/png">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="deleteStyle.css">
</head>

<body class="bg-light">
    <div class="container my-5">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h1 class="card-title mb-0">Delete This Appointment</h1>
            </div>
            <div class="card-body">
                <?php if ($error_message): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo htmlspecialchars($error_message); ?>
                    </div>
                <?php elseif ($success_message): ?>
                    <div class="alert alert-success" role="alert">
                        <?php echo htmlspecialchars($success_message); ?>
                    </div>
                <?php endif; ?>

                <?php if ($appointment): ?>
                    <p><strong>ID:</strong> <?php echo htmlspecialchars($appointment['id']); ?></p>
                    <p><strong>Patient ID:</strong> <?php echo htmlspecialchars($appointment['patient_id']); ?></p>
                    <p><strong>Staff ID:</strong> <?php echo htmlspecialchars($appointment['staff_id']); ?></p>
                    <p><strong>Appointment Type:</strong> <?php echo htmlspecialchars($appointment['appointment_type']); ?></p>
                    <p><strong>Date:</strong> <?php echo htmlspecialchars($formattedDateTime); ?></p> <!-- Display formatted date_time -->
                    <p><strong>Appointment Status:</strong> <?php echo htmlspecialchars($appointment['appointment_status']); ?></p>
                <?php endif; ?>
            </div>
            <div class="card-footer text-end">
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                    <i class="fas fa-trash-alt"></i> Delete
                </button>
                <a href="appointmentList.php" class="btn btn-secondary">Back to Appointments</a>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this appointment?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form method="POST" action="deleteAppointment.php" style="display: inline;">

                        <input type="hidden" name="_token" value="<?php echo $_SESSION['_token']; ?>">

                        <input type="hidden" name="appointment_id" value="<?php echo htmlspecialchars($appointment_id); ?>">
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>

</html>