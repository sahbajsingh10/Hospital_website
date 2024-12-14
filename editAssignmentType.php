<?php
require_once 'AssignmentTypeController.php';

$controller = new AssignmentTypeController();
$error_message = '';
$success_message = '';
$assignment_name = ''; // Default empty values

// Check if assignmenttype_id is set in the URL for initial loading
if (isset($_GET['id'])) {
    $assignmenttype_id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
    $assignmenttype = $controller->viewAssignmentType($assignmenttype_id);

    if ($assignmenttype) {
        // Populate variables with assignmenttype data
        $assignment_name = $assignmenttype['assignment_name'];
    } else {
        $error_message = "Assignment Name not found.";
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process the form submission to update assignmenttype
    $assignmenttype_id = filter_var($_POST['assignmenttype_id'], FILTER_SANITIZE_NUMBER_INT);
    $assignment_name = filter_var($_POST['assignment_name'], FILTER_SANITIZE_STRING);

    $result = $controller->updateAssignmentType($assignmenttype_id, $assignment_name);
    if ($result === true) {
        $success_message = "Assignment Name updated successfully!";
        header("Location: assignmenttypeList.php");
        exit();
    } else {
        $error_message = $result; // This will show the "duplicate name" error message
    }
}

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
    <title>Clinic | Edit Assignment Type</title>
    <link rel="icon" href="images/icon.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="editStyle.css">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="text-center mb-3">
            <a href="addAssignmentType.php" class="btn btn-primary">Add New Assignment Type</a>
            <a href='assignmenttypeList.php' class='btn btn-secondary'>Back to Assignment Types</a>
        </div>

        <div class="card">
            <div class="card-body">
                <h2 class="card-title text-center mb-4">Update Assignment Type</h2>

                <?php if (!empty($error_message)): ?>
                    <p class="text-danger text-center"><?php echo $error_message; ?></p>
                <?php elseif (!empty($success_message)): ?>
                    <p class="text-success text-center"><?php echo $success_message; ?></p>
                <?php endif; ?>

                <form method="POST" action="editAssignmentType.php">
                    <input type="hidden" name="_token" value="<?php echo $_SESSION['_token']; ?>">
                    <input type="hidden" name="assignmenttype_id" value="<?php echo htmlspecialchars($assignmenttype_id); ?>">

                    <div class="mb-3">
                        <label for="assignment_name" class="form-label">Assignment Type:</label>
                        <input type="text" id="assignment_name" name="assignment_name" class="form-control" required
                            value="<?php echo htmlspecialchars($assignment_name); ?>">
                    </div>

                    <div class="text-center">
                        <input type="submit" class="btn btn-success" value="Update Assignment Type">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>