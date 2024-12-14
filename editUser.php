<?php
require_once 'UserController.php';
require "./includes/conn.php";
require_once 'StaffTypeController.php';
require_once 'StaffProfileController.php';
$staffTypecontroller = new StaffTypeController();
$staffProfileController = new StaffProfileController;
$userController = new UserController();
$staffTypes = $staffTypecontroller->listStaffTypes();
$user_type = $_SESSION['user_type'];

if (!isset($user_type) || $user_type != 'admin') {
    // Redirect to the home page if user should not have access to this portal
    header('Location: home.php');
    exit();
}

$error_message = '';
$success_message = '';
$email = $first_name = $last_name = $user_password = $user_type = $phone = $user_image = ''; // Default empty values

// Check if user_id is set in the URL for initial loading
if (isset($_GET['id'])) {
    $user_id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
    $user = $userController->viewUser($user_id);

    if ($user) {
        // Populate variables with user data
        $email = $user['email'];
        $first_name = $user['first_name'];
        $last_name = $user['last_name'];
        $user_password = $user['user_password'];
        $user_type = $user['user_type'];
        $phone = $user['phone'];
        $user_image = $user['user_image'];
        $staff_type = $user_type == 'staff' ? $staffProfileController->viewStaffTypeForUser($user_id) : ''; // Get Staff profile if user type of selected user is staff

    } else {
        $error_message = "User not found.";
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process the form submission to update user
    $user_id = filter_var($_POST['user_id'], FILTER_SANITIZE_NUMBER_INT);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $first_name = filter_var($_POST['first_name'], FILTER_SANITIZE_STRING);
    $last_name = filter_var($_POST['last_name'], FILTER_SANITIZE_STRING);
    $new_password = filter_var($_POST['user_password'], FILTER_SANITIZE_STRING);
    $current_password = $_POST['current_password'];


    // Check if a new password is provided, otherwise use the current one
    $user_password = !empty($new_password) ? password_hash($new_password, PASSWORD_DEFAULT) : $current_password;

    $user_type = filter_var($_POST['user_type'], FILTER_SANITIZE_STRING);

    $staff_type = filter_var($_POST['staff_type'], FILTER_SANITIZE_STRING);

    $phone = filter_var($_POST['phone'], FILTER_SANITIZE_STRING);

    // Handle image upload
    if (isset($_FILES['user_image']) && $_FILES['user_image']['error'] == 0) {
        $file_tmp = $_FILES['user_image']['tmp_name'];
        $file_name = $_FILES['user_image']['name'];
        $file_size = $_FILES['user_image']['size'];
        $file_type = $_FILES['user_image']['type'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        // Allowed file types
        $allowed_ext = array("jpg", "jpeg", "png", "pdf");

        if (in_array($file_ext, $allowed_ext) && $file_size <= 5000000) { // 5MB max size
            $user_image = file_get_contents($file_tmp);
        } else {
            $error_message = "Invalid file type or size.";
        }
    } else {
        // If no new image is uploaded, keep the current image
        $user_image = base64_decode($_POST['current_image']);
    }

    // Validate phone number
    if (!preg_match('/^[0-9]{10}$/', $phone)) {
        $error_message = "Invalid phone number. Please enter a 10-digit phone number.";
    } elseif (!empty($new_password) && !preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,}$/', $new_password)) {
        $error_message = "Password must be at least 8 characters long, contain at least one letter, one number, and one special character (!@#$%^&*).";
    } elseif (in_array($user_type, ['staff', 'patient', 'admin'])) {

        // Update User Table
        if ($userController->updateUser($user_id, $email, $first_name, $last_name, $user_password, $user_type, $phone, $user_image)) {
            $success_message = "User updated successfully!";
        } else {
            $error_message = "Failed to update user. Email already exists.";
        }
    } else {
        $error_message = "Invalid user type.";
    }

    // Update Staff profile type if user is type staff
    
    if ($user_type === 'staff') {
        if ($staffProfileController->updateStaffProfileTypeByUserID($user_id, $staff_type)) {
            $success_message = "User User updated successfully!";
        } else {
            $message = "Error Updating User Staff Type, please see your Admin.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clinic | Edit User</title>
    <link rel="icon" href="images/icon.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="editStyle.css">

</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="text-center mb-3">
            <a href="addUser.php" class="btn btn-primary">Add New User</a>
            <a href='userList.php' class='btn btn-secondary'>Back to Users</a>
        </div>

        <div class="card">
            <div class="card-body">
                <h2 class="card-title text-center mb-4">Update User</h2>

                <?php if (!empty($error_message)): ?>
                    <p class="text-danger text-center"><?php echo $error_message; ?></p>
                <?php elseif (!empty($success_message)): ?>
                    <p class="text-success text-center"><?php echo $success_message; ?></p>
                <?php endif; ?>

                <?php if (empty($success_message)): ?>
                    <form method="POST" action="editUser.php" enctype="multipart/form-data">
                        <input type="hidden" name="_token" value="<?php echo $_SESSION['_token']; ?>">
                        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">
                        <input type="hidden" name="current_password" value="<?php echo htmlspecialchars($user_password); ?>">
                        <input type="hidden" name="current_image" value="<?php echo base64_encode($user_image); ?>">

                        <div class="mb-3">
                            <label for="email" class="form-label">Email:</label>
                            <input type="email" id="email" name="email" class="form-control" required
                                value="<?php echo htmlspecialchars($email); ?>" autocomplete="off">
                        </div>

                        <div class="mb-3">
                            <label for="first_name" class="form-label">First Name:</label>
                            <input type="text" id="first_name" name="first_name" class="form-control" required
                                value="<?php echo htmlspecialchars($first_name); ?>" autocomplete="off">
                        </div>

                        <div class="mb-3">
                            <label for="last_name" class="form-label">Last Name:</label>
                            <input type="text" id="last_name" name="last_name" class="form-control" required
                                value="<?php echo htmlspecialchars($last_name); ?>" autocomplete="off">
                        </div>

                        <div class="mb-3">
                            <label for="user_password" class="form-label">User Password:</label>
                            <input type="password" id="user_password" name="user_password" class="form-control"
                                autocomplete="new-password">
                            <small class="form-text text-muted">Leave empty to keep the current password.</small>
                        </div>

                        <!-- User Type -->
                        <div class="mb-3">
                            <label for="user_type" class="form-label">User Type:</label>
                            <select id="user_type" name="user_type" class="form-control" required>
                                <option value=""></option>
                                <option value="staff" <?php echo $user_type == 'staff' ? 'selected' : ''; ?>>Staff</option>
                                <option value="patient" <?php echo $user_type == 'patient' ? 'selected' : ''; ?>>Patient</option>
                                <option value="admin" <?php echo $user_type == 'admin' ? 'selected' : ''; ?>>Admin</option>
                            </select>
                        </div>

                        <!-- Staff Type -->
                        <div class="mb-3" id="staff_type_container" style="display: none;">
                            <label for="staff_type_select" class="form-label">Select Staff Type:</label>
                            <select class="form-control" id="staff_type_select" name="staff_type">
                                <option value=""></option>
                                <?php foreach ($staffTypes as $staffType): ?>
                                    <option value="<?php echo htmlspecialchars($staffType['staff_type']); ?>"
                                        <?php echo (isset($staff_type['staff_profile_type']) && $staff_type['staff_profile_type'] == $staffType['staff_type']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($staffType['staff_type']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone:</label>
                            <input type="text" id="phone" name="phone" class="form-control" required
                                value="<?php echo htmlspecialchars($phone); ?>" autocomplete="off">
                        </div>

                        <div class="mb-3">
                            <label for="user_image" class="form-label"><i class="fas fa-file-upload"></i> Upload File</label>
                            <input type="file" class="form-control" id="user_image" name="user_image" accept=".jpg,.jpeg,.png,.pdf">
                            <div class="invalid-feedback">
                                Please upload a valid file (jpg, jpeg, png, pdf).
                            </div>
                        </div>

                        <div class="text-center">
                            <input type="submit" class="btn btn-success" value="Update User">
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('user_type').addEventListener('change', function() {
            const staffTypeContainer = document.getElementById('staff_type_container');
            const staffTypeSelect = document.getElementById('staff_type_select');

            if (this.value === 'staff') {
                staffTypeContainer.style.display = 'block';
                staffTypeSelect.required = true;
            } else {
                staffTypeContainer.style.display = 'none';
                staffTypeSelect.required = false;
                staffTypeSelect.value = '';
            }
        });

        // Trigger the change event on page load if staff is selected
        window.addEventListener('load', function() {
            const userType = document.getElementById('user_type');
            if (userType.value === 'staff') {
                userType.dispatchEvent(new Event('change'));
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>