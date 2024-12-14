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
$staff_id = $appointment_type = $date_time = $appointment_status = ''; // Default empty values

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

$doctors = [];
if (!isset($_GET['time'])) {
    $sql = "SELECT sp.user_id, sp.first_name, sp.last_name, st.staff_type 
            FROM staffprofiles sp
            JOIN stafftypes st ON sp.staff_profile_type = st.staff_type
            WHERE st.take_appointment = 'yes'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $doctors[] = $row;
        }
    }
}

// Check if appointment_id is set in the URL for initial loading
if (isset($_GET['id'])) {
    $appointment_id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
    $appointment = $controller->viewAppointment($appointment_id);

    if ($appointment) {
        // Populate variables with appointment data
        $staff_id = $appointment['staff_id'];
        $appointment_type = $appointment['appointment_type'];
        $date_time = (new DateTime($appointment['date_time']))->format('Y-m-d h:i A'); // Format date with AM/PM
        $appointment_status = $appointment['appointment_status'];
    } else {
        $error_message = "Appointment not found.";
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process the form submission to update appointment
    $appointment_id = filter_var($_POST['appointment_id'], FILTER_SANITIZE_NUMBER_INT);
    $patient_id = $_SESSION['user_id']; // Use the logged-in user's ID
    $staff_id = filter_var($_POST['staff_id'], FILTER_SANITIZE_NUMBER_INT);
    $appointment_type = filter_var($_POST['appointment_type'], FILTER_SANITIZE_SPECIAL_CHARS);
    $date_time = filter_var($_POST['date_time'], FILTER_SANITIZE_SPECIAL_CHARS);
    $appointment_status = 'scheduled'; // Automatically set status to "Scheduled" for consistency
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
        $buffer_start_time = (clone $new_start_time)->sub(new DateInterval('PT29M'));
        $buffer_end_time = (clone $new_start_time)->add(new DateInterval('PT29M'));

        // Verify if the staff ID exists
        $staff_check_sql = "SELECT user_id FROM staffprofiles WHERE user_id = ?";
        $stmt = $conn->prepare($staff_check_sql);
        $stmt->bind_param('i', $staff_id);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows == 0) {
            $error_message = "Error: Staff ID does not exist.";
        } else {
            // Check for overlapping appointments, excluding canceled appointments and the current appointment being edited
            $overlap_check_sql = "SELECT * FROM appointments WHERE (staff_id = ? OR patient_id = ?) 
                    AND appointment_status != 'canceled' 
                    AND (date_time BETWEEN ? AND ? OR DATE_ADD(date_time, INTERVAL 29 MINUTE) BETWEEN ? AND ?) 
                    AND id != ?";
            $stmt = $conn->prepare($overlap_check_sql);
            $start_time_str = $new_start_time->format('Y-m-d H:i:s');
            $buffer_end_time_str = $buffer_end_time->format('Y-m-d H:i:s');
            $stmt->bind_param('iissssi', $staff_id, $patient_id, $start_time_str, $buffer_end_time_str, $start_time_str, $buffer_end_time_str, $appointment_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $error_message = "Error: Overlapping appointment detected.";
            } else {
                // Attempt to update the appointment
                $result = $controller->updateAppointment($appointment_id, $patient_id, $staff_id, $appointment_type, $date_time, $appointment_status);
                if (strpos($result, 'Error:') === false) {
                    $success_message = "Appointment updated successfully.";
                    header("Location: patientappointmentList.php");
                    exit();
                } else {
                    $error_message = $result;
                }
            }
        }
        $stmt->close();
        $conn->close();
    }
} elseif (isset($_GET['time']) && isset($_GET['id'])) {
    header('Content-Type: application/json');

    $new_start_time = new DateTime($_GET['time']);
    $buffer_start_time = (clone $new_start_time)->sub(new DateInterval('PT29M'));
    $buffer_end_time = (clone $new_start_time)->add(new DateInterval('PT29M'));
    $appointment_id = $_GET['id'];
    $patient_id = $_SESSION['user_id'];

    // Get the current appointment's details
    $current_appt_sql = "SELECT staff_id, date_time FROM appointments WHERE id = ?";
    $stmt = $conn->prepare($current_appt_sql);
    $stmt->bind_param('i', $appointment_id);
    $stmt->execute();
    $current_appt = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $selected_time_str = $new_start_time->format('Y-m-d H:i:s');
    $current_time_str = (new DateTime($current_appt['date_time']))->format('Y-m-d H:i:s');

    $sql = "SELECT DISTINCT sp.user_id, sp.first_name, sp.last_name, st.staff_type 
            FROM staffprofiles sp
            JOIN stafftypes st ON sp.staff_profile_type = st.staff_type
            WHERE st.take_appointment = 'yes' 
            AND sp.user_id NOT IN (
                SELECT DISTINCT a.staff_id 
                FROM appointments a 
                WHERE a.id != ? 
                AND a.appointment_status != 'canceled'  -- Exclude canceled appointments
                AND (
                    -- Check if the new appointment time falls within buffer of existing appointments
                    (? BETWEEN DATE_SUB(a.date_time, INTERVAL 29 MINUTE) AND DATE_ADD(a.date_time, INTERVAL 29 MINUTE))
                    -- Check if existing appointments fall within buffer of new appointment time
                    OR (a.date_time BETWEEN ? AND ?)
                )
                -- Only exclude if this is actually a different time
                AND ? != ?
            )";

    $stmt = $conn->prepare($sql);
    $start_time_str = $buffer_start_time->format('Y-m-d H:i:s');
    $end_time_str = $buffer_end_time->format('Y-m-d H:i:s');

    $stmt->bind_param(
        'isssss',
        $appointment_id,
        $selected_time_str,  // New appointment time
        $start_time_str,     // Buffer start
        $end_time_str,       // Buffer end
        $selected_time_str,  // Selected time
        $current_time_str    // Current appointment time
    );

    $stmt->execute();
    $result = $stmt->get_result();
    $doctors = [];
    while ($row = $result->fetch_assoc()) {
        $doctors[] = $row;
    }

    echo json_encode($doctors);
    $stmt->close();
    exit();
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
    <title>Clinic | Edit Appointment</title>
    <link rel="icon" href="images/icon.png" type="image/png">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="editStyle.css">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="text-center mb-3">
            <a href="addPatientAppointment.php" class="btn btn-primary">Add New Appointment</a>
            <a href='patientappointmentList.php' class='btn btn-secondary'>Back to Appointments</a>
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

                <form method="POST" action="editPatientAppointment.php">

                    <input type="hidden" name="_token" value="<?php echo $_SESSION['_token']; ?>">

                    <input type="hidden" name="appointment_id" value="<?php echo htmlspecialchars($appointment_id); ?>">

                    <div class="mb-3">
                        <label for="staff_id" class="form-label">Practitioner:</label>
                        <select class="form-control" id="staff_id" name="staff_id" required>
                            <option value=""></option>
                            <?php foreach ($doctors as $doctor): ?>
                                <option value="<?php echo htmlspecialchars($doctor['user_id']); ?>" <?php echo ($staff_id == $doctor['user_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($doctor['first_name'] . ' ' . $doctor['last_name'] . ' (' . $doctor['staff_type'] . ')'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
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
                        <input type="text" class="form-control" id="date_time" name="date_time" required value="<?php echo htmlspecialchars($date_time); ?>">
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
            const dateTimePicker = flatpickr("#date_time", {
                enableTime: true,
                dateFormat: "Y-m-d h:i K",
                minDate: new Date(new Date().setDate(new Date().getDate() + 1)).setHours(8, 0, 0, 0),
                time_24hr: false,
                minTime: "08:00",
                maxTime: "18:00",
                onChange: function(selectedDates, dateStr) {
                    // Call the fetch function whenever the date/time changes
                    fetchAvailablePractitioners(dateStr);
                }
            });

            // Initial fetch of practitioners if date is already set
            const initialDateTime = document.getElementById('date_time').value;
            if (initialDateTime) {
                fetchAvailablePractitioners(initialDateTime);
            }
        });

        function fetchAvailablePractitioners(selectedTime) {
            const appointmentId = document.querySelector('input[name="appointment_id"]').value;
            fetch(`editPatientAppointment.php?time=${encodeURIComponent(selectedTime)}&id=${appointmentId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    const practitionerSelect = document.getElementById('staff_id');
                    const currentValue = practitionerSelect.value; // Store current selection
                    practitionerSelect.innerHTML = '<option value=""></option>';
                    data.forEach(practitioner => {
                        const option = document.createElement('option');
                        option.value = practitioner.user_id;
                        option.text = `${practitioner.first_name} ${practitioner.last_name} (${practitioner.staff_type})`;
                        if (practitioner.user_id == currentValue) {
                            option.selected = true;
                        }
                        practitionerSelect.add(option);
                    });
                })
                .catch(error => {
                    console.error('Error fetching practitioners:', error);
                });
        }
    </script>
</body>

</html>