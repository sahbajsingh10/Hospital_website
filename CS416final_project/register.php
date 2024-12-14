<?php
require "./includes/conn.php";
require_once 'StaffTypeController.php';

$controller = new StaffTypeController();
$staffTypes = $controller->listStaffTypes();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clinic | Register</title>
    <link rel="icon" href="images/icon.png" type="image/png">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/7e01d2ce2c.js" crossorigin="anonymous"></script>
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

        #manage_profile_image_thumbnail {
            width: 75px !important;
            height: 75px !important;
            object-fit: cover;
        }

        #staff_type {
            display: none;
        }

        #user_image {
            line-height: 1.2 !important;
        }
    </style>
</head>

<body>
    <?php include "header.php" ?>

    <section class="content">
        <form id="register_form" action="./includes/process_registration.php" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>

            <!--Session Token -->
            <input type="hidden" name="_token" value="<?php echo $_SESSION['_token']; ?>">

            <!-- Email -->

            <div class="mb-3">
                <label for="email" class="form-label"><i class="fas fa-envelope"></i> Email address</label>
                <input type="email" class="form-control" id="email" name="email"
                    pattern="^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$" required>
                <div class="invalid-feedback">
                    Please provide a valid email address.
                </div>
            </div>


            <!-- Password -->


            <div class="mb-3">
                <label for="password" class="form-label"><i class="fas fa-lock"></i> Password</label>
                <input type="password" class="form-control" id="password" name="password"
                    pattern="^(?=.*[A-Za-z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,}$" required>
                <div class="invalid-feedback">
                    Password must be at least 8 characters long, contain at least one letter, one number, and one
                    special character (!@#$%^&*).
                </div>
            </div>

            <!-- First Name -->

            <div class="mb-3">
                <label for="first_name" class="form-label"><i class="fas fa-id-card"></i> First Name</label>
                <input type="text" class="form-control" id="first_name" name="first_name" required>
                <div class="invalid-feedback">
                    Please provide your first name.
                </div>
            </div>

            <!-- Last Name -->

            <div class="mb-3">
                <label for="last_name" class="form-label"><i class="fas fa-id-card"></i> Last Name</label>
                <input type="text" class="form-control" id="last_name" name="last_name" required>
                <div class="invalid-feedback">
                    Please provide your last name.
                </div>
            </div>

            <!-- Phone Number -->

            <div class="mb-3">
                <label for="phone_number" class="form-label"><i class="fas fa-phone"></i> Phone Number</label>
                <input type="tel" class="form-control" id="phone_number" name="phone_number" pattern="^\d{10}$" required>
                <div class="invalid-feedback">
                    Please enter a 10-digit phone number.
                </div>
            </div>

            <!-- File Upload -->
            <div class="mb-3">
                <label for="user_image" class="form-label"><i class="fas fa-file-upload"></i> Upload File</label>
                <input type="file" class="form-control" id="user_image" name="user_image" accept=".jpg,.jpeg,.png,.pdf" required>
                <div class="invalid-feedback">
                    Please upload a valid file (jpg, jpeg, png, pdf).
                </div>
            </div>


            <!-- User Type -->

            <div class="mb-3" id="user_type">
                <label for="user_type" class="form-label"><i class="fas fa-user"></i> Select User Type</label>
                <select id="user_type_select" name="user_type" class="form-control" onchange="showFormOption('staff_type', 'user_type_select', 'staff')">
                    <option value="admin">Admin</option>
                    <option value="staff">Staff</option>
                    <option value="patient">Patient</option>
                </select>
            </div>

            <!-- Staff Type - hidden unless Staff is chosen as User Type -->
            <div class="mb-3" id="staff_type">
                <label for="staff_type" class="form-label"><i class="fas fa-user"></i> Select Staff Type:</label>
                <select class="form-control" id="staff_type_select" name="staff_type" required>
                    <option value=""></option>
                    <?php foreach ($staffTypes as $staffType): ?>
                        <option value="<?php echo htmlspecialchars($staffType['staff_type']); ?>" <?php echo ($staffType['staff_type']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($staffType['staff_type']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>


            <button type="submit">Register</button>
        </form>
    </section>



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
    </script>
</body>
<br><br><br><br>
<?php include "footer.php"; ?>

</html>