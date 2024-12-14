<?php
require_once 'AssignmentTypeController.php';

$controller = new AssignmentTypeController();
$error_message = '';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input data
    $assignmenttype_id = htmlspecialchars(trim($_POST['assignmenttype_id']));
    $assignment_name = htmlspecialchars(trim($_POST['assignment_name']));

    // Check if assignment type already exists
    if ($controller->assignmentTypeExists($assignment_name)) {
        $error_message = "This assignment type already exists.";
    } else {
        // Attempt to add the assignmenttype
        if ($controller->addAssignmentType($assignment_name)) {
            echo "<div class='alert alert-success'>Assignment Type added successfully.</div>";
            header("Location: assignmenttypeList.php");
            die();
        } else {
            $error_message = "Failed to add assignmenttype. Please try again.";
        }
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
    <title>Clinic | Add Assignment Type</title>
    <link rel="icon" href="images/icon.png" type="image/png">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="addStyle.css">
</head>

<body class="bg-light">
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h2 class="mb-0">Add Assignment Type</h2>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($error_message)): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo htmlspecialchars($error_message); ?>
                            </div>
                        <?php endif; ?>

                        <form method="post" action="addAssignmentType.php">
                            <input type="hidden" name="_token" value="<?php echo $_SESSION['_token']; ?>">
                            <input type="hidden" name="assignmenttype_id" value="<?php echo isset($_GET['assignmenttype_id']) ? htmlspecialchars($_GET['assignmenttype_id']) : ''; ?>">

                            <div class="mb-3">
                                <label for="assignment_name" class="form-label">Assignment Type</label>
                                <input type="text" class="form-control" id="assignment_name" name="assignment_name" required value="<?php echo isset($assignment_name) ? htmlspecialchars($assignment_name) : ''; ?>">
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Add Assignment Type</button>
                        </form>
                    </div>
                </div>
                <div class="text-center mt-3">
                    <a href="assignmenttypeList.php" class="btn btn-secondary">Back to Assignment Types</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>