<?php
require_once 'UserController.php';
$controller = new UserController();
$users = $controller->listUsers();

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
    <title>Clinic | User List</title>
    <link rel="icon" href="images/icon.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">
    <!-- Lightbox2 CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css">
    <!-- Custom CSS for Lightbox -->
    <link rel="stylesheet" href="bootstrap4.5.2.css">
    <link rel="stylesheet" href="hospital-style.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="table-styles.css">
    <style>
        /* Table images */
        .imageTable {
            width: 35px; /* You can adjust this size as needed */
            height: 35px; /* You can adjust this size as needed */
            object-fit: cover; /* Ensures the image fills the space without distortion */
            cursor: pointer;
        }
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
            <h1>User List</h1>
        </div>
        <div class="table-responsive mt-3">
            <table id="userTable" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Email</th>
                        <th>First&nbsp;Name</th>
                        <th>Last&nbsp;Name</th>
                        <th>User&nbsp;Type</th>
                        <th>Phone</th>
                        <th>User Image</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['id']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['first_name']); ?></td>
                        <td><?php echo htmlspecialchars($user['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($user['user_type']); ?></td>
                        <td><?php echo htmlspecialchars($user['phone']); ?></td>
                        <td>
                            <?php
                            // Decode the BLOB image data
                            $imageData = base64_encode($user['user_image']);
                            $src = 'data:image/jpeg;base64,' . $imageData;
                            ?>
                            <!-- Lightbox image link -->
                            <a href="<?php echo $src; ?>" data-lightbox="user-images">
                                <img src="<?php echo $src; ?>" class="imageTable" alt="User Image">
                            </a>
                        </td>
                        <td>
                            <a href="addUser.php?id=<?php echo $user['id']; ?>" class="btn btn-success btn-sm">
                                <i class="fas fa-add"></i>
                            </a>
                            <a href="viewUser.php?id=<?php echo $user['id']; ?>" class="btn btn-info btn-sm">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="editUser.php?id=<?php echo $user['id']; ?>" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="deleteUser.php?id=<?php echo $user['id']; ?>" class="btn btn-danger btn-sm">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <a href="addUser.php" class="btn btn-primary add-appointment-btn">
        <i class="fas fa-plus"></i>
    </a>
    <!-- jQuery, Bootstrap JS, DataTables JS, and Lightbox2 JS -->
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
<br><br><br><br>
<?php include "footer.php"; ?>
</html>
