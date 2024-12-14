<?php
require "./includes/conn.php";
$user_id = $_SESSION['user_id'];
$email = $_SESSION['email'];
$phone = $_SESSION['phone'];
$first_name = $_SESSION['first_name'];
$last_name = $_SESSION['last_name'];
$user_image = $_SESSION['user_image']; // Change this line to use session instead of $_FILES
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clinic | Manage Profile</title><link rel="icon" href="images/icon.png" type="image/png">
    <script src="https://kit.fontawesome.com/7e01d2ce2c.js" crossorigin="anonymous"></script>
    <script src="script.js" defer></script>
    <link rel="stylesheet" href="bootstrap4.5.2.css">
    <link rel="stylesheet" href="hospital-style.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css">
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
        }

        .content {
            flex: 1;
            padding: 2rem;
        }

        @media (max-width: 767px) {
            .content {
                padding: 1rem;
                margin-top: 8vh !important;
            }

            .form-control {
                font-size: 1rem;
                width: 100%;
            }

            button[type="submit"] {
                font-size: 1rem;
            }

        }

        @media (min-width: 768px) {
            .form-control {
                font-size: 1.1rem;
            }

            button[type="submit"] {
                font-size: 1.1rem;
            }

        }

        #profile_update_message {
            text-align: center;
        }

        #manage_profile_image_thumbnail {
            width: 75px !important;
            height: 75px !important;
            object-fit: cover;


        }

        #user_image {
            line-height: 1.2 !important;
        }
    </style>
</head>

<body>
    <?php include "header.php" ?>

    <section class="content">
        <form id="register_form" action="./includes/process_profile_update.php" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>

            <input type="hidden" name="_token" value="<?php echo $_SESSION['_token']; ?>">

            <!-- Email -->

            <div class="mb-3">
                <label for="email" class="form-label"><i class="fas fa-envelope"></i> Email address</label>
                <input type="email" class="form-control" id="email" name="email" value=<?php echo $email ?>
                    pattern="^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$" required>
                <div class="invalid-feedback">
                    Please provide a valid email address.
                </div>
            </div>

            <!-- Password -->

            <div class="mb-3">
                <label for="password" class="form-label"><i class="fas fa-lock"></i> Password</label>
                <input type="password" class="form-control" id="password" name="password" value="<?php echo $password ?>"
                    pattern="^(?=.*[A-Za-z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,}$">
                <div class="invalid-feedback">
                    Password must be at least 8 characters long, contain at least one letter, one number, and one
                    special character (!@#$%^&*).
                </div>
            </div>

            <!-- First Name -->

            <div class="mb-3">
                <label for="first_name" class="form-label"><i class="fas fa-id-card"></i> First Name</label>
                <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo $first_name ?>" required>
                <div class="invalid-feedback">
                    Please provide your first name.
                </div>
            </div>

            <!-- Last Name -->

            <div class="mb-3">
                <label for="last_name" class="form-label"><i class="fas fa-id-card"></i> Last Name</label>
                <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo $last_name ?>" required>
                <div class="invalid-feedback">
                    Please provide your last name.
                </div>
            </div>

            <!-- Phone Number -->

            <div class="mb-3">
                <label for="phone_number" class="form-label"><i class="fas fa-phone"></i> Phone Number</label>
                <input type="tel" class="form-control" id="phone_number" name="phone_number" value="<?php echo $phone ?>" pattern="^\d{10}$" required>
                <div class="invalid-feedback">
                    Please enter a 10-digit phone number.
                </div>
            </div>

            <!-- File Upload -->

            <div class="mb-3">
                <label for="user_image" class="form-label"><i class="fas fa-file-upload"></i> Upload File</label>
                <input type="file" class="form-control" id="user_image" name="user_image" accept=".jpg,.jpeg,.png,.pdf">
                <div class="invalid-feedback">
                    Please upload a valid file (jpg, jpeg, png, pdf).
                </div>
            </div>
            <img id="manage_profile_image_thumbnail" src="<?php echo htmlspecialchars($user_image); ?>" alt="User Image" class="img-fluid rounded">

            <div class="mb-3" id="profile_update_message">
                <?php if (isset($_GET['message'])) {
                    include './includes/main_show_message.php';
                } ?>
            </div>

            <button type="submit">Update</button>
        </form>
    </section>
    <br><br><br><br>
    <?php include "footer.php"; ?>
    <!-- jQuery, Bootstrap JS, DataTables JS, and Lightbox2 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script>
        // Bootstrap form validation
        (function() {
            'use strict';
            const forms = document.querySelectorAll('.needs-validation');

            Array.prototype.slice.call(forms)
                .forEach(function(form) {
                    form.addEventListener('submit', function(event) {
                        if (!form.checkValidity()) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
        })();

        $(document).ready(function() {
            $('#userTable').DataTable({
                responsive: true,
                autoWidth: false,
                paging: true,
                searching: true
            });

            // Lightbox settings
            lightbox.option({
                'resizeDuration': 200,
                'wrapAround': true,
                'alwaysShowNavOnTouchDevices': true,
                'imageFadeDuration': 300,
                'fitImagesInViewport': true,
                'disableScrolling': true,
                'positionFromTop': 285,
                'fadeDuration': 200,
                'showImageNumberLabel': false,
            });
        });
    </script>
</body>

</html>