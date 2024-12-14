<?php
require_once 'StaffAssignmentController.php';

$controller = new StaffAssignmentController();
$error_message = '';

// Database connection
$conn = new mysqli('localhost', 'root', '', 'hospitaldb');

// Fetch staff members with their types
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
$conn->close();

// Check if the form is submitted

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input data
    $staff_id = htmlspecialchars(trim($_POST['staff_id']));
    $assignment_id = htmlspecialchars(trim($_POST['assignment_id']));
    $date_time = htmlspecialchars(trim($_POST['date_time']));
    
    // Convert 12-hour format to 24-hour format
    $datetime_obj = DateTime::createFromFormat('Y-m-d h:i A', $date_time);
    if ($datetime_obj === false) {
        $error_message = "Invalid date/time format";
    } else {
        $date_time = $datetime_obj->format('Y-m-d H:i:s');
    }
    
    $shift_length = htmlspecialchars(trim($_POST['shift_length']));

    if (empty($staff_id) || empty($assignment_id) || empty($date_time) || empty($shift_length)) {
        $error_message = "All fields are required.";
    } elseif (!is_numeric($staff_id) || !is_numeric($assignment_id) || !is_numeric($shift_length)) {
        $error_message = "Staff ID, Assignment ID, and Shift Length must be numeric.";
    } else {
        // Check if ID exists in staffprofiles
        $conn = new mysqli('localhost', 'root', '', 'hospitaldb');
        
        // First check if this is a valid staff member
        $stmt = $conn->prepare("SELECT sp.user_id 
                              FROM staffprofiles sp 
                              JOIN users u ON sp.user_id = u.id 
                              WHERE sp.user_id = ? AND u.user_type = 'staff'");
        $stmt->bind_param("i", $staff_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            $error_message = "Error: Invalid Staff ID. Please enter a valid staff member ID.";
        } else {
            // Attempt to add the staff assignment
            $result = $controller->addStaffAssignment($staff_id, $assignment_id, $date_time, $shift_length);
            if (strpos($result, 'Error:') === false) {
                echo "<div class='alert alert-success'>Staff Assignment added successfully.</div>";
                header("Location: staffassignmentList.php");
                exit();
            } else {
                $error_message = $result;
            }
        }
        $stmt->close();
        $conn->close();
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
    <title>Clinic | Add Staff Assignment</title>
    <link rel="icon" href="images/icon.png" type="image/png">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="addStyle.css">
</head>

<body class="bg-light">
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h2 class="mb-0">Add Staff Assignment</h2>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($error_message)): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo htmlspecialchars($error_message); ?>
                            </div>
                        <?php endif; ?>

                        <form method="post" action="addStaffAssignment.php">

                            <input type="hidden" name="_token" value="<?php echo $_SESSION['_token']; ?>">

                            <div class="mb-3">
    <label for="staff_id" class="form-label">Staff Member</label>
    <select class="form-control" id="staff_id" name="staff_id" required>
        <option value=""></option>
        <?php foreach ($staffMembers as $staff): ?>
            <option value="<?php echo htmlspecialchars($staff['user_id']); ?>" 
                <?php echo (isset($staff_id) && $staff_id == $staff['user_id']) ? 'selected' : ''; ?>>
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
                                        <option value="<?php echo htmlspecialchars($assignment['id']); ?>" <?php echo (isset($assignment_id) && $assignment_id == $assignment['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($assignment['assignment_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="date_time" class="form-label">Date</label>
                                <input type="text" class="form-control" id="date_time" name="date_time" required
                                    value="<?php echo isset($date_time) ? htmlspecialchars($date_time) : ''; ?>">
                            </div>

                            <div class="mb-3">
    <label for="shift_length" class="form-label">Shift Length</label>
    <select class="form-control" id="shift_length" name="shift_length" required>
        <option value=""></option>
        <option value="4" <?php echo (isset($shift_length) && $shift_length == '4') ? 'selected' : ''; ?>>4 Hours</option>
        <option value="6" <?php echo (isset($shift_length) && $shift_length == '6') ? 'selected' : ''; ?>>6 Hours</option>
        <option value="8" <?php echo (isset($shift_length) && $shift_length == '8') ? 'selected' : ''; ?>>8 Hours</option>
        <option value="10" <?php echo (isset($shift_length) && $shift_length == '10') ? 'selected' : ''; ?>>10 Hours</option>
        <option value="12" <?php echo (isset($shift_length) && $shift_length == '12') ? 'selected' : ''; ?>>12 Hours</option>
    </select>
</div>

                            <button type="submit" class="btn btn-primary w-100">Add Staff Assignment</button>
                        </form>
                    </div>
                </div>
                <div class="text-center mt-3">
                    <a href="staffassignmentList.php" class="btn btn-secondary">Back to Staff Assignments</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
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