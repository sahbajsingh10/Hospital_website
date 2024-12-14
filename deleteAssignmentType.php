<?php
require_once 'AssignmentTypeController.php';

$error_message = '';
$success_message = '';
$assignment_name = '';
$assignmenttype_id = null;
$assignmenttype = null; // Initialize the variable

$controller = new AssignmentTypeController();

if (isset($_GET['id'])) {
    $assignmenttype_id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
    $assignmenttype = $controller->viewAssignmentType($assignmenttype_id);

    if ($assignmenttype) {
        $assignment_name = $assignmenttype['assignment_name'];
    } else {
        $error_message = "Assignment Type not found.";
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $assignmenttype_id = filter_var($_POST['assignmenttype_id'], FILTER_SANITIZE_NUMBER_INT);
    
    // Get the assignment type details before attempting to delete
    $assignmenttype = $controller->viewAssignmentType($assignmenttype_id);
    
    $result = $controller->deleteAssignmentType($assignmenttype_id);
    if ($result === true) {
        $success_message = "Assignment Type deleted successfully!";
        header("Location: assignmenttypeList.php");
        exit();
    } else {
        $error_message = $result; // This will show the "in use" error message
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
    <title>Clinic | Delete Assignment Type</title>
    <link rel="icon" href="images/icon.png" type="image/png">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="deleteStyle.css">
</head>

<body class="bg-light">
    <div class="container my-5">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h1 class="card-title mb-0">Delete This Assignment Type</h1>
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

                <?php if ($assignmenttype): ?>
                    <p><strong>ID:</strong> <?php echo htmlspecialchars($assignmenttype['id']); ?></p>
                    <p><strong>Assignment Type:</strong> <?php echo htmlspecialchars($assignmenttype['assignment_name']); ?></p>
                
                    <div class="card-footer text-end">
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="fas fa-trash-alt"></i> Delete
                        </button>
                        <a href="assignmenttypeList.php" class="btn btn-secondary">Back to Assignment Types</a>
                    </div>
                <?php else: ?>
                    <div class="text-center">
                        <a href="assignmenttypeList.php" class="btn btn-secondary">Back to Assignment Types</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <?php if ($assignmenttype): ?>
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this assignment type?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form method="POST" action="deleteAssignmentType.php" style="display: inline;">
                        <input type="hidden" name="_token" value="<?php echo $_SESSION['_token']; ?>">
                        <input type="hidden" name="assignmenttype_id" value="<?php echo htmlspecialchars($assignmenttype_id); ?>">
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>

</html>