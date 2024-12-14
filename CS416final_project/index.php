<?php
session_start();
session_destroy();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Our Project</title><link rel="icon" href="images/icon.png" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        /* Basic CSS Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Full-page setup */
        body,
        html {
            height: 100%;
            font-family: 'Poppins', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            background: conic-gradient(from 0deg, #2e8b57, #30925b, #2e8b57);
         
            position: relative;
            overflow: hidden;
        }

        /* Particle Background */
        .particle-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: -1;
        }

        .particle {
            position: absolute;
            width: 10px;
            height: 10px;
            background-color: rgba(255, 255, 255, 0.8);
            border-radius: 50%;
            animation: float 20s infinite ease-in-out;
        }

        @keyframes float {
            0% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-100vh);
                opacity: 0.5;
            }

            100% {
                transform: translateY(0);
            }
        }

        /* Content Container */
        .intro-container {
            text-align: center;
            color: #fff;
            opacity: 0;
            animation: fadeIn 2s ease forwards;
            z-index: 1;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Heading with Smooth Gradient Animation */
        h1,
        .clinic {
            font-size: 3.5rem;
            margin-bottom: 10px;
            background: linear-gradient(90deg, #2ebd86, #FFFFFF, #2ebd86, #FFFFFF, #2ebd86, #FFFFFF);
            background-size: 500% 100%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: gradient 10s linear infinite; 
        }

        @keyframes gradient {
            0% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        /* Subheading Text */
        p {
            font-size: 1.5rem;
            margin-bottom: 30px;
            animation: slideIn 2s ease forwards;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Get Started Button with Bobbing Animation */
        .start-btn {
            font-size: 1.2rem;
            padding: 10px 20px;
            color: #2e8b57;
            background-color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            animation: bob 2s infinite;
        }

        @keyframes bob {
            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        .start-btn:hover {
            background-color: #2e8b57;
            color: #fff;
        }

        .clinic {
            font-size: 2.5em;
        }
    </style>
</head>

<body>
    <!-- Particle Background Animation -->
    <div class="particle-container">
        <!-- Generate particles dynamically with JavaScript -->
    </div>

    <!-- Intro Content -->
    <div class="intro-container">
        <h1>Welcome to Our Project</h1>
        <p class="clinic">Evergreen Health Clinic</p>
        <hr><br>
        <p>By: Gary Fox, Michael Botteon, and Sahbaj Singh</p>
        <button class="start-btn" onclick="getStarted()">Get Started</button>
    </div>

    <!-- JavaScript for Particle Animation and Redirection -->
    <script>
        // Particle Animation: Generate particles
        const particleContainer = document.querySelector('.particle-container');

        for (let i = 0; i < 100; i++) {
            const particle = document.createElement('div');
            particle.classList.add('particle');
            particle.style.left = `${Math.random() * 100}vw`;
            particle.style.top = `${Math.random() * 100}vh`;
            particle.style.animationDuration = `${Math.random() * 5 + 15}s`;
            particle.style.animationDelay = `${Math.random() * 5}s`;
            particleContainer.appendChild(particle);
        }

        // Redirect to main page
        function getStarted() {
            window.location.href = "home.php"; // Replace with your main page link
        }
    </script>
</body>

</html>