<?php
require "./includes/conn.php";
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
    <title>Clinic | Admin Portal</title>
    <link rel="icon" href="images/icon.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="bootstrap4.5.2.css">
    <link rel="stylesheet" href="hospital-style.css">
    <link rel="stylesheet" href="style.css">
    <style>
@media (max-width: 767px) {
    .content {
        flex: 1;
        display: flex;
        flex-direction: column;
        overflow-y: hidden !important;
        margin-top:8vh !important;
    }

    .admin-dashboard {
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .dashboard-header {
        flex-shrink: 0;
    }

    .dashboard-cards {
        flex-grow: 1;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        grid-gap: 1rem;
        overflow-y: auto;
    }

    .dashboard-card {
        display: flex;
        flex-direction: column;
        height: 100%;
    }

   
   .content {
    min-height: calc(167vh - 60px); /* Adjust the value based on the height of your header and footer */
    display: flex;
    flex-direction: column;
}

.admin-dashboard {
    flex: 1;
    display: flex;
    flex-direction: column;
}
}


</style>
</head>

<body>
    <?php include "header.php" ?>

    <section class="content admin-dashboard">
        <div class="container">
            <div class="dashboard-header">
                <h2>Admin Portal</h2>
                <p>Manage hospital system settings and user access</p>
            </div>
            <div class="row dashboard-cards">
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="dashboard-card">
                        <div class="card-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3>User Management</h3>
                        <p>Manage user accounts and access controls</p>
                        <a href="userList.php" class="btn btn-primary">
                            <span>Access Users</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="dashboard-card">
                        <div class="card-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <h3>Appointment Types</h3>
                        <p>Configure available appointment categories</p>
                        <a href="appointmenttypeList.php" class="btn btn-primary">
                            <span>Manage Types</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="dashboard-card">
                        <div class="card-icon">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <h3>Assignment Types</h3>
                        <p>Define staff assignment categories</p>
                        <a href="assignmenttypeList.php" class="btn btn-primary">
                            <span>Configure</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="dashboard-card">
                        <div class="card-icon">
                            <i class="fas fa-user-md"></i>
                        </div>
                        <h3>Staff Types</h3>
                        <p>Manage staff roles and permissions</p>
                        <a href="stafftypeList.php" class="btn btn-primary">
                            <span>View Roles</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>
<?php include "footer.php"; ?>
</html>