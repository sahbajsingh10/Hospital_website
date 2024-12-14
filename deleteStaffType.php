<?php
require_once 'StaffTypeController.php';

// Initialize variables
$error_message = '';
$success_message = '';
$stafftype = null;
$stafftype_id = null;

// Generate CSRF token if it doesn't exist
if (!isset($_SESSION['_token'])) {
    $_SESSION['_token'] = bin2hex(random_bytes(32));
}

// Verify user authorization
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: home.php');
    exit();
}

$controller = new StaffTypeController();

// Handle GET request
if (isset($_GET['id'])) {
    $stafftype_id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
    $stafftype = $controller->viewStaffType($stafftype_id);
    
    if (!$stafftype) {
        $error_message = "Staff Type not found.";
    }
}

// Handle POST request for deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['stafftype_id'])) {
    // Get the staff type before attempting to delete
    $stafftype_id = filter_var($_POST['stafftype_id'], FILTER_SANITIZE_NUMBER_INT);
    $stafftype_to_delete = $controller->viewStaffType($stafftype_id);
    
    // Check if the staff type is in use
    if ($controller->isStaffTypeReferenced($stafftype_to_delete['staff_type'])) {
        $error_message = "Cannot delete this staff type as it is currently assigned to staff members.";
        $stafftype = $stafftype_to_delete; // Maintain the staff type details in view
    } else {
        if ($controller->deleteStaffType($stafftype_id)) {
            $_SESSION['success_message'] = "Staff Type deleted successfully!";
            header("Location: stafftypeList.php");
            exit();
        } else {
            $error_message = "Failed to delete staff type.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clinic | Delete Staff Type</title>
    <link rel="icon" href="images/icon.png" type="image/png">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="deleteStyle.css">
</head>

<body class="bg-light">
    <div class="container my-5">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h1 class="card-title mb-0">Delete Staff Type</h1>
            </div>
            <div class="card-body">
                <?php if ($error_message): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>

                <?php if ($stafftype): ?>
                    <div class="staff-type-details">
                        <p><strong>ID:</strong> <?php echo htmlspecialchars($stafftype['id']); ?></p>
                        <p><strong>Staff Type:</strong> <?php echo htmlspecialchars($stafftype['staff_type']); ?></p>
                        <p><strong>Takes Appointments:</strong> <?php echo htmlspecialchars($stafftype['take_appointment']); ?></p>
                    </div>
                <?php endif; ?>
            </div>
            <div class="card-footer text-end">
                <?php if ($stafftype): ?>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="fas fa-trash-alt"></i> Delete
                    </button>
                <?php endif; ?>
                <a href="stafftypeList.php" class="btn btn-secondary">Back to Staff Types</a>
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
                    Are you sure you want to delete this staff type?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form method="POST">
                        <input type="hidden" name="_token" value="<?php echo $_SESSION['_token']; ?>">
                        <input type="hidden" name="stafftype_id" value="<?php echo htmlspecialchars($stafftype_id); ?>">
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>