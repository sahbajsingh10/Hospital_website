<?php
require "./includes/conn.php";
$user_type = $_SESSION['user_type'];

if (!isset($user_type) || $user_type != 'staff') {
    header('Location: home.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clinic | Staff Portal</title>
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

    .staff-dashboard {
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

.staff-dashboard {
    flex: 1;
    display: flex;
    flex-direction: column;
}
}


</style>
</head>

<body>
    <?php include "header.php" ?>

    <section class="content staff-dashboard">
        <div class="container">
            <div class="dashboard-header">
                <h2>Staff Portal</h2>
                <p>Manage hospital resources and patient care</p>
            </div>

            <div class="row dashboard-cards">
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="dashboard-card">
                        <div class="card-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <h3>Appointments</h3>
                        <p>View and manage patient appointments</p>
                        <a href="appointmentList.php" class="btn btn-primary">
                            <span>Access Appointments</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="dashboard-card">
                        <div class="card-icon">
                            <i class="fas fa-user-injured"></i>
                        </div>
                        <h3>Patient Profiles</h3>
                        <p>Access patient records and information</p>
                        <a href="patientprofileList.php" class="btn btn-primary">
                            <span>View Profiles</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="dashboard-card">
                        <div class="card-icon">
                            <i class="fas fa-user-md"></i>
                        </div>
                        <h3>Staff Profiles</h3>
                        <p>View staff information and schedules</p>
                        <a href="staffprofileList.php" class="btn btn-primary">
                            <span>Staff Directory</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="dashboard-card">
                        <div class="card-icon">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <h3>Assignments</h3>
                        <p>Manage staff duties and assignments</p>
                        <a href="staffassignmentList.php" class="btn btn-primary">
                            <span>View Assignments</span>
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