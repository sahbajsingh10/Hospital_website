<?php
require_once 'PatientProfileController.php';
$user_type = $_SESSION['user_type'];

if (!isset($user_type) || $user_type != 'staff') {
    // Redirect to the home page if user should not have access to this portal
    header('Location: home.php');
    exit();
}
$error_message = '';
$success_message = '';
$user_id = $first_name = $last_name = ''; // Default empty values

// Check if patientprofile_id is set in the URL for initial loading
if (isset($_GET['id'])) {
    $patientprofile_id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
    $controller = new PatientProfileController();
    $patientprofile = $controller->viewPatientProfile($patientprofile_id);

    if ($patientprofile) {
        // Populate variables with patientprofile data
        $user_id = $patientprofile['user_id'];
        $first_name = $patientprofile['first_name'];
        $last_name = $patientprofile['last_name'];
    } else {
        $error_message = "Patient Profile not found.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clinic | Patient Profile Details</title> <link rel="icon" href="images/icon.png" type="image/png">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="viewStyle.css">
</head>

<body class="bg-light">

    <div class="container my-5">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h1 class="card-title mb-0">Patient Profile Details</h1>
            </div>
            <div class="card-body">
                <?php if ($error_message): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>

                <p><strong>ID:</strong> <?php echo htmlspecialchars($patientprofile['id']); ?></p>
                <p><strong>Patient ID:</strong> <?php echo htmlspecialchars($patientprofile['user_id']); ?></p>
                <p><strong>First Name:</strong> <?php echo htmlspecialchars($patientprofile['first_name']); ?></p>
                <p><strong>Last Name:</strong> <?php echo htmlspecialchars($patientprofile['last_name']); ?></p>
            </div>
            <div class="card-footer text-end">
                <a href="patientprofileList.php" class="btn btn-secondary">Back to Patient Profiles</a>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>

</html>