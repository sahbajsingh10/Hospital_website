<?php
require_once 'StaffProfileController.php';

$error_message = '';
$success_message = '';
$user_id = $first_name = $last_name = $staff_profile_type = ''; // Default empty values

// Check if staffprofile_id is set in the URL for initial loading
if (isset($_GET['id'])) {
    $staffprofile_id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
    $controller = new StaffProfileController();
    $staffprofile = $controller->viewStaffProfile($staffprofile_id);

    if ($staffprofile) {
        // Populate variables with staffprofile data
        $user_id = $staffprofile['user_id'];
        $first_name = $staffprofile['first_name'];
        $last_name = $staffprofile['last_name'];
        $staff_profile_type = $staffprofile['staff_profile_type'];
    } else {
        $error_message = "Staff Profile not found.";
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
    <title>Clinic | Staff Profile Details</title> <link rel="icon" href="images/icon.png" type="image/png">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="viewStyle.css">
</head>

<body class="bg-light">

    <div class="container my-5">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h1 class="card-title mb-0">Staff Profile Details</h1>
            </div>
            <div class="card-body">
                <?php if ($error_message): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>

                <p><strong>ID:</strong> <?php echo htmlspecialchars($staffprofile['id']); ?></p>
                <p><strong>Staff ID:</strong> <?php echo htmlspecialchars($staffprofile['user_id']); ?></p>
                <p><strong>First Name:</strong> <?php echo htmlspecialchars($staffprofile['first_name']); ?></p>
                <p><strong>Last Name:</strong> <?php echo htmlspecialchars($staffprofile['last_name']); ?></p>
                <p><strong>Staff Type:</strong> <?php echo htmlspecialchars($staffprofile['staff_profile_type']); ?></p>
            </div>
            <div class="card-footer text-end">
                <a href="staffprofileList.php" class="btn btn-secondary">Back to Staff Profiles</a>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>

</html>