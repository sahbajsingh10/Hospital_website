<?php
require_once 'PatientAppointmentController.php';
session_start();
$error_message = '';
$success_message = '';
$patient_id = $staff_id = $appointment_type = $date_time = $appointment_status = ''; // Default empty values

// Check if appointment_id is set in the URL for initial loading
if (isset($_GET['id'])) {
    $appointment_id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
    $controller = new PatientAppointmentController();
    $appointment = $controller->viewAppointment($appointment_id);

    if ($appointment) {
        // Populate variables with appointment data
        $patient_id = $appointment['patient_id'];
        $staff_id = $appointment['staff_id'];
        $appointment_type = $appointment['appointment_type'];
        $date_time = $appointment['date_time'];
        $appointment_status = $appointment['appointment_status'];

        // Format date_time to 12-hour AM/PM format
        $dateTime = new DateTime($date_time);
        $formattedDateTime = $dateTime->format('Y-m-d h:i A');

        // Fetch doctor's name from staffprofiles
        $conn = new mysqli('localhost', 'root', '', 'hospitaldb');
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $doctor_sql = "SELECT first_name, last_name FROM staffprofiles WHERE user_id = ?";
        $stmt = $conn->prepare($doctor_sql);
        $stmt->bind_param('s', $staff_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $doctor = $result->fetch_assoc();
            $doctor_name = $doctor['first_name'] . ' ' . $doctor['last_name'];
        } else {
            $error_message = "Doctor not found.";
        }
        $stmt->close();
        $conn->close();
    } else {
        $error_message = "Appointment not found.";
    }
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
    <title>Clinic | Appointment Details</title>
    <link rel="icon" href="images/icon.png" type="image/png">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="viewStyle.css">
</head>

<body class="bg-light">

    <div class="container my-5">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h1 class="card-title mb-0">Appointment Details</h1>
            </div>
            <div class="card-body">
                <?php if ($error_message): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo htmlspecialchars($error_message); ?>
                    </div>
                <?php else: ?>
                    <p><strong>Practitioner:</strong> <?php echo htmlspecialchars($doctor_name); ?></p>
                    <p><strong>Appointment Type:</strong> <?php echo htmlspecialchars($appointment['appointment_type']); ?></p>
                    <p><strong>Date:</strong> <?php echo htmlspecialchars($formattedDateTime); ?></p> <!-- Display formatted date_time -->
                    <p><strong>Appointment Status:</strong> <?php echo htmlspecialchars($appointment['appointment_status']); ?></p>
                <?php endif; ?>
            </div>
            <div class="card-footer text-end">
                <a href="patientappointmentList.php" class="btn btn-secondary">Back to Appointments</a>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>

</html>
