<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clinic | Contact</title><link rel="icon" href="images/icon.png" type="image/png">
    <script src="https://kit.fontawesome.com/7e01d2ce2c.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="bootstrap4.5.2.css">
    <link rel="stylesheet" href="hospital-style.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .contact-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .emergency-notice {
            background: #ff6b6b;
            color: white;
            padding: 1rem;
            text-align: center;
            border-radius: 10px;
            margin-bottom: 2rem;
            font-size: 1.2rem;
        }

        .emergency-notice strong {
            font-size: 1.4rem;
        }

        .contact-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .contact-info, .location-info {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .section-title {
            color: #2e8b57;
            margin-bottom: 1.5rem;
        }

        .info-item {
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .info-item i {
            color: #2e8b57;
            font-size: 1.5rem;
            width: 30px;
        }

        .map-container {
            margin-top: 1rem;
            height: 300px;
        }

        .map-container iframe {
            width: 100%;
            height: 100%;
            border: none;
            border-radius: 8px;
        }

        .hours-list {
            list-style: none;
            padding: 0;
        }

        .hours-list li {
            margin-bottom: 0.5rem;
            display: flex;
            justify-content: space-between;
        }
        body{
            margin-top:12vh !important;
        }

        @media (max-width: 768px) {
            .contact-section {
                grid-template-columns: 1fr;
            }

        .contact-container{
        margin-top:-5vh !important;
      }
        }
    </style>
</head>

<?php include "header.php"; ?>

<body>
    <div class="contact-container">
        <div class="emergency-notice">
            <strong>For Medical Emergencies, Please Dial 911</strong><br>
            Emergency services are available 24/7
        </div>

        <div class="contact-section">
            <div class="contact-info">
                <h2 class="section-title">Contact Information</h2>
                
                <div class="info-item">
                    <i class="fas fa-phone"></i>
                    <div>
                        <strong>Main Line</strong><br>
                        <a href="tel:+5551234567" style="text-decoration:none !important; color:#333333;">  (555) 123-4567</a>
                    </div>
                </div>

                <div class="info-item">
                    <i class="fas fa-envelope"></i>
                    <div>
                        <strong>Email</strong><br>
                        <a href="mailto:info@evergreenhealth.com" style="text-decoration:none !important; color:#333333;">info@evergreenhealth.com</a>
                    </div>
                </div>

                <div class="info-item">
                    <i class="fas fa-location-dot"></i>
                    <div>
                        <strong>Address</strong><br>
                        <a href="https://www.google.com/maps/@41.768374,-72.772014,884m/data=!3m1!1e3?hl=en&entry=ttu&g_ep=EgoyMDI0MTIwNC4wIKXMDSoASAFQAw%3D%3D" target="_blank"  style="text-decoration:none; color:#333333;">  123 Healthcare Avenue<br>
                        New Britain, CT 06050 </a>
                    </div>
                </div>

                <h3 class="section-title">Hours of Operation</h3>
                <ul class="hours-list">
                    <li>
                        <span>Monday - Sunday</span>
                        <span>8:00 AM - 6:00 PM</span>
                    </li>
                </ul>
            </div>

            <div class="location-info">
                <h2 class="section-title">Find Us</h2>
                <div class="map-container">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2974.4910660392897!2d-72.77147792426697!3d41.76844627222821!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89e7b2718c77c60b%3A0x8d2cd4c5b2c7ce89!2sCentral%20Connecticut%20State%20University!5e0!3m2!1sen!2sus!4v1701905735419!5m2!1sen!2sus" 
                        allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
                <div class="info-item" style="margin-top: 1rem;">
                    <i class="fas fa-bus"></i>
                    <div>
                        <strong>Public Transportation</strong><br>
                        Bus routes 41 and 45 stop directly in front of the hospital
                    </div>
                </div>
            </div>
        </div>
    </div>
<br><br><br><br>
</body>
<?php include "footer.php"; ?>
</html>