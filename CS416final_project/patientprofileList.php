<?php
require_once 'PatientProfileController.php';
$user_type = $_SESSION['user_type'];

if (!isset($user_type) || $user_type != 'staff') {
    // Redirect to the home page if user should not have access to this portal
    header('Location: home.php');
    exit();
}

$controller = new PatientProfileController();
$patientprofiles = $controller->listPatientProfiles();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clinic | Patient Profile List</title>  <link rel="icon" href="images/icon.png" type="image/png">
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
</head>
<?php include "header.php"; ?>
<body>
    <div class="container my-5">
        <div class="d-flex justify-content-between align-items-center">
            <h1>Patient Profile List</h1>
        </div>
        <div class="table-responsive mt-3">
            <table id="patientprofileTable" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Patient ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($patientprofiles as $patientprofile): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($patientprofile['id']); ?></td>
                        <td><?php echo htmlspecialchars($patientprofile['user_id']); ?></td>
                        <td><?php echo htmlspecialchars($patientprofile['first_name']); ?></td>
                        <td><?php echo htmlspecialchars($patientprofile['last_name']); ?></td>
                        <td>
                            <a href="viewPatientProfile.php?id=<?php echo $patientprofile['id']; ?>" class="btn btn-info btn-sm">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
  
    <!-- jQuery, Bootstrap JS, and DataTables JS -->
    <!-- Include jQuery and DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script>
    $(document).ready(function() {
        $('#patientprofileTable').DataTable({
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