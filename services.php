<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="bootstrap4.5.2.css">
    <link rel="stylesheet" href="hospital-style.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="services.css">
    <title>Clinic | Services</title>
    <link rel="icon" href="images/icon.png" type="image/png">
    <style>
        /* Reset any existing margins/padding */
        body {
            margin-top: 30vh !important;
            padding-top: 0 !important;
        }
        .services-page {
            padding-top: 0 !important;
            margin-top: 0 !important;
        }
    </style>
</head>
<?php include "header.php"; ?>
<body>
    <script>
        // Function to set the margin
        function setMargin() {
            const header = document.querySelector('header');
            if (header) {
                const headerHeight = header.getBoundingClientRect().height;
                document.body.style.marginTop = (headerHeight + 20) + 'px';
            }
        }

        // Run when DOM loads
        document.addEventListener('DOMContentLoaded', setMargin);
        // Run when everything loads
        window.addEventListener('load', setMargin);
        // Run when window resizes
        window.addEventListener('resize', setMargin);
        // Run again after a slight delay to catch any dynamic content
        setTimeout(setMargin, 100);
        setTimeout(setMargin, 500);
    </script>

    <div class="services-page">
        <h1>Our Services</h1>
        <p>At Hartford HealthCare, we offer a wide range of healthcare services designed to meet the needs of our community.</p>
        <div class="services-grid">
            <?php
            $services = [
                ["title" => "Emergency Care", "description" => "Comprehensive emergency medical treatment available 24/7.", "img" => "images/er_room.png"],
                ["title" => "Cardiology Services", "description" => "Our state-of-the-art cardiac center provides quality care for your heart.", "img" => "images/cardiology.png"],
                ["title" => "Orthopedic Services", "description" => "Advanced treatment for musculoskeletal issues to help you regain mobility.", "img" => "images/ortho.png"],
                ["title" => "Women's Health", "description" => "Dedicated care for women at every stage of life.", "img" => "images/womens_health.png"],
                ["title" => "Oncology", "description" => "Comprehensive cancer care and support.", "img" => "images/oncology.png"],
                ["title" => "Primary Care", "description" => "Personalized care for your everyday health needs.", "img" => "images/primary_care.png"],
                ["title" => "Mental Health Services", "description" => "We offer a variety of mental health services to support your well-being.", "img" => "images/mental_health.png"],
            ];
            foreach ($services as $service) {
                echo '
                <div class="service-card">
                    <img src="' . htmlspecialchars($service["img"]) . '" alt="' . htmlspecialchars($service["title"]) . '" class="service-image">
                    <h2>' . htmlspecialchars($service["title"]) . '</h2>
                    <p>' . htmlspecialchars($service["description"]) . '</p>
                </div>';
            }
            ?>
        </div>
    </div>
</body>
<?php include "footer.php"; ?>
</html>