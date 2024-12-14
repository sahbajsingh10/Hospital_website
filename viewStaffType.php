<?php
require_once 'StaffTypeController.php';

$error_message = '';
$success_message = '';
$staff_type = $take_appointment = ''; // Default empty values

// Check if stafftype_id is set in the URL for initial loading
if (isset($_GET['id'])) {
    $stafftype_id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
    $controller = new StaffTypeController();
    $stafftype = $controller->viewStaffType($stafftype_id);

    if ($stafftype) {
        // Populate variables with stafftype data
        $staff_type = $stafftype['staff_type'];
        $take_appointment = $stafftype['take_appointment'];
    } else {
        $error_message = "Staff Type not found.";
    }
}
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
    <title>Clinic | Staff Type Details</title> <link rel="icon" href="images/icon.png" type="image/png">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="viewStyle.css">
</head>

<body class="bg-light">

    <div class="container my-5">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h1 class="card-title mb-0">Staff Type Details</h1>
            </div>
            <div class="card-body">
                <?php if ($error_message): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>

                <p><strong>ID:</strong> <?php echo htmlspecialchars($stafftype['id']); ?></p>
                <p><strong>Staff Type:</strong> <?php echo htmlspecialchars($stafftype['staff_type']); ?></p>
                <p><strong>Takes Appointments:</strong> <?php echo htmlspecialchars($stafftype['take_appointment']); ?></p>
            </div>
            <div class="card-footer text-end">
                <a href="editStaffType.php?id=<?php echo $stafftype_id; ?>" class="btn btn-warning">Edit</a>
                <a href="stafftypeList.php" class="btn btn-secondary">Back to Appointment Types</a>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>

</html>