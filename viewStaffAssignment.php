<?php
require_once 'StaffAssignmentController.php';

$error_message = '';
$success_message = '';
$staff_id = $assignment_id = $date_time = $shift_length = ''; // Default empty values

// Database connection
$conn = new mysqli('localhost', 'root', '', 'hospitaldb');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch assignment types and map them by ID
$sql = "SELECT id, assignment_name FROM assignmenttypes";
$result = $conn->query($sql);

$assignment_names = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $assignment_names[$row['id']] = $row['assignment_name'];
    }
}

// Check if staffassignment_id is set in the URL for initial loading
if (isset($_GET['id'])) {
    $staffassignment_id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
    $controller = new StaffAssignmentController();
    $staffassignment = $controller->viewStaffAssignment($staffassignment_id);

    if ($staffassignment) {
        // Populate variables with staffassignment data
        $staff_id = $staffassignment['staff_id'];
        $assignment_id = $staffassignment['assignment_id'];
        $date_time = (new DateTime($staffassignment['date_time']))->format('Y-m-d h:i A'); // Format date with AM/PM
        $shift_length = $staffassignment['shift_length'];
    } else {
        $error_message = "Staff Assignment not found.";
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
    <title>Clinic | Staff Assignment Details</title>
    <link rel="icon" href="images/icon.png" type="image/png">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="viewStyle.css">
</head>
<body class="bg-light">
    <div class="container my-5">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h1 class="card-title mb-0">Staff Assignment Details</h1>
            </div>
            <div class="card-body">
                <?php if ($error_message): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>

                <p><strong>ID:</strong> <?php echo htmlspecialchars($staffassignment['id']); ?></p>
                <p><strong>Staff ID:</strong> <?php echo htmlspecialchars($staffassignment['staff_id']); ?></p>
                <p><strong>Assignment:</strong> <?php echo htmlspecialchars($assignment_names[$staffassignment['assignment_id']]); ?></p> <!-- Display assignment name -->
                <p><strong>Date:</strong> <?php echo htmlspecialchars($date_time); ?></p>
                <p><strong>Shift Length:</strong> <?php echo htmlspecialchars($staffassignment['shift_length']); ?></p>
            </div>
            <div class="card-footer text-end">
                <a href="editStaffAssignment.php?id=<?php echo $staffassignment_id; ?>" class="btn btn-warning">Edit</a>
                <a href="staffassignmentList.php" class="btn btn-secondary">Back to Staff Assignments</a>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
