<?php
require_once 'StaffAssignmentController.php';

$controller = new StaffAssignmentController();
$error_message = '';
$success_message = '';
$staff_id = $assignment_id = $date_time = $shift_length = ''; // Default empty values

// Database connection
$conn = new mysqli('localhost', 'root', '', 'hospitaldb');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch assignment types
$sql = "SELECT id, assignment_name FROM assignmenttypes";
$result = $conn->query($sql);

$assignments = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $assignments[] = $row;
    }
}

$sql = "SELECT sp.user_id, 
        CONCAT(u.first_name, ' ', u.last_name) as full_name, 
        sp.staff_profile_type 
        FROM staffprofiles sp 
        JOIN users u ON sp.user_id = u.id 
        WHERE u.user_type = 'staff'";
$staffResult = $conn->query($sql);

$staffMembers = [];
if ($staffResult->num_rows > 0) {
    while ($row = $staffResult->fetch_assoc()) {
        $staffMembers[] = $row;
    }
}

// Check if staffassignment_id is set in the URL for initial loading
if (isset($_GET['id'])) {
    $staffassignment_id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
    $staffassignment = $controller->viewStaffAssignment($staffassignment_id);

    if ($staffassignment) {
        $staff_id = $staffassignment['staff_id'];
        $assignment_id = $staffassignment['assignment_id'];
        
        // Convert the database datetime to the correct display format
        try {
            $datetime_obj = new DateTime($staffassignment['date_time']);
            $date_time = $datetime_obj->format('Y-m-d h:i A');
        } catch (Exception $e) {
            $date_time = '';
            $error_message = "Error formatting date";
        }
        
        $shift_length = $staffassignment['shift_length'];
    } else {
        $error_message = "Staff Assignment not found.";
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process the form submission
    $staffassignment_id = filter_var($_POST['staffassignment_id'], FILTER_SANITIZE_NUMBER_INT);
    $staff_id = filter_var($_POST['staff_id'], FILTER_SANITIZE_NUMBER_INT);
    $assignment_id = filter_var($_POST['assignment_id'], FILTER_SANITIZE_NUMBER_INT);
    $date_time = trim($_POST['date_time']);
    
    // Convert the submitted time to database format
    try {
        $datetime_obj = DateTime::createFromFormat('Y-m-d h:i A', $date_time);
        if ($datetime_obj === false) {
            throw new Exception('Invalid date format');
        }
        $date_time = $datetime_obj->format('Y-m-d H:i:s');
    } catch (Exception $e) {
        $error_message = "Invalid date/time format";
    }
    
    $shift_length = filter_var($_POST['shift_length'], FILTER_SANITIZE_NUMBER_INT);

    // Only proceed if there's no error_message
    if (empty($error_message)) {
        // Validate inputs
        if (empty($staff_id) || empty($assignment_id) || empty($date_time) || empty($shift_length)) {
            $error_message = "All fields are required.";
        } elseif (!is_numeric($staff_id) || !is_numeric($assignment_id) || !is_numeric($shift_length)) {
            $error_message = "Staff ID, Assignment ID, and Shift Length must be numeric.";
        } else {
            $result = $controller->updateStaffAssignment($staffassignment_id, $staff_id, $assignment_id, $date_time, $shift_length);
            if (strpos($result, 'Error:') === false) {
                header("Location: staffassignmentList.php");
                exit();
            } else {
                $error_message = $result;
            }
        }
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
    <title>Clinic | Edit Staff Assignment</title>
    <link rel="icon" href="images/icon.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="editStyle.css">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="text-center mb-3">
            <a href="addStaffAssignment.php" class="btn btn-primary">Add New Staff Assignment</a>
            <a href='staffassignmentList.php' class='btn btn-secondary'>Back to Staff Assignments</a>
        </div>

        <div class="card">
            <div class="card-body">
                <h2 class="card-title text-center mb-4">Update Staff Assignment</h2>

                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo htmlspecialchars($error_message); ?>
                    </div>
                <?php elseif (!empty($success_message)): ?>
                    <div class="alert alert-success" role="alert">
                        <?php echo htmlspecialchars($success_message); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="editStaffAssignment.php">
                    <input type="hidden" name="_token" value="<?php echo $_SESSION['_token']; ?>">
                    <input type="hidden" name="staffassignment_id" value="<?php echo htmlspecialchars($staffassignment_id); ?>">

                    <div class="mb-3">
                        <label for="staff_id" class="form-label">Staff Member:</label>
                        <select class="form-control" id="staff_id" name="staff_id" required>
                            <option value=""></option>
                            <?php foreach ($staffMembers as $staff): ?>
                                <option value="<?php echo htmlspecialchars($staff['user_id']); ?>" 
                                    <?php echo ($staff_id == $staff['user_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($staff['full_name'] . ' (' . $staff['staff_profile_type'] . ')'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="assignment_id" class="form-label">Assignment</label>
                        <select class="form-control" id="assignment_id" name="assignment_id" required>
                            <option value=""></option>
                            <?php foreach ($assignments as $assignment): ?>
                                <option value="<?php echo htmlspecialchars($assignment['id']); ?>" 
                                    <?php echo (isset($assignment_id) && $assignment_id == $assignment['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($assignment['assignment_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="date_time" class="form-label">Date:</label>
                        <input type="text" id="date_time" name="date_time" class="form-control" required
                            value="<?php echo htmlspecialchars($date_time); ?>">
                    </div>

                    <div class="mb-3">
                        <label for="shift_length" class="form-label">Shift Length:</label>
                        <select class="form-control" id="shift_length" name="shift_length" required>
                            <option value=""></option>
                            <option value="4" <?php echo ($shift_length == '4') ? 'selected' : ''; ?>>4 Hours</option>
                            <option value="6" <?php echo ($shift_length == '6') ? 'selected' : ''; ?>>6 Hours</option>
                            <option value="8" <?php echo ($shift_length == '8') ? 'selected' : ''; ?>>8 Hours</option>
                            <option value="10" <?php echo ($shift_length == '10') ? 'selected' : ''; ?>>10 Hours</option>
                            <option value="12" <?php echo ($shift_length == '12') ? 'selected' : ''; ?>>12 Hours</option>
                        </select>
                    </div>

                    <div class="text-center">
                        <input type="submit" class="btn btn-success" value="Update Staff Assignment">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
       document.addEventListener('DOMContentLoaded', function() {
    // Calculate date 1 week from today
    const today = new Date();
    const nextWeek = new Date(today.getTime() + 7 * 24 * 60 * 60 * 1000);
    nextWeek.setHours(8, 0, 0, 0); // Set time to 8:00 AM
    
    flatpickr("#date_time", {
        enableTime: true,
        dateFormat: "Y-m-d h:i K", // Format with AM/PM
        minDate: nextWeek,
        time_24hr: false,
        minTime: "00:00",
        maxTime: "23:59",
        minuteIncrement: 30,
        altFormat: "Y-m-d h:i K", // More readable format for display
        defaultHour: 8
    });
});
    </script>
</body>

</html>