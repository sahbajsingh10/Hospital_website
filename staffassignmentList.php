<?php
require_once 'StaffAssignmentController.php';

$controller = new StaffAssignmentController();
$staffassignments = $controller->listStaffAssignments();

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
    <title>Clinic | Staff Assignment List</title>
    <link rel="icon" href="images/icon.png" type="image/png">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">
    <link rel="stylesheet" href="bootstrap4.5.2.css">
    <link rel="stylesheet" href="hospital-style.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="table-styles.css">
    <style>
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
            <h1>Staff Assignment List</h1>
        </div>
        <div class="table-responsive mt-3">
            <table id="staffassignmentTable" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Staff ID</th>
                        <th>Assignment Name</th>
                        <th>Date</th>
                        <th>Shift Length</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($staffassignments as $staffassignment): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($staffassignment['id']); ?></td>
                        <td><?php echo htmlspecialchars($staffassignment['staff_id']); ?></td>
                        <td><?php echo htmlspecialchars($assignment_names[$staffassignment['assignment_id']]); ?></td>
                        <td>
                            <?php
                                // Convert date_time to 12-hour AM/PM format
                                $date = new DateTime($staffassignment['date_time']);
                                echo $date->format('Y-m-d h:i A'); // 12-hour AM/PM format
                            ?>
                        </td>
                        <td><?php echo htmlspecialchars($staffassignment['shift_length']); ?> Hours</td>
                        <td>
                            <a href="addStaffAssignment.php?id=<?php echo $staffassignment['id']; ?>" class="btn btn-success btn-sm">
                                <i class="fas fa-add"></i>
                            </a>
                            <a href="viewStaffAssignment.php?id=<?php echo $staffassignment['id']; ?>" class="btn btn-info btn-sm">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="editStaffAssignment.php?id=<?php echo $staffassignment['id']; ?>" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="deleteStaffAssignment.php?id=<?php echo $staffassignment['id']; ?>" class="btn btn-danger btn-sm">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
   
    <a href="addStaffAssignment.php" class="btn btn-primary add-appointment-btn">
        <i class="fas fa-plus"></i>
    </a>
    <!-- jQuery, Bootstrap JS, and DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script>
    $(document).ready(function() {
        $('#staffassignmentTable').DataTable({
            responsive: true,
            autoWidth: false,
            paging: true,
            searching: true
        });
    });
    </script>
</body><br><br><br><br>
<?php include "footer.php"; ?>
</html>
