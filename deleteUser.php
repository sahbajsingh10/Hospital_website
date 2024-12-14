<?php
require_once 'UserController.php';
$user_type = $_SESSION['user_type'];

if (!isset($user_type) || $user_type != 'admin') {
    // Redirect to the home page if user should not have access to this portal
    header('Location: home.php');
    exit();
}
$error_message = '';
$success_message = '';
$email = $first_name = $last_name = $user_password = $user_type = $phone = $user_image = '';
$user_id = null;

$controller = new UserController();

if (isset($_GET['id'])) {
    $user_id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
    $user = $controller->viewUser($user_id);

    if ($user) {
        $email = $user['email'];
        $first_name = $user['first_name'];
        $last_name = $user['last_name'];
        $user_password = $user['user_password'];
        $user_type = $user['user_type'];
        $phone = $user['phone'];
        $user_image = $user['user_image'];
    } else {
        $error_message = "User not found.";
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = filter_var($_POST['user_id'], FILTER_SANITIZE_NUMBER_INT);

    if ($controller->deleteUser($user_id)) {
        $success_message = "User deleted successfully!";
        header("Location: userList.php");
        exit();
    } else {
        $error_message = "Failed to delete user.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clinic | Delete User</title>
    <link rel="icon" href="images/icon.png" type="image/png">
    <link rel="stylesheet" href="style1.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">
    <!-- Lightbox2 CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css">
    <link rel="stylesheet" href="deleteStyle.css">
    <!-- Custom CSS for Lightbox -->
    <style>
        /* Table images */
        .imageTable {
            width: 35px;
            /* You can adjust this size as needed */
            height: 35px;
            /* You can adjust this size as needed */
            object-fit: cover;
            /* Ensures the image fills the space without distortion */
            cursor: pointer;
        }
    </style>
</head>

<body class="bg-light">
    <div class="container my-5">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h1 class="card-title mb-0">Delete User</h1>
            </div>
            <div class="card-body">
                <?php if ($error_message): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo htmlspecialchars($error_message); ?>
                    </div>
                <?php elseif ($success_message): ?>
                    <div class="alert alert-success" role="alert">
                        <?php echo htmlspecialchars($success_message); ?>
                    </div>
                <?php endif; ?>

                <?php if ($user): ?>
                    <p><strong>ID:</strong> <?php echo htmlspecialchars($user['id']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                    <p><strong>First Name:</strong> <?php echo htmlspecialchars($user['first_name']); ?></p>
                    <p><strong>Last Name:</strong> <?php echo htmlspecialchars($user['last_name']); ?></p>
                    <p><strong>User Type:</strong> <?php echo htmlspecialchars($user['user_type']); ?></p>
                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone']); ?></p>
                    <p><strong>User Image:</strong><?php
                                                    // Decode the BLOB image data
                                                    $imageData = base64_encode($user['user_image']);
                                                    $src = 'data:image/jpeg;base64,' . $imageData;
                                                    ?>
                        <!-- Lightbox image link -->
                        <a href="<?php echo $src; ?>" data-lightbox="user-images">
                            <img src="<?php echo $src; ?>" class="imageTable" alt="User Image">
                        </a>
                    </p>
                <?php endif; ?>
            </div>
            <div class="card-footer text-end">
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                    <i class="fas fa-trash-alt"></i> Delete
                </button>
                <a href="userList.php" class="btn btn-secondary">Back to Users</a>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this user?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form method="POST" action="deleteUser.php" style="display: inline;">

                        <input type="hidden" name="_token" value="<?php echo $_SESSION['_token']; ?>">

                        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#userTable').DataTable({
                responsive: true,
                autoWidth: false,
                paging: true,
                searching: true
            });

            // Lightbox settings
            lightbox.option({
                'resizeDuration': 200,
                'wrapAround': true,
                'alwaysShowNavOnTouchDevices': true,
                'imageFadeDuration': 300,
                'fitImagesInViewport': true,
                'disableScrolling': true,
                'positionFromTop': 285,
                'fadeDuration': 200,
                'showImageNumberLabel': false,
            });
        });
    </script>
</body>

</html>