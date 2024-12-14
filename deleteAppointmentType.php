<?php
require_once 'AppointmentTypeController.php';

$error_message = '';
$success_message = '';
$appointment_name = '';
$appointmenttype_id = null;

$controller = new AppointmentTypeController();

if (isset($_GET['id'])) {
    $appointmenttype_id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
    $appointmenttype = $controller->viewAppointmentType($appointmenttype_id);

    if ($appointmenttype) {
        $appointment_name = $appointmenttype['appointment_name'];
    } else {
        $error_message = "Appointment Type not found.";
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appointmenttype_id = filter_var($_POST['appointmenttype_id'], FILTER_SANITIZE_NUMBER_INT);
    
    // Get the appointment type details
    $appointmenttype = $controller->viewAppointmentType($appointmenttype_id);
    
    if ($appointmenttype) {
        // Check if the appointment type is being referenced
        if ($controller->isAppointmentTypeReferenced($appointmenttype['appointment_name'])) {
            $error_message = "Cannot delete this appointment type as it is currently being used by existing appointments.";
            // Reload the appointment type details for display
            $appointment_name = $appointmenttype['appointment_name'];
        } else {
            // Safe to delete - not referenced
            if ($controller->deleteAppointmentType($appointmenttype_id)) {
                $success_message = "Appointment Type deleted successfully!";
                header("Location: appointmenttypeList.php");
                exit();
            } else {
                $error_message = "Failed to delete appointment type.";
            }
        }
    } else {
        $error_message = "Appointment Type not found.";
    }
}

$user_type = $_SESSION['user_type'];

if (!isset($user_type) || $user_type != 'admin') {
    header('Location: home.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clinic | Delete Appointment Type</title>
    <link rel="icon" href="images/icon.png" type="image/png">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="deleteStyle.css">
</head>

<body class="bg-light">
    <div class="container my-5">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h1 class="card-title mb-0">Delete This Appointment Type</h1>
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

                <?php if ($appointmenttype): ?>
                    <p><strong>ID:</strong> <?php echo htmlspecialchars($appointmenttype['id']); ?></p>
                    <p><strong>Appointment Name:</strong> <?php echo htmlspecialchars($appointmenttype['appointment_name']); ?></p>
                <?php endif; ?>
            </div>
            <div class="card-footer text-end">
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                    <i class="fas fa-trash-alt"></i> Delete
                </button>
                <a href="appointmenttypeList.php" class="btn btn-secondary">Back to Appointment Types</a>
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
                    Are you sure you want to delete this appointment type?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form method="POST" action="deleteAppointmentType.php" style="display: inline;">

                        <input type="hidden" name="_token" value="<?php echo $_SESSION['_token']; ?>">

                        <input type="hidden" name="appointmenttype_id" value="<?php echo htmlspecialchars($appointmenttype_id); ?>">
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