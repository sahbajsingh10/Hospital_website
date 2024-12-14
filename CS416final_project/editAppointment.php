<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'AppointmentController.php';

$controller = new AppointmentController();
$error_message = '';
$success_message = '';
$patient_id = $staff_id = $appointment_type = $date_time = $appointment_status = ''; // Default empty values

// Database connection
$conn = new mysqli('localhost', 'root', '', 'hospitaldb');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch appointment types
$sql = "SELECT appointment_name FROM appointmenttypes";
$result = $conn->query($sql);

$appointment_types = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $appointment_types[] = $row['appointment_name'];
    }
}
$conn->close();

// Check if appointment_id is set in the URL for initial loading
if (isset($_GET['id'])) {
    $appointment_id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
    $appointment = $controller->viewAppointment($appointment_id);

    if ($appointment) {
        // Populate variables with appointment data
        $patient_id = $appointment['patient_id'];
        $staff_id = $appointment['staff_id'];
        $appointment_type = $appointment['appointment_type'];
        $date_time = (new DateTime($appointment['date_time']))->format('Y-m-d h:i A'); // Format date with AM/PM
        $appointment_status = $appointment['appointment_status'];
    } else {
        $error_message = "Appointment not found.";
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appointment_id = filter_var($_POST['appointment_id'], FILTER_SANITIZE_NUMBER_INT);
    $patient_id = filter_var($_POST['patient_id'], FILTER_SANITIZE_NUMBER_INT);
    $staff_id = filter_var($_POST['staff_id'], FILTER_SANITIZE_NUMBER_INT);
    $appointment_type = filter_var($_POST['appointment_type'], FILTER_SANITIZE_SPECIAL_CHARS);
    $date_time = filter_var($_POST['date_time'], FILTER_SANITIZE_SPECIAL_CHARS);
    $appointment_status = filter_var($_POST['appointment_status'], FILTER_SANITIZE_SPECIAL_CHARS);

    if (empty($date_time)) {
        $error_message = "Date and time must be selected.";
    } else {
        // Database connection
        $conn = new mysqli('localhost', 'root', '', 'hospitaldb');

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Convert the date_time to a DateTime object
        $new_start_time = new DateTime($date_time);
        $buffer_end_time = (clone $new_start_time)->add(new DateInterval('PT29M'));

        // Verify if the patient ID exists
        $patient_check_sql = "SELECT user_id FROM patientprofiles WHERE user_id = ?";
        $stmt = $conn->prepare($patient_check_sql);
        $stmt->bind_param('i', $patient_id);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows == 0) {
            $error_message = "Error: Patient ID does not exist.";
        } else {
            // Verify if the staff ID exists
            $staff_check_sql = "SELECT user_id FROM staffprofiles WHERE user_id = ?";
            $stmt->prepare($staff_check_sql);
            $stmt->bind_param('i', $staff_id);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows == 0) {
                $error_message = "Error: Staff ID does not exist.";
            } else {
                // Check if the staff member can take appointments
                if (!$controller->canTakeAppointment($staff_id)) {
                    $error_message = "Error: The selected staff member does not take appointments.";
                } else {
                    // Fetch current appointment details to compare
                    $current_appt_sql = "SELECT date_time FROM appointments WHERE id = ?";
                    $stmt = $conn->prepare($current_appt_sql);
                    $stmt->bind_param('i', $appointment_id);
                    $stmt->execute();
                    $current_appt = $stmt->get_result()->fetch_assoc();
                    $stmt->close();

                    // Check for overlapping appointments, excluding canceled appointments and the current appointment being edited
                    $overlap_check_sql = "SELECT * FROM appointments WHERE (staff_id = ? OR patient_id = ?) AND 
                            appointment_status != 'canceled' AND 
                            (date_time BETWEEN ? AND ? OR DATE_ADD(date_time, INTERVAL 29 MINUTE) BETWEEN ? AND ?) 
                            AND id != ? AND ? != ?";
                    $stmt = $conn->prepare($overlap_check_sql);
                    $start_time_str = $new_start_time->format('Y-m-d H:i:s');
                    $buffer_end_time_str = $buffer_end_time->format('Y-m-d H:i:s');
                    $current_time_str = (new DateTime($current_appt['date_time']))->format('Y-m-d H:i:s');
                    $stmt->bind_param('iissssiss', $staff_id, $patient_id, $start_time_str, $buffer_end_time_str, $start_time_str, $buffer_end_time_str, $appointment_id, $current_time_str, $start_time_str);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        $error_message = "Error: Overlapping appointment detected.";
                    } else {
                        // Attempt to update the appointment
                        $result = $controller->updateAppointment($appointment_id, $patient_id, $staff_id, $appointment_type, $date_time, $appointment_status);
                        if (strpos($result, 'Error:') === false) {
                            $success_message = "Appointment updated successfully.";
                            header("Location: appointmentList.php");
                            exit();
                        } else {
                            $error_message = $result;
                        }
                    }
                }
            }
        }
        $stmt->close();
        $conn->close();
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
    <title>Clinic | Edit Appointment</title>
    <link rel="icon" href="images/icon.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="editStyle.css">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="text-center mb-3">
            <a href="addAppointment.php" class="btn btn-primary">Add New Appointment</a>
            <a href='appointmentList.php' class='btn btn-secondary'>Back to Appointments</a>
        </div>

        <div class="card">
            <div class="card-body">
                <h2 class="card-title text-center mb-4">Update Appointment</h2>

                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo htmlspecialchars($error_message); ?>
                    </div>
                <?php elseif (!empty($success_message)): ?>
                    <div class="alert alert-success" role="alert">
                        <?php echo htmlspecialchars($success_message); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="editAppointment.php">

                    <input type="hidden" name="_token" value="<?php echo $_SESSION['_token']; ?>">

                    <input type="hidden" name="appointment_id" value="<?php echo htmlspecialchars($appointment_id); ?>">

                    <div class="mb-3">
                        <label for="patient_id" class="form-label">Patient ID:</label>
                        <input type="text" id="patient_id" name="patient_id" class="form-control" required
                            value="<?php echo htmlspecialchars($patient_id); ?>">
                    </div>

                    <div class="mb-3">
                        <label for="staff_id" class="form-label">Staff ID:</label>
                        <input type="text" id="staff_id" name="staff_id" class="form-control" required
                            value="<?php echo htmlspecialchars($staff_id); ?>">
                    </div>

                    <div class="mb-3">
                        <label for="appointment_type" class="form-label">Appointment Type:</label>
                        <select class="form-control" id="appointment_type" name="appointment_type" required>
                            <option value=""></option>
                            <?php foreach ($appointment_types as $type): ?>
                                <option value="<?php echo htmlspecialchars($type); ?>" <?php echo (isset($appointment_type) && $appointment_type == $type) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($type); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="date_time" class="form-label">Date:</label>
                        <input type="text" id="date_time" name="date_time" class="form-control" required
                            value="<?php echo htmlspecialchars($date_time); ?>">
                    </div>

                    <div class="mb-3">
                        <label for="appointment_status" class="form-label">Appointment Status:</label>
                        <select class="form-control" id="appointment_status" name="appointment_status" required>
                            <option value=""></option>
                            <option value="scheduled" <?php echo (isset($appointment_status) && $appointment_status == 'scheduled') ? 'selected' : ''; ?>>Scheduled</option>
                            <option value="completed" <?php echo (isset($appointment_status) && $appointment_status == 'completed') ? 'selected' : ''; ?>>Completed</option>
                            <option value="canceled" <?php echo (isset($appointment_status) && $appointment_status == 'canceled') ? 'selected' : ''; ?>>Canceled</option>
                        </select>
                    </div>

                    <div class="text-center">
                        <input type="submit" class="btn btn-success" value="Update Appointment">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            flatpickr("#date_time", {
                enableTime: true,
                dateFormat: "Y-m-d h:i K", // Use K for AM/PM
                minDate: new Date(new Date().setDate(new Date().getDate() + 1)).setHours(8, 0, 0, 0), // Ensure future dates are selected (day after current date)
                time_24hr: false, // Set to false for 12-hour clock
                minTime: "08:00", // Define time range (optional)
                maxTime: "18:00"
            });
        });
    </script>
</body>

</html>