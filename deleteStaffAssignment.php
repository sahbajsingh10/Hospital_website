<?php
require_once 'StaffAssignmentController.php';

$error_message = '';
$success_message = '';
$staff_id = $assignment_id = $date_time = $shift_length = '';
$staffassignment_id = null;

$controller = new StaffAssignmentController();

// Database connection
$conn = new mysqli('localhost', 'root', '', 'hospitaldb');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch assignment names and map them by ID
$sql = "SELECT id, assignment_name FROM assignmenttypes";
$result = $conn->query($sql);

$assignment_names = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $assignment_names[$row['id']] = $row['assignment_name'];
    }
}

if (isset($_GET['id'])) {
    $staffassignment_id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
    $staffassignment = $controller->viewStaffAssignment($staffassignment_id);

    if ($staffassignment) {
        $staff_id = $staffassignment['staff_id'];
        $assignment_id = $staffassignment['assignment_id'];
        $date_time = (new DateTime($staffassignment['date_time']))->format('Y-m-d h:i A'); // Format date with AM/PM
        $shift_length = $staffassignment['shift_length'];
    } else {
        $error_message = "Staff Assignment not found.";
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $staffassignment_id = filter_var($_POST['staffassignment_id'], FILTER_SANITIZE_NUMBER_INT);

    if ($controller->deleteStaffAssignment($staffassignment_id)) {
        $success_message = "Staff Assignment deleted successfully!";
        header("Location: staffassignmentList.php");
        exit();
    } else {
        $error_message = "Failed to delete staff assignment.";
    }
}
$user_type = $_SESSION['user_type'];

if (!isset($user_type) || $user_type != 'staff') {
    // Redirect to the home page if user should not have access to this portal
    header('Location: home.php');
    exit();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clinic | Delete Staff Assignment</title>
    <link rel="icon" href="images/icon.png" type="image/png">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="deleteStyle.css">
</head>

<body class="bg-light">
    <div class="container my-5">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h1 class="card-title mb-0">Delete This Staff Assignment</h1>
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

                <?php if ($staffassignment): ?>
                    <p><strong>ID:</strong> <?php echo htmlspecialchars($staffassignment['id']); ?></p>
                    <p><strong>Staff ID:</strong> <?php echo htmlspecialchars($staffassignment['staff_id']); ?></p>
                    <p><strong>Assignment:</strong> <?php echo htmlspecialchars($assignment_names[$staffassignment['assignment_id']]); ?></p> <!-- Update to show assignment name -->
                    <p><strong>Date:</strong> <?php echo htmlspecialchars($date_time); ?></p>
                    <p><strong>Shift Length:</strong> <?php echo htmlspecialchars($staffassignment['shift_length']); ?></p>
                <?php endif; ?>
            </div>
            <div class="card-footer text-end">
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                    <i class="fas fa-trash-alt"></i> Delete
                </button>
                <a href="staffassignmentList.php" class="btn btn-secondary">Back to Staff Assignments</a>
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
                    Are you sure you want to delete this staff assignment?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form method="POST" action="deleteStaffAssignment.php" style="display: inline;">

                        <input type="hidden" name="_token" value="<?php echo $_SESSION['_token']; ?>">
                        <input type="hidden" name="staffassignment_id" value="<?php echo htmlspecialchars($staffassignment_id); ?>">
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