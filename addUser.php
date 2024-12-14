<?php
require './includes/conn.php';
require_once 'UserController.php';
require_once 'StaffTypeController.php';
require_once 'StaffProfileController.php';

$user_type = $_SESSION['user_type'];

if (!isset($user_type) || $user_type != 'admin') {
    header('Location: home.php');
    exit();
}

$controller = new UserController();
$staffTypeController = new StaffTypeController();
$staffProfileController = new StaffProfileController();
$staffTypes = $staffTypeController->listStaffTypes();

$error_message = '';
$email = $first_name = $last_name = $user_password = $user_type = $phone = $user_image = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // $user_id = htmlspecialchars(trim($_POST['user_id']));
    $email = trim($_POST['email']);
    $first_name = htmlspecialchars(trim($_POST['first_name']));
    $last_name = htmlspecialchars(trim($_POST['last_name']));
    $user_password = trim($_POST['user_password']);
    $user_type = htmlspecialchars(trim($_POST['user_type']));
    $staff_type = htmlspecialchars(trim($_POST['staff_type']));
    $phone = htmlspecialchars(trim($_POST['phone']));

    if (isset($_FILES['user_image']) && $_FILES['user_image']['error'] == 0) {
        $file_tmp = $_FILES['user_image']['tmp_name'];
        $file_name = $_FILES['user_image']['name'];
        $file_size = $_FILES['user_image']['size'];
        $file_type = $_FILES['user_image']['type'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        $allowed_ext = array("jpg", "jpeg", "png", "pdf");

        if (in_array($file_ext, $allowed_ext) && $file_size <= 5000000) {
            $user_image = file_get_contents($file_tmp);
        } else {
            $error_message = "Invalid file type or size.";
        }
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email address. Please include an '@' in the email address.";
    } elseif (!preg_match('/^[0-9]{10}$/', $phone)) {
        $error_message = "Invalid phone number. Please enter a 10-digit phone number.";
    } elseif (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,}$/', $user_password)) {
        $error_message = "Password must be at least 8 characters long, contain at least one letter, one number, and one special character (!@#$%^&*).";
    } else {
        if ($controller->addUser($email, $first_name, $last_name, $user_password, $user_type, $phone, $user_image)) {

            $user_id = $conn->lastInsertId();

            if ($user_type === 'staff') {
                if ($staffProfileController->updateStaffProfileTypeByUserID($user_id, $staff_type)) {
                    $success_message = "User User updated successfully!";
                } else {
                    $message = "Error Updating User Staff Type, please see your Admin.";
                }
            }

            echo "<div class='alert alert-success'>User added successfully.</div>";
            header("Location: userList.php");
            die();
        } else {
            $error_message = "Failed to add user. Email already exists.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clinic | Add User</title>
    <link rel="icon" href="images/icon.png" type="image/png">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="addStyle.css">
</head>

<body class="bg-light">
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h2 class="mb-0">Add User</h2>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($error_message)): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo htmlspecialchars($error_message); ?>
                            </div>
                        <?php endif; ?>

                        <form method="post" action="addUser.php" enctype="multipart/form-data">
                            <input type="hidden" name="_token" value="<?php echo $_SESSION['_token']; ?>">
                            <!-- //<input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>"> -->

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="text" class="form-control" id="email" name="email" required
                                    value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                                    autocomplete="off">
                            </div>

                            <div class="mb-3">
                                <label for="first_name" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" required
                                    value="<?php echo isset($_POST['first_name']) ? htmlspecialchars($_POST['first_name']) : ''; ?>"
                                    autocomplete="off">
                            </div>

                            <div class="mb-3">
                                <label for="last_name" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" required
                                    value="<?php echo isset($_POST['last_name']) ? htmlspecialchars($_POST['last_name']) : ''; ?>"
                                    autocomplete="off">
                            </div>

                            <div class="mb-3">
                                <label for="user_password" class="form-label">User Password</label>
                                <input type="password" class="form-control" id="user_password" name="user_password" required
                                    value="<?php echo isset($_POST['user_password']) ? htmlspecialchars($_POST['user_password']) : ''; ?>"
                                    autocomplete="new-password">
                            </div>

                            <div class="mb-3">
                                <label for="user_type" class="form-label">User Type</label>
                                <select class="form-control" id="user_type" name="user_type" required>
                                    <option value=""></option>
                                    <option value="staff" <?php echo isset($_POST['user_type']) && $_POST['user_type'] == 'staff' ? 'selected' : ''; ?>>Staff</option>
                                    <option value="patient" <?php echo isset($_POST['user_type']) && $_POST['user_type'] == 'patient' ? 'selected' : ''; ?>>Patient</option>
                                    <option value="admin" <?php echo isset($_POST['user_type']) && $_POST['user_type'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                                </select>
                            </div>

                            <div class="mb-3" id="staff_type" style="display: none;">
                                <label for="staff_type" class="form-label">Staff Type</label>
                                <select class="form-control" id="staff_type_select" name="staff_type">
                                    <option value=""></option>
                                    <?php foreach ($staffTypes as $staffType): ?>
                                        <option value="<?php echo htmlspecialchars($staffType['staff_type']); ?>">
                                            <?php echo htmlspecialchars($staffType['staff_type']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="text" class="form-control" id="phone" name="phone" required
                                    value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>"
                                    autocomplete="off">
                            </div>

                            <div class="mb-3">
                                <label for="user_image" class="form-label">Upload File</label>
                                <input type="file" class="form-control" id="user_image" name="user_image"
                                    accept=".jpg,.jpeg,.png,.pdf" required>
                                <div class="invalid-feedback">
                                    Please upload a valid file (jpg, jpeg, png, pdf).
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Add User</button>
                        </form>
                    </div>
                </div>
                <div class="text-center mt-3">
                    <a href="userList.php" class="btn btn-secondary">Back to Users</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('user_type').addEventListener('change', function() {
            const staffTypeDiv = document.getElementById('staff_type');
            const staffTypeSelect = document.getElementById('staff_type_select');

            if (this.value === 'staff') {
                staffTypeDiv.style.display = 'block';
                staffTypeSelect.required = true;
            } else {
                staffTypeDiv.style.display = 'none';
                staffTypeSelect.required = false;
                staffTypeSelect.value = '';
            }
        });
    </script>
</body>

</html>