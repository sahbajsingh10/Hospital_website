<?php
require_once 'AppointmentTypeController.php';

$controller = new AppointmentTypeController();
$error_message = '';

// Verify user authorization first
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: home.php');
    exit();
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input data
    $appointmenttype_id = filter_var($_POST['appointmenttype_id'], FILTER_SANITIZE_NUMBER_INT);
    $appointment_name = trim(filter_var($_POST['appointment_name'], FILTER_SANITIZE_STRING));

    // Check if appointment type already exists
    $existing_type = $controller->getAppointmentTypeByName($appointment_name);
    
    if ($existing_type) {
        $error_message = "An appointment type with this name already exists.";
    } else {
        // Attempt to add the appointment type
        if ($controller->addAppointmentType($appointment_name)) {
            $_SESSION['success_message'] = "Appointment Type added successfully.";
            header("Location: appointmenttypeList.php");
            exit();
        } else {
            $error_message = "Failed to add appointment type. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clinic | Add Appointment Type</title>
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
                        <h2 class="mb-0">Add Appointment Type</h2>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($error_message)): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo htmlspecialchars($error_message); ?>
                            </div>
                        <?php endif; ?>

                        <form method="post" action="addAppointmentType.php">
                            <input type="hidden" name="_token" value="<?php echo htmlspecialchars($_SESSION['_token']); ?>">
                            <input type="hidden" name="appointmenttype_id"
                                   value="<?php echo isset($_GET['appointmenttype_id']) ? htmlspecialchars($_GET['appointmenttype_id']) : ''; ?>">

                            <div class="mb-3">
                                <label for="appointment_name" class="form-label">Appointment Type</label>
                                <input type="text" class="form-control" id="appointment_name" name="appointment_name" required
                                       value="<?php echo isset($appointment_name) ? htmlspecialchars($appointment_name) : ''; ?>">
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Add Appointment Type</button>
                        </form>
                    </div>
                </div>

                <div class="text-center mt-3">
                    <a href="appointmenttypeList.php" class="btn btn-secondary">Back to Appointment Types</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>

</html>