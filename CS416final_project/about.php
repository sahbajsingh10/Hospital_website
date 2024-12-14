<?php
include './includes/conn.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clinic | About Us</title> <link rel="icon" href="images/icon.png" type="image/png">
    <link rel="stylesheet" href="bootstrap4.5.2.css">
    <link rel="stylesheet" href="hospital-style.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        main {
            margin: auto;
            max-width: 100%;
            margin-bottom:5vh !important;
        }
        body {
    margin: 0;
    padding-top: 23vh !important; /* This will create space below header */
}
        section {
            margin: 30px 0;
            padding: 20px;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        section h2, section h3, section p {
            text-align: center;
        }

        .services ul {
            list-style: none;
            padding: 0;
            text-align: center;
        }

        .services ul li {
            margin: 5px 0;
        }
        /* About Page Styles */
.about-page main {
  padding: 2rem;
  max-width: 1200px;
  margin: 0 auto;
}

/* Hero Section */
.hero {
  background: linear-gradient(rgba(46, 139, 87, 0.9), rgba(46, 139, 87, 0.8)), url('/images/hospital-bg.jpg');
  background-size: cover;
  background-position: center;
  color: white;
  text-align: center;
  padding: 4rem 2rem;
}

.hero h2 {
  font-size: 2.8rem;
  margin-bottom: 1.5rem;
}

.hero p {
  font-size: 1.2rem;
  max-width: 800px;
  margin: 0 auto;
  line-height: 1.6;
  color:white;
}
/* Content Sections */
section:not(.hero) {
  background: white;
  border-radius: 10px;
  padding: 2rem;
  margin-bottom: 2rem;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

section:not(.hero):hover {
  transform: translateY(-5px);
  box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
}

section h3 {
  color: #2e8b57;
  font-size: 1.8rem;
  margin-bottom: 1.5rem;
  text-align: center;
}

section p {
  color: #666;
  line-height: 1.8;
  margin-bottom: 1.5rem;
  text-align: center;
  max-width: 900px;
  margin-left: auto;
  margin-right: auto;
}

/* Services Section */
.services ul {
    list-style: none;
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 1rem;
    padding: 0;
    max-width: 1000px;
    margin: 0 auto;
}

.services li {
    flex-basis: calc(25% - 1rem);  /* For 4 items per row */
    min-width: 200px;
    background: #f8f9fa;
    padding: 1rem;
    text-align: center;
    border-radius: 5px;
    margin-bottom: 1rem;
}

.services li:nth-last-child(-n+3):first-child,
.services li:nth-last-child(-n+3):first-child ~ li {
    margin-left: auto;
    margin-right: auto;
}

.services li:hover {
    background: #e9ecef;
}

/* Vision-Mission Section */
.vision-mission {
  background: linear-gradient(to bottom right, #ffffff, #f8f9fa);
}

.vision-mission h3 {
  margin-top: 2rem;
}

.vision-mission h3:first-child {
  margin-top: 0;
}

/* Responsive Design */
@media (max-width: 768px) {
  .about-page{
        margin-top:-3vh !important;
      }
  .about-page main {
      padding: 1rem;
  }

  .hero {
        padding: 2rem 1rem !important;
        width: 100vw !important;
        margin-left: calc(-50vw + 50%) !important;
        margin-right: calc(-50vw + 50%) !important;
        position: relative !important;
        left: 0 !important;
        right: 0 !important;
    }

  .hero h2 {
      font-size: 2rem;
  }

  section h3 {
      font-size: 1.5rem;
  }

  .services ul {
      grid-template-columns: 1fr;
  }
   .services li {
        flex-basis: 100%;
        margin: 0.5rem 0;
    }
}

/* Additional spacing for the footer */
main {
  margin-bottom: 7vh;
}
    </style>
</head>
<?php include "header.php"; ?>
<body class="about-page">

<main>
    <section class="hero">
        <h2>About Our Hospital</h2>
        <p>Your health is at the heart of everything we do. Our hospital combines world-class healthcare with personalized treatment plans to deliver the care you deserve.</p>
    </section>


    <section class="vision-mission">
    <h3>Who We Are</h3>
    <p>Founded in 1776, CCSU Hospital has grown to become a leading healthcare provider in the region. Our state-of-the-art facilities and expert medical staff are dedicated to delivering exceptional care for every patient.</p>
        <h3>Our Vision</h3>
        <p>To be the trusted leader in healthcare, committed to innovation, education, and excellence in patient care.</p>
        <h3>Our Mission</h3>
        <p>We strive to provide accessible, high-quality healthcare services that improve the health and well-being of our community. Through a team of compassionate and highly skilled professionals, we aim to set the standard for excellence in healthcare delivery.</p>
    </section>

    <section class="services">
        <h3>What We Offer</h3>
        <ul>
            <li>24/7 Emergency Services</li>
            <li>Comprehensive Diagnostic Imaging</li>
            <li>Specialized Surgery Units</li>
            <li>Cardiology and Heart Care</li>
            <li>Maternity and Neonatal Care</li>
            <li>Oncology and Cancer Treatment</li>
            <li>Rehabilitation and Physical Therapy</li>
        </ul>
    </section>

    <section class="team">
        <h3>Meet Our Team</h3>
        <p>Our hospital is home to a diverse team of healthcare professionals, including leading doctors, surgeons, nurses, and support staff. With a shared commitment to patient care, our team works collaboratively to ensure every individual receives the best treatment possible.</p>
    </section>

    <section class="history">
        <h3>Our Journey</h3>
        <p>Over the years, [Hospital Name] has evolved to meet the changing healthcare needs of our community. From our humble beginnings as a small clinic to becoming a full-fledged hospital, our focus has always been on advancing healthcare through innovation and compassionate care.</p>
    </section>

    <section class="community">
        <h3>Our Commitment to the Community</h3>
        <p>Beyond patient care, we are deeply involved in community outreach programs, health education, and charitable services. We believe in giving back to the community that has supported us through the years.</p>
    </section>
</main>

</body>
<?php include "footer.php"; ?>
</html>
