<?php
require_once 'AppointmentController.php';
$user_type = $_SESSION['user_type'];

if (!isset($user_type) || $user_type != 'staff') {
    // Redirect to the home page if user should not have access to this portal
    header('Location: home.php');
    exit();
}

$controller = new AppointmentController();
$appointments = $controller->listAppointments();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clinic | Appointment List</title>
    <link rel="icon" href="images/icon.png" type="image/png">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <!-- Include DataTables CSS and jQuery -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">
    <link rel="stylesheet" href="bootstrap4.5.2.css">
    <link rel="stylesheet" href="hospital-style.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="table-styles.css">
    <style>
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
        </style>
</head>


<body>
<?php include "header.php"; ?>
    <div class="container my-5">
        <div class="d-flex justify-content-between align-items-center">
            <h1>Appointment List</h1>
        </div>
        <div class="table-responsive mt-3">
            <table id="appointmentTable" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Patient&nbsp;ID</th>
                        <th>Staff&nbsp;ID</th>
                        <th>Appointment&nbsp;Type</th>
                        <th>Date</th>
                        <th>Appointment&nbsp;Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($appointments as $appointment): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($appointment['id']); ?></td>
                        <td><?php echo htmlspecialchars($appointment['patient_id']); ?></td>
                        <td><?php echo htmlspecialchars($appointment['staff_id']); ?></td>
                        <td><?php echo htmlspecialchars($appointment['appointment_type']); ?></td>

                        <!-- Format the 'date_time' to 12-hour format with AM/PM -->
                        <td>
                            <?php
                                // Convert the date_time string into a DateTime object
                                $dateTime = new DateTime($appointment['date_time']);
                                // Format the date_time in 12-hour format with AM/PM
                                echo $dateTime->format('Y-m-d h:i A'); // 'h' for 12-hour, 'A' for AM/PM
                            ?>
                        </td>
                        
                        <td><?php echo htmlspecialchars($appointment['appointment_status']); ?></td>
                        <td>
                            <a href="addAppointment.php?id=<?php echo $appointment['id']; ?>" class="btn btn-success btn-sm">
                                <i class="fas fa-add"></i>
                            </a>
                            <a href="viewAppointment.php?id=<?php echo $appointment['id']; ?>" class="btn btn-info btn-sm">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="editAppointment.php?id=<?php echo $appointment['id']; ?>" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="deleteAppointment.php?id=<?php echo $appointment['id']; ?>" class="btn btn-danger btn-sm">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <a href="addAppointment.php" class="btn btn-primary add-appointment-btn">
        <i class="fas fa-plus"></i>
    </a>
    <!-- jQuery, Bootstrap JS, and DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script>
    $(document).ready(function() {
        $('#appointmentTable').DataTable({
            responsive: true,
            autoWidth: false,
            paging: true,
            searching: true
        });
    });
    </script>
</body>
<?php include "footer.php"; ?>
</html>