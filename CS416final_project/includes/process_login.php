<?php
session_start();
include './conn.php';

$error = "";

// Check if the form is submitted with the POST method
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve the email and password from the form submission
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Check if $conn is defined and available
    if (isset($conn)) {

        try {

            // Prepare the SQL query to fetch the user by email
            $sql = "SELECT id, email, first_name, last_name, user_password, phone, user_type, user_image FROM users WHERE email = :email";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();

            // Check if a user with the provided email exists
            if ($stmt->rowCount() > 0) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                // Verify the password
                if (password_verify($password, $user['user_password'])) {
                    //Password is correct, set up the session
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['phone'] = $user['phone'];
                    $_SESSION['first_name'] = $user['first_name'];
                    $_SESSION['last_name'] = $user['last_name'];
                    $_SESSION['user_type'] = $user['user_type'];
                    $_SESSION['user_image'] = $user['user_image'] ? 'data:image/jpeg;base64,' . base64_encode($user['user_image']) : '';

                    // check user type redirect to appropriate portal.
                    switch ($_SESSION['user_type']) {
                        case 'admin':
                            header("location: ../admin_portal.php");
                            exit();
                            break;
                        case "staff":
                            header("location: ../staff_portal.php");
                            exit();
                            break;
                        case "patient":
                            header("location: ../patient_portal.php");
                            exit();
                            break;
                    }
                } else {
                    // Password is incorrect
                    $error = "Invalid email or password.";
                }
            } else {
                // No user found with that email
                $error = "Invalid email or password.";
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    } else {
        $error = "Database connection unavailable.";
    }

    // If there's an error, store it in the session and redirect to the login page
    if (!empty($error)) {
        $_SESSION['login_error'] = $error;
        header('Location: ../login.php?type=Login&message='. $error);  // Use urlencode to encode the message for safe URL passing
        exit();
    }
} else {
    // If not a POST request, display an error message
    $error = "Invalid request method.";
    $_SESSION['login_error'] = $error;
    header('Location: ../login.php?type=Login&message='. $error);
    exit();
}
