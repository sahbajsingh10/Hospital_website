<?php
require_once 'AppointmentTypeController.php';

$controller = new AppointmentTypeController();
$appointmenttypes = $controller->listAppointmentTypes();
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
    <title>Clinic | Appointment Type List</title>  <link rel="icon" href="images/icon.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
   
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
<?php include "header.php"; ?>

<body>
    <div class="container my-5">
        <div class="d-flex justify-content-between align-items-center">
            <h1>Appointment Type List</h1>
        </div>
        <div class="table-responsive mt-3">
            <table id="appointmenttypeTable" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Appointment Type</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($appointmenttypes as $appointmenttype): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($appointmenttype['id']); ?></td>
                        <td><?php echo htmlspecialchars($appointmenttype['appointment_name']); ?></td>
                        <td>
                            <a href="addAppointmentType.php?id=<?php echo $appointmenttype['id']; ?>" class="btn btn-success btn-sm">
                                <i class="fas fa-add"></i>
                            </a>
                            <a href="viewAppointmentType.php?id=<?php echo $appointmenttype['id']; ?>" class="btn btn-info btn-sm">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="editAppointmentType.php?id=<?php echo $appointmenttype['id']; ?>" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="deleteAppointmentType.php?id=<?php echo $appointmenttype['id']; ?>" class="btn btn-danger btn-sm">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <a href="addAppointmentType.php" class="btn btn-primary add-appointment-btn">
        <i class="fas fa-plus"></i>
    </a>
    <!-- jQuery, Bootstrap JS, and DataTables JS -->
    <!-- Include jQuery and DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script>
    $(document).ready(function() {
        $('#appointmenttypeTable').DataTable({
            responsive: true,
            autoWidth: false,
            paging: true,
            searching: true
        });
    });
    </script>
</body><br><br><br><br>
<?php include "footer.php"; ?>
</html>