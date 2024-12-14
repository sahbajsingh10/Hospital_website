<?php
include './conn.php';
require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
require_once '../StaffProfileController.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$staffProfileController = new StaffProfileController;
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
        $user_type = !empty($_POST['user_type']) ? trim($_POST['user_type']) : null;
        $staff_type = !empty($_POST['staff_type']) ? trim($_POST['staff_type']) : null;


        //Check if a file is uploaded and available
        if (isset($_FILES['user_image']) && $_FILES['user_image']['error'] == 0) {
            $file = $_FILES['user_image']['tmp_name'];

            // Read file data into a variable
            $user_image = file_get_contents($file);

            try {
                // Prepare SQL and bind parameters
                $stmt = $conn->prepare("INSERT INTO Users (email, first_name, last_name, user_password, user_type, phone, user_image) 
                VALUES (:email, :first_name, :last_name, :user_password, :user_type, :phone, :user_image)");

                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':first_name', $first_name);
                $stmt->bindParam(':last_name', $last_name);
                $stmt->bindParam(':user_password', $password);
                $stmt->bindParam(':phone', $phone_number);
                $stmt->bindParam(':user_type', $user_type);
                $stmt->bindParam(':user_image', $user_image, PDO::PARAM_LOB); // Storing as LOB (large object)

                $stmt->execute();

                $user_id = $conn->lastInsertId();

                if ($user_type === 'staff') {
                    try {
                        // Add selected Staff profile type to new staff profile that gets created by db trigger when new user is inserterd.
                        $staffProfileController->updateStaffProfileTypeByUserID($user_id, $staff_type);
                    } catch (PDOException $e) {
                        $message = "Error Creating User, please see your Admin.";
                    }
                }

                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'CS41602FinalProject@gmail.com';
                    $mail->Password = 'yphtuflnrnshtmlc';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->addEmbeddedImage('../images/icon.png', 'icon');
                    $mail->Port = 587;
                    $mail->isHTML(true);
                    $mail->setFrom('CS41602FinalProject@gmail.com', 'Final Project');
                    $mail->addAddress($email);
                    $mail->Subject = 'Welcome to Evergreen Health Clinic';
                    $mail->Body = '<h1 style="color:#2e8b57;"><img src="cid:icon" style="width:100px;height:100px">Welcome to Evergreen Health Clinic</h1><p style="color:black">Hello <strong>' . $first_name . ' ' . $last_name . '</strong>,<br> Your user account has been created and you may now log in to access services.</p>';
                    $mail->send();
                    $message = 'New user created successfully, please check your Email for welcome message.';
                } catch (Exception $e) {
                    $message = "New user created successfully, Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
                }
            } catch (PDOException $e) {
                $message =  "Error creating user: " . $e->getMessage();
            }
        } else {
            $message =  "Error: Please upload a valid file.";
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
$_SESSION['registration_message'] = $message;
header('Location: ../show_message.php?type=Registration&message=' . $message);  // Use urlencode to encode the message for safe URL passing
exit();  // Always use exit after a redirect to stop further execution