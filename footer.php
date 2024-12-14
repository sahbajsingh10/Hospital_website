<!DOCTYPE html>
<html>
<head>
  <style>
    footer {
      background-color: #1a1a1a;
      color: #ffffff;
      padding: 3rem 0;
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }

    .footer-content {
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 2rem;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 2rem;
    }

    .footer-section h3 {
      color: #ffffff;
      font-size: 1.2rem;
      margin-bottom: 1rem;
      font-weight: 600;
    }

    .footer-section ul {
      list-style: none;
      padding: 0;
      margin: 0;
    }

    .footer-section li {
      margin-bottom: 0.5rem;
    }

    .footer-section a {
      color: #9ca3af;
      text-decoration: none;
      transition: color 0.2s ease;
    }

    .footer-section a:hover {
      color: #ffffff;
    }

    .social-links {
      display: flex;
      gap: 1rem;
      margin-top: 1rem;
    }

    .social-links a {
      color: #9ca3af;
      font-size: 1.5rem;
    }

    .bottom-bar {
      margin-top: 3rem;
      padding-top: 1.5rem;
      border-top: 1px solid #333;
      text-align: center;
      color: #9ca3af;
      font-size: 0.875rem;
    }

    .emergency-button {
      background-color: #dc2626;
      color: white;
      padding: 0.75rem;
      border-radius: 4px;
      text-align: center;
      font-weight: bold;
      margin-bottom: 1rem;
      display: block;
      text-decoration: none;
    }

    .emergency-button:hover {
      background-color: #b91c1c;
    }

    @media (max-width: 768px) {
      .footer-content {
        grid-template-columns: 1fr;
      }
      
      .footer-section {
        text-align: center;
      }
      
      .social-links {
        justify-content: center;
      }
    }
  </style>
</head>
<body>
  <footer>
    <div class="footer-content">
      <div class="footer-section">
        <h3>About Evergreen Health Clinic</h3>
        <p style="color: #9ca3af; line-height: 1.6;">Committed to providing exceptional healthcare services with compassion and excellence. Serving our community since 1975.</p>
      </div>
      
      <div class="footer-section">
        <h3>Patient Resources</h3>
        <ul>
          <li><a href="home.php">Home</a></li>
          <li><a href="about.php">About Us</a></li>
          <li><a href="contact.php">Contact</a></li>
          <li><a href="services.php">Services</a></li>
        </ul>
      </div>
      
      <div class="footer-section">
        <h3>Contact Info</h3>
        <ul>
        <a href="https://www.google.com/maps/@41.768374,-72.772014,884m/data=!3m1!1e3?hl=en&entry=ttu&g_ep=EgoyMDI0MTIwNC4wIKXMDSoASAFQAw%3D%3D" target="_blank" style="text-decoration:none;">   <li style="color: #9ca3af;">123 Healthcare Avenue</li>
          <li style="color: #9ca3af;">New Britain, CT 06050</li> </a>
          <a href="tel:+5551234567"><li style="color: #9ca3af;">Main: (555) 123-4567</li></a>
          <a href="tel:+5551234568"><li style="color: #9ca3af;">Appointments: (555) 123-4568</li></a>
        </ul>
      </div>
      
      <div class="footer-section">
        <h3>Emergency Services</h3>
        <p class="emergency-button">Call 911 for Emergencies</p>
        <p style="color: #9ca3af; margin-bottom: 1rem;">24/7 Emergency Department Location:</p>
        <ul>
          <li style="color: #9ca3af;">Emergency Room Entrance</li>
          <li style="color: #9ca3af;">North Wing, Ground Floor</li>
          <li style="color: #9ca3af;">Ambulance Bay: East Side</li>
        </ul>
        <p style="color: #9ca3af; margin-top: 1rem;">For non-emergencies, please call our main number or schedule an appointment online.</p>
      </div>
    </div>
    
    <div class="bottom-bar">
      <p>&copy; 2024 Evergreen Health Clinic. All rights reserved. | Privacy Policy | Accessibility | Patient Rights</p>
    </div>
  </footer>
</body>
</html>