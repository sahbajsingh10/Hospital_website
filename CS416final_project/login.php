<?php require "./includes/conn.php";

//redirect if user is currently logged in.
if (isset($_SESSION['user_id'])) {
    header('Location: home.php');
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clinic | Login</title>
    <link rel="icon" href="images/icon.png" type="image/png">
    
    <script src="https://kit.fontawesome.com/7e01d2ce2c.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="bootstrap4.5.2.css">
    <link rel="stylesheet" href="hospital-style.css">
    <link rel="stylesheet" href="style.css">
    <script src="script.js" defer></script>
    <style>
        .register a{
            text-decoration:none;
        }
        body{
            margin-top:4.25vh !important;
        }
        </style>
</head>
<?php include "header.php"; ?>
<body>
  
    <section class="content">
        <!-- Add the logout message here -->
        <?php if (isset($_SESSION['logout_message'])): ?>
            <div class="alert alert-info">
                <?php 
                echo htmlspecialchars($_SESSION['logout_message']);
                unset($_SESSION['logout_message']); 
                ?>
            </div>
        <?php endif; ?>

        <form id="login_form" action="./includes/process_login.php" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>

            <input type="hidden" name="_token" value="<?php echo $_SESSION['_token']; ?>">

            <div class="mb-3">
                <label for="email" class="form-label"><i class="fas fa-envelope"></i> Email Address</label>
                <input type="email" class="form-control" id="email" name="email" required>
                <div class="invalid-feedback">
                    Enter Email Address
                </div>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label"><i class="fas fa-lock"></i> Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
                <div class="invalid-feedback">
                    Enter Password
                </div>
            </div>

            <div class="mb-3" id="login_error">
                <?php if (isset($_GET['message'])) {
                    include './includes/main_show_message.php';
                } ?>
            </div>

            <button type="submit">Login</button>
        </form>
        <p>
        <div class="register">
            <p>Don't have an account? <a href="register.php">Register</a></p>
        </div>
        </p>
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

        var url_params = url.searchParams.getAll();

        $(function() {
            if (url_params.length == 0) {
                $('#form_comparison').hide();
            }
        });
    </script>
</body>
<?php include "footer.php"; ?>
</html>