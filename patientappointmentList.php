<?php
session_start(); // Start the session

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'PatientAppointmentController.php';

$controller = new PatientAppointmentController();
$user_id = $_SESSION['user_id']; // Get the logged-in user's ID
$appointments = $controller->listAppointments($user_id);

// Fetch doctors' names
$conn = new mysqli('localhost', 'root', '', 'hospitaldb');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$doctors = [];
$sql = "SELECT user_id, first_name, last_name FROM staffprofiles";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $doctors[$row['user_id']] = $row['first_name'] . ' ' . $row['last_name'];
    }
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
    <title>Clinic | Appointments</title>
    <link rel="icon" href="images/icon.png" type="image/png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="bootstrap4.5.2.css">
    <link rel="stylesheet" href="hospital-style.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="table-styles.css">
    <style>
        body{
            margin-bottom:100px;
        }
        .dashboard-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
            padding-top: 8vh !important;
        }

        .page-title {
            color: #2e8b57;
            font-size: 3rem;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 3px solid #2e8b57;
        }

        .nav-tabs {
            border-bottom: 2px solid #dee2e6;
            margin-bottom: 2rem;
        }

        .nav-tabs .nav-link {
            color: #666;
            font-weight: 500;
            padding: 1rem 1.5rem;
            border: none;
            border-bottom: 2px solid transparent;
            margin-bottom: -2px;
            transition: all 0.3s ease;
        }

        .nav-tabs .nav-link:hover {
            border-color: transparent;
            color: #2e8b57;
        }

        .nav-tabs .nav-link.active {
            color: #2e8b57;
            border-bottom: 2px solid #2e8b57;
            background: none;
        }

        .appointment-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 1.5rem;
            transition: transform 0.3s ease;
        }

        .appointment-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }

        .card-body {
            padding: 1.5rem;
        }

        .card-title {
            color: #2e8b57;
            font-size: 1.25rem;
            margin-bottom: 1rem;
        }

        .appointment-info {
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .info-label {
            font-weight: 600;
            color: #666;
            margin-right: 1rem;
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }

        .btn {
            padding: 0.5rem 1rem;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background-color: #2e8b57;
            border-color: #2e8b57;
        }

        .btn-primary:hover {
            background-color: #246b43;
            border-color: #246b43;
        }

        .add-appointment-btn {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            padding: 1rem;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .status-scheduled {
            background-color: #e3f2fd;
            color: #1976d2;
        }

        .status-completed {
            background-color: #e8f5e9;
            color: #2e7d32;
        }

        .status-canceled {
            background-color: #ffebee;
            color: #c62828;
        }

        @media (max-width: 768px) {
            .appointment-info {
                grid-template-columns: 1fr;
            }
            
            .action-buttons {
                flex-wrap: wrap;
            }
        }
    </style>
</head>
<?php include "header.php"; ?>
<body>
    <div class="dashboard-container">
        <h1 class="page-title">My Appointments</h1>

        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" id="upcoming-tab" data-bs-toggle="tab" data-bs-target="#upcoming">
                    <i class="fas fa-calendar-alt me-2"></i>Upcoming
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="past-tab" data-bs-toggle="tab" data-bs-target="#past">
                    <i class="fas fa-history me-2"></i>Past
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="canceled-tab" data-bs-toggle="tab" data-bs-target="#canceled">
                    <i class="fas fa-ban me-2"></i>Canceled
                </button>
            </li>
        </ul>

        <div class="tab-content">
            <!-- Upcoming Appointments -->
            <div class="tab-pane fade show active" id="upcoming">
                <?php
                if (count($appointments) > 0) {
                    foreach ($appointments as $appointment) {
                        if ($appointment['appointment_status'] === 'scheduled') {
                            ?>
                            <div class="appointment-card">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="fas fa-stethoscope me-2"></i>
                                        <?php echo htmlspecialchars($appointment['appointment_type']); ?>
                                    </h5>
                                    <div class="appointment-info">
                                        <span class="info-label"><i class="fas fa-user-md me-2"></i>Practitioner:</span>
                                        <span><?php echo htmlspecialchars($doctors[$appointment['staff_id']] ?? 'Unknown'); ?></span>
                                        
                                        <span class="info-label"><i class="fas fa-calendar me-2"></i>Date:</span>
                                        <span><?php 
                                            $dateTime = new DateTime($appointment['date_time']);
                                            echo $dateTime->format('F j, Y h:i A'); 
                                        ?></span>
                                        
                                        <span class="info-label"><i class="fas fa-info-circle me-2"></i>Status:</span>
                                        <span class="status-badge status-scheduled">
                                            <?php echo htmlspecialchars($appointment['appointment_status']); ?>
                                        </span>
                                    </div>
                                    <div class="action-buttons">
                                        <a href="viewPatientAppointment.php?id=<?php echo $appointment['id']; ?>" 
                                           class="btn btn-info">
                                            <i class="fas fa-eye me-2"></i>View
                                        </a>
                                        <a href="editPatientAppointment.php?id=<?php echo $appointment['id']; ?>" 
                                           class="btn btn-warning">
                                            <i class="fas fa-edit me-2"></i>Edit
                                        </a>
                                        <button class="btn btn-danger" 
                                                onclick="setAppointmentId('<?php echo $appointment['id']; ?>')" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#cancelModal">
                                            <i class="fas fa-times me-2"></i>Cancel
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    }
                } else {
                    echo '<p class="text-center mt-5">You have no upcoming appointments.</p>';
                }
                ?>
            </div>

            <!-- Past Appointments -->
            <div class="tab-pane fade" id="past">
                <?php
                if (count($appointments) > 0) {
                    foreach ($appointments as $appointment) {
                        if ($appointment['appointment_status'] === 'completed') {
                            ?>
                            <div class="appointment-card">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="fas fa-stethoscope me-2"></i>
                                        <?php echo htmlspecialchars($appointment['appointment_type']); ?>
                                    </h5>
                                    <div class="appointment-info">
                                        <span class="info-label"><i class="fas fa-user-md me-2"></i>Practitioner:</span>
                                        <span><?php echo htmlspecialchars($doctors[$appointment['staff_id']] ?? 'Unknown'); ?></span>
                                        
                                        <span class="info-label"><i class="fas fa-calendar me-2"></i>Date:</span>
                                        <span><?php 
                                            $dateTime = new DateTime($appointment['date_time']);
                                            echo $dateTime->format('F j, Y h:i A'); 
                                        ?></span>
                                        
                                        <span class="info-label"><i class="fas fa-info-circle me-2"></i>Status:</span>
                                        <span class="status-badge status-completed">
                                            <?php echo htmlspecialchars($appointment['appointment_status']); ?>
                                        </span>
                                    </div>
                                    <div class="action-buttons">
                                        <a href="viewPatientAppointment1.php?id=<?php echo $appointment['id']; ?>" 
                                           class="btn btn-info">
                                            <i class="fas fa-eye me-2"></i>View
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    }
                } else {
                    echo '<p class="text-center mt-5">You have no past appointments.</p>';
                }
                ?>
            </div>

            <!-- Canceled Appointments -->
            <div class="tab-pane fade" id="canceled">
                <?php
                if (count($appointments) > 0) {
                    foreach ($appointments as $appointment) {
                        if ($appointment['appointment_status'] === 'canceled') {
                            ?>
                            <div class="appointment-card">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="fas fa-stethoscope me-2"></i>
                                        <?php echo htmlspecialchars($appointment['appointment_type']); ?>
                                    </h5>
                                    <div class="appointment-info">
                                        <span class="info-label"><i class="fas fa-user-md me-2"></i>Practitioner:</span>
                                        <span><?php echo htmlspecialchars($doctors[$appointment['staff_id']] ?? 'Unknown'); ?></span>
                                        
                                        <span class="info-label"><i class="fas fa-calendar me-2"></i>Date:</span>
                                        <span><?php 
                                            $dateTime = new DateTime($appointment['date_time']);
                                            echo $dateTime->format('F j, Y h:i A'); 
                                        ?></span>
                                        
                                        <span class="info-label"><i class="fas fa-info-circle me-2"></i>Status:</span>
                                        <span class="status-badge status-canceled">
                                            <?php echo htmlspecialchars($appointment['appointment_status']); ?>
                                        </span>
                                    </div>
                                    <div class="action-buttons">
                                        <a href="viewPatientAppointment1.php?id=<?php echo $appointment['id']; ?>" 
                                           class="btn btn-info">
                                            <i class="fas fa-eye me-2"></i>View
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    }
                } else {
                    echo '<p class="text-center mt-5">You have no canceled appointments.</p>';
                }
                ?>
            </div>
        </div>
    </div>

    <a href="addPatientAppointment.php" class="btn btn-primary add-appointment-btn">
        <i class="fas fa-plus"></i>
    </a>

    <!-- Cancel Modal -->
    <div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cancelModalLabel">Cancel Appointment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="cancelPatientAppointment.php" method="post">
                        <input type="hidden" id="modal-appointment-id" name="appointment_id">
                        <p>Are you sure you want to cancel this appointment?</p>
                        <div class="d-flex justify-content-end"><center>
                            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-danger">Cancel</button>
            </center>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function setAppointmentId(appointmentId) {
            document.getElementById('modal-appointment-id').value = appointmentId;
        }
    </script>
</body>
<br><br><br><br><br><br><br><br>
<?php include "footer.php"; ?>
</html>