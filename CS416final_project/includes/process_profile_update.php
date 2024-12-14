<?php
include './conn.php';
$message = "";
// Check if $conn is defined and available
if (isset($conn)) {

    // Check if the request method is POST
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Basic validation for form inputs
        $email = !empty($_POST['email']) ? trim($_POST['email']) : null;
        $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null; // Hash the password
        $first_name = !empty($_POST['first_name']) ? trim($_POST['first_name']) : null;
        $last_name = !empty($_POST['last_name']) ? trim($_POST['last_name']) : null;
        $phone_number = !empty($_POST['phone_number']) ? trim($_POST['phone_number']) : null;
        $user_id = $_SESSION['user_id'];

        //Check if a file is uploaded and available then update user record for image.
        if (isset($_FILES['user_image']) && $_FILES['user_image']['error'] == 0) {
            $file = $_FILES['user_image']['tmp_name'];

            // Read file data into a variable
            $user_image = file_get_contents($file);

            try {
                // Prepare SQL and bind parameters
                $stmt = $conn->prepare("UPDATE Users SET user_image=:user_image WHERE id=:user_id");

                $stmt->bindParam(':user_image', $user_image, PDO::PARAM_LOB); // Storing as LOB (large object)
                $stmt->bindParam(':user_id', $user_id);

                $stmt->execute();

            } catch (PDOException $e) {
                $message =  "Error updating user image: " . $e->getMessage();
            }
        }

        if ($password) {
            try {
                // Prepare SQL and bind parameters
                $stmt = $conn->prepare("UPDATE Users SET user_password=:user_password WHERE id=:user_id");

                $stmt->bindParam(':user_password', $password);
                $stmt->bindParam(':user_id', $user_id);

                $stmt->execute();

            } catch (PDOException $e) {
                $message =  "Error updating user password: " . $e->getMessage();
            }
        }

        try {
            // Prepare SQL and bind parameters
            $stmt = $conn->prepare("UPDATE Users SET email=:email, first_name=:first_name, last_name=:last_name, phone=:phone WHERE id=:user_id");


            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':first_name', $first_name);
            $stmt->bindParam(':last_name', $last_name);
            $stmt->bindParam(':phone', $phone_number);
            $stmt->bindParam(':user_id', $user_id);

            $stmt->execute();

            $message = "User updated successfully";
        } catch (PDOException $e) {
            $message =  "Error updating user: " . $e->getMessage();
        }

        //Update Session Variables with updated DB data
        try {

            // Prepare the SQL query to fetch the user by email
            $sql = "SELECT id, email, first_name, last_name, user_password, phone, user_type, user_image FROM users WHERE id = :user_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_STR);
            $stmt->execute();

            // Check if a user with the provided id exists
            if ($stmt->rowCount() > 0) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                $_SESSION['email'] = $user['email'];
                $_SESSION['phone'] = $user['phone'];
                $_SESSION['first_name'] = $user['first_name'];
                $_SESSION['last_name'] = $user['last_name'];
                $_SESSION['user_type'] = $user['user_type'];
                $_SESSION['user_image'] = $user['user_image'] ? 'data:image/jpeg;base64,' . base64_encode($user['user_image']) : '';
            } else {
                // No user found with that id
                $error = "Error Updating User, please try again.";
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }

        //Close the connection
        $conn = null;
    } else {
        $message = "Error: Submit failed.";
    }
} else {
    $message =  "Error: Database connection is not available.";
}


// Redirect with a message
$_SESSION['user_update_message'] = $message;
echo $messsage;
header('Location: ../manage_profile.php?type=Update User&message=' . $message);  // Use urlencode to encode the message for safe URL passing
exit();  // Always use exit after a redirect to stop further execution