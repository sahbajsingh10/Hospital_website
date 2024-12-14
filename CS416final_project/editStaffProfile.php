<?php
require_once 'StaffProfileController.php';

$profileController = new StaffProfileController();
$error_message = '';
$success_message = '';
$user_id = $first_name = $last_name = $staff_profile_type = ''; // Default empty values

// Fetch staff types from the stafftypes table
$staffprofile_types = [];
$query = "SELECT staff_type FROM stafftypes";
$stmt = $conn->prepare($query);
$stmt->execute();
if ($stmt->rowCount() > 0) {
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $staffprofile_types[] = $row['staff_type'];
    }
}

// Check if staffprofile_id is set in the URL for initial loading
if (isset($_GET['id'])) {
    $staffprofile_id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
    $staffprofile = $profileController->viewStaffProfile($staffprofile_id);

    if ($staffprofile) {
        // Populate variables with staffprofile data
        $user_id = $staffprofile['user_id'];
        $first_name = $staffprofile['first_name'];
        $last_name = $staffprofile['last_name'];
        $staff_profile_type = $staffprofile['staff_profile_type'];
    } else {
        $error_message = "Staff Profile not found.";
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process the form submission to update staffprofile
    $staffprofile_id = filter_var($_POST['staffprofile_id'], FILTER_SANITIZE_NUMBER_INT);
    $user_id = filter_var($_POST['user_id'], FILTER_SANITIZE_NUMBER_INT);
    $first_name = filter_var($_POST['first_name'], FILTER_SANITIZE_STRING);
    $last_name = filter_var($_POST['last_name'], FILTER_SANITIZE_STRING);
    $staff_profile_type = filter_var($_POST['staff_profile_type'], FILTER_SANITIZE_STRING);

    if ($profileController->updateStaffProfile($staffprofile_id, $user_id, $first_name, $last_name, $staff_profile_type)) {
        $success_message = "Staff Profile updated successfully!";
    } else {
        $error_message = "Failed to update staff profile.";
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
    <title>Clinic | Edit Staff Profile</title>
    <link rel="icon" href="images/icon.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="editStyle.css">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="text-center mb-3">
            <a href='staffprofileList.php' class='btn btn-secondary'>Back to Staff Profiles</a>
        </div>

        <div class="card">
            <div class="card-body">
                <h2 class="card-title text-center mb-4">Update Staff Profile</h2>

                <?php if (!empty($error_message)): ?>
                    <p class="text-danger text-center"><?php echo $error_message; ?></p>
                <?php elseif (!empty($success_message)): ?>
                    <p class="text-success text-center"><?php echo $success_message; ?></p>
                <?php else: ?>
                    <form method="POST" action="editStaffProfile.php">

                        <input type="hidden" name="_token" value="<?php echo $_SESSION['_token']; ?>">
                        
                        <input type="hidden" name="staffprofile_id" value="<?php echo htmlspecialchars($staffprofile_id); ?>">

                        <div class="mb-3">
                            <label for="user_id" class="form-label">Staff ID:</label>
                            <input type="text" id="user_id" name="user_id" class="form-control" readonly
                                value="<?php echo htmlspecialchars($user_id); ?>">
                        </div>

                        <div class="mb-3">
                            <label for="first_name" class="form-label">First Name:</label>
                            <input type="text" id="first_name" name="first_name" class="form-control" readonly
                                value="<?php echo htmlspecialchars($first_name); ?>">
                        </div>

                        <div class="mb-3">
                            <label for="last_name" class="form-label">Last Name:</label>
                            <input type="text" id="last_name" name="last_name" class="form-control" readonly
                                value="<?php echo htmlspecialchars($last_name); ?>">
                        </div>

                        <div class="mb-3">
                            <label for="staff_profile_type" class="form-label">Staff Type:</label>
                            <select class="form-control" id="staff_profile_type" name="staff_profile_type" required>
                                <option value=""></option>
                                <?php foreach ($staffprofile_types as $type): ?>
                                    <option value="<?php echo htmlspecialchars($type); ?>" <?php echo (isset($staff_profile_type) && $staff_profile_type == $type) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($type); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="text-center">
                            <input type="submit" class="btn btn-success" value="Update Staff Profile">
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>