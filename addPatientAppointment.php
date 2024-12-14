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

// Default form values
$staff_id = $appointment_type = $date_time = '';

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

// Fetch all available doctors
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input data
    $patient_id = $_SESSION['user_id']; // Use the logged-in user's ID
    $staff_id = filter_var($_POST['staff_id'], FILTER_SANITIZE_NUMBER_INT);
    $appointment_type = filter_var($_POST['appointment_type'], FILTER_SANITIZE_SPECIAL_CHARS);
    $date_time = filter_var($_POST['date_time'], FILTER_SANITIZE_SPECIAL_CHARS);
    $appointment_status = 'scheduled'; // Automatically set status to "Scheduled"

    if (empty($date_time)) {
        $error_message = "Date and time must be selected.";
    } else {
        // Verify if the patient has an existing appointment within 30 minutes of the selected time
        $new_start_time = new DateTime($date_time);
        $buffer_end_time = (clone $new_start_time)->add(new DateInterval('PT29M'));

        $patient_check_sql = "SELECT * FROM appointments WHERE patient_id = ? AND appointment_status != 'canceled' AND 
            (date_time BETWEEN ? AND ? OR DATE_ADD(date_time, INTERVAL 29 MINUTE) BETWEEN ? AND ?)";
        $stmt = $conn->prepare($patient_check_sql);
        $start_time_str = $new_start_time->format('Y-m-d H:i:s');
        $buffer_end_time_str = $buffer_end_time->format('Y-m-d H:i:s');
        $stmt->bind_param('issss', $patient_id, $start_time_str, $buffer_end_time_str, $start_time_str, $buffer_end_time_str);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error_message = "Error: You have an appointment within 30 minutes of the selected time.";
        } else {
            // Verify if the staff ID exists
            $staff_check_sql = "SELECT user_id FROM staffprofiles WHERE user_id = ?";
            $stmt = $conn->prepare($staff_check_sql);
            $stmt->bind_param('i', $staff_id);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows == 0) {
                $error_message = "Error: Staff ID does not exist.";
            } else {
                // Attempt to add the appointment using logged-in user's ID for patient_id
                $result = $controller->addAppointment($patient_id, $staff_id, $appointment_type, $date_time, $appointment_status);
                if (strpos($result, 'Error:') === false) {
                    echo "<div class='alert alert-success'>Appointment added successfully.</div>";
                    header("Location: patientappointmentList.php");
                    exit();
                } else {
                    $error_message = $result;
                }
            }
        }
        $stmt->close();
    }
} elseif (isset($_GET['time'])) {
    $new_start_time = new DateTime($_GET['time']);
    $buffer_end_time = (clone $new_start_time)->add(new DateInterval('PT29M'));

    $sql = "SELECT sp.user_id, sp.first_name, sp.last_name, st.staff_type 
            FROM staffprofiles sp
            JOIN stafftypes st ON sp.staff_profile_type = st.staff_type
            WHERE st.take_appointment = 'yes' AND NOT EXISTS (
                SELECT 1 FROM appointments a WHERE a.staff_id = sp.user_id AND appointment_status != 'canceled' AND
                (a.date_time BETWEEN ? AND ? OR DATE_ADD(a.date_time, INTERVAL 29 MINUTE) BETWEEN ? AND ?)
            )";
    $stmt = $conn->prepare($sql);
    $start_time_str = $new_start_time->format('Y-m-d H:i:s');
    $buffer_end_time_str = $buffer_end_time->format('Y-m-d H:i:s');
    $stmt->bind_param('ssss', $start_time_str, $buffer_end_time_str, $start_time_str, $buffer_end_time_str);
    $stmt->execute();
    $result = $stmt->get_result();
    $doctors = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $doctors[] = $row;
        }
    }
    echo json_encode($doctors);
    $stmt->close();
    $conn->close();
    exit();
}
$user_type = $_SESSION['user_type'];

if (!isset($user_type) || $user_type != 'patient') {
    // Redirect to the home page if user should not have access to this portal
    header('Location: home.php');
    exit();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clinic | Add Appointment</title>
    <link rel="icon" href="images/icon.png" type="image/png">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="addStyle.css">
</head>

<body class="bg-light">
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h2 class="mb-0">Add Appointment</h2>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($error_message)): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo htmlspecialchars($error_message); ?>
                            </div>
                        <?php endif; ?>

                        <form method="post" action="addPatientAppointment.php">

                            <input type="hidden" name="_token" value="<?php echo $_SESSION['_token']; ?>">

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
                                        <option value="<?php echo htmlspecialchars($type); ?>" <?php echo ($appointment_type == $type) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($type); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="date_time" class="form-label">Date:</label>
                                <input type="text" class="form-control" id="date_time" name="date_time" required value="<?php echo htmlspecialchars($date_time); ?>">
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Add Appointment</button>
                        </form>
                    </div>
                </div>
                <div class="text-center mt-3">
                    <a href="patientappointmentList.php" class="btn btn-secondary">Back to Appointments</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            flatpickr("#date_time", {
                enableTime: true,
                dateFormat: "Y-m-d h:i K",
                minDate: new Date(new Date().setDate(new Date().getDate() + 1)).setHours(8, 0, 0, 0), // Ensure future dates are selected (day after current date)
                time_24hr: false, // Set to false for 12-hour clock
                minTime: "08:00", // Define time range (optional)
                maxTime: "18:00"
            });

            // Fetch and update available practitioners based on selected time
            document.getElementById('date_time').addEventListener('change', function() {
                var selectedTime = this.value;
                fetchAvailablePractitioners(selectedTime);
            });
        });

        function fetchAvailablePractitioners(selectedTime) {
            fetch('addPatientAppointment.php?time=' + encodeURIComponent(selectedTime))
                .then(response => response.json())
                .then(data => {
                    var practitionerSelect = document.getElementById('staff_id');
                    practitionerSelect.innerHTML = '<option value=""></option>';
                    data.forEach(practitioner => {
                        var option = document.createElement('option');
                        option.value = practitioner.user_id;
                        option.text = `${practitioner.first_name} ${practitioner.last_name} (${practitioner.staff_type})`;
                        practitionerSelect.add(option);
                    });
                })
                .catch(error => console.error('Error fetching practitioners:', error));
        }
    </script>
</body>

</html>