<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clinic | Home</title><link rel="icon" href="images/icon.png" type="image/png">
    <link rel="stylesheet" href="bootstrap4.5.2.css">
    <link rel="stylesheet" href="hospital-style.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            margin-top: 10vh !important;
           
        }
        .cta {
            padding: 3rem 0;
            margin-bottom: 0; /* Remove bottom margin */
            padding-top:65px;
            padding-bottom:65px;
        }
        footer {
            margin-top: 0; /* Remove top margin from footer */
        }
        .cta{
            margin-top:7vh;
        }
    </style>
</head>
<body>
    <?php include "header.php"; ?>

    <main>
        <!-- Hero Section with Carousel -->
        <div id="heroCarousel" class="carousel slide" data-ride="carousel">
            <ol class="carousel-indicators">
                <li data-target="#heroCarousel" data-slide-to="0" class="active"></li>
                <li data-target="#heroCarousel" data-slide-to="1"></li>
                <li data-target="#heroCarousel" data-slide-to="2"></li>
                <li data-target="#heroCarousel" data-slide-to="3"></li>
                <li data-target="#heroCarousel" data-slide-to="4"></li>
            </ol>
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="images/hospital-1.jpg" class="d-block w-100" alt="Hospital Front">
                    <div class="carousel-caption">
                        <h2>Welcome to Evergreen Health Clinic</h2>
                        <p>Providing Quality Healthcare for Our Community</p>
                    </div>
                </div>
                <div class="carousel-item">
                    <img src="images/hospital-2.jpg" class="d-block w-100" alt="Medical Staff">
                    <div class="carousel-caption">
                        <h2>Expert Medical Team</h2>
                        <p>Dedicated Professionals at Your Service</p>
                    </div>
                </div>
                <div class="carousel-item">
                    <img src="images/hospital-3.jpg" class="d-block w-100" alt="Medical Equipment">
                    <div class="carousel-caption">
                        <h2>State-of-the-Art Facilities</h2>
                        <p>Advanced Technology for Better Care</p>
                    </div>
                </div>
                <div class="carousel-item">
                    <img src="images/hospital-4.jpg" class="d-block w-100" alt="Patient Care">
                    <div class="carousel-caption">
                        <h2>Patient-Centered Care</h2>
                        <p>Your Health is Our Priority</p>
                    </div>
                </div>
                <div class="carousel-item">
                    <img src="images/hospital-5.jpg" class="d-block w-100" alt="Emergency Care">
                    <div class="carousel-caption">
                        <h2>24/7 Emergency Services</h2>
                        <p>Always Here When You Need Us</p>
                    </div>
                </div>
            </div>
            <a class="carousel-control-prev" href="#heroCarousel" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#heroCarousel" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a>
        </div>

        <!-- Features Section -->
        <section class="features">
            <div class="container">
                <div class="row">
                    <div class="col-md-4 feature-box">
                        <i class="fas fa-user-md"></i>
                        <h3>Expert Doctors</h3>
                        <p>Our team of experienced medical professionals is dedicated to providing the highest quality care.</p>
                    </div>
                    <div class="col-md-4 feature-box">
                        <i class="fas fa-heartbeat"></i>
                        <h3>Emergency Care</h3>
                        <p>24/7 emergency services with rapid response times and state-of-the-art equipment.</p>
                    </div>
                    <div class="col-md-4 feature-box">
                        <i class="fas fa-procedures"></i>
                        <h3>Quality Treatment</h3>
                        <p>Comprehensive healthcare services using the latest medical technologies.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- About Section -->
        <section class="about">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h2>About Evergreen Health Clinic</h2>
                        <p>We are a state-of-the-art medical facility committed to delivering exceptional healthcare services to our community. Our dedicated team of healthcare professionals works tirelessly to ensure every patient receives personalized care and attention.</p>
                        <a href="services.php" class="btn btn-primary">Learn More</a>
                    </div>
                    <div class="col-md-6">
                        <img src="images/hospital-1.jpg" alt="Hospital Building" class="img-fluid rounded">
                    </div>
                </div>
            </div>
        </section>

  <!-- CTA Section -->
<section class="cta">
    <div class="container text-center">
    <?php 
                if (isset($_SESSION['user_type'])) {
                    if ($_SESSION['user_type'] === 'patient') {
                        echo '<h2>Need Medical Attention?</h2>';
                        echo '<p>Schedule an appointment with one of our healthcare professionals today.</p>';
                        echo '<a href="patientAppointmentList.php" class="btn btn-lg btn-primary">Make an Appointment</a>';
                    } 
                    elseif ($_SESSION['user_type'] === 'staff') {
                        echo '<h2>Manage Patient Care</h2>';
                        echo '<p>View and manage your upcoming appointments.</p>';
                        echo '<a href="appointmentList.php" class="btn btn-lg btn-primary">View Appointments</a>';
                    }
                    elseif ($_SESSION['user_type'] === 'admin') {
                        echo '<h2>Hospital Administration</h2>';
                        echo '<p>Access administrative tools and system management.</p>';
                        echo '<a href="admin_portal.php" class="btn btn-lg btn-primary">Go to Admin Portal</a>';
                    }
                } else {
                    echo '<h2>Need Medical Attention?</h2>';
                    echo '<p>Schedule an appointment with one of our healthcare professionals today.</p>';
                    echo '<a href="login.php" class="btn btn-lg btn-primary">Login to Make an Appointment</a>';
                    echo '<p class="mt-2 text-light">New patient? <a href="register.php" class="text-white"><u>Register here</u></a></p>';
                }
                ?>
            </div>
        </section>
    </main>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <?php include "footer.php"; ?>
</body>
</html>