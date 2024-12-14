<?php
require "./includes/conn.php";
$user_type = $_SESSION['user_type'];

if (!isset($user_type) || $user_type != 'patient') {
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
    <title>Clinic | Patient Portal</title>
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

    .patient-dashboard {
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

.patient-dashboard {
    flex: 1;
    display: flex;
    flex-direction: column;
}
}


</style>
</head>

<body>
    <?php include "header.php" ?>

    <section class="content patient-dashboard">
        <div class="container">
            <div class="dashboard-header">
                <h2>Patient Portal</h2>
                <p>Access your healthcare services and appointments</p>
            </div>

            <div class="row dashboard-cards">
                <!-- Appointments Card -->
                <div class="col-lg-6 mb-4">
                    <div class="dashboard-card">
                        <div class="card-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <h3>My Appointments</h3>
                        <p>View your upcoming appointments and schedule new visits with our healthcare providers</p>
                        <a href="patientappointmentList.php" class="btn btn-primary">
                            <span>Manage Appointments</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Quick Info Card -->
                <div class="col-lg-6 mb-4">
                    <div class="dashboard-card">
                        <div class="card-icon">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <h3>Need Help?</h3>
                        <p>For emergency services, please call 911. For general inquiries, contact our front desk at (555) 123-4567</p>
                        <a href="contact.php" class="btn btn-primary">
                            <span>Contact Us</span>
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