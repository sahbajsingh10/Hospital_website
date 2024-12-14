<script src="script.js"></script>
    <link rel="stylesheet" href="header.css">

<?php
require "./includes/conn.php";
$logged_in = isset($_SESSION['user_id']) ? true : false;
$user_type = isset($_SESSION['user_type']) ? $_SESSION['user_type'] : '';
$user_image = isset($_SESSION['user_image']) ? $_SESSION['user_image'] : '';
?>
<body>
<header>
    <!-- Add a container div for better logo + content alignment -->
    <div class="header-top">
    <a href="home.php"> <img src="images/weblogo.png" alt="CCSU Hospital Logo" class="logo"></a>
        <div class="header-content">
            <h1>Evergreen Health Clinic</h1>
            <p>Providing Quality Care You Can Trust</p>
        </div>
        
        <!-- Move user icon container up to align with logo -->
        <?php if ($logged_in === true): ?>
            <div class="user-icon-container">
                <img id="header_user_icon" src="<?php echo htmlspecialchars($user_image); ?>" alt="User Profile" onclick="openMenu('user_card');">
            </div>
        <?php endif; ?>
        
        <button class="hamburger" onclick="toggleMenu()" aria-label="Toggle menu">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </div>
    
    <div class="nav-container">
        <?php if ($logged_in === true): ?>
            <a href="manage_profile.php" class="mobile-user-info" style="cursor: pointer; text-decoration: none; color:white;" >
                <img src="<?php echo htmlspecialchars($user_image); ?>" alt="User Profile">
                <span>My Account</span> 
          </a>
        <?php endif; ?>
        
        <nav>
            <div class="nav-left">
                <a href="home.php" <?php echo ($_SERVER['REQUEST_URI'] === '/home.php' ? 'class="active"' : ''); ?>>Home</a>
                <a href="about.php" <?php echo ($_SERVER['REQUEST_URI'] === '/about.php' ? 'class="active"' : ''); ?>>About Us</a>
                <a href="contact.php" <?php echo ($_SERVER['REQUEST_URI'] === '/contact.php' ? 'class="active"' : ''); ?>>Contact</a>
                <?php if ($user_type === 'admin'): ?>
                    <a href="admin_portal.php" <?php echo ($_SERVER['REQUEST_URI'] === '/admin_portal.php' ? 'class="active"' : ''); ?>>Admin Portal</a>
                <?php endif; ?>
                <?php if ($user_type === 'staff'): ?>
                    <a href="staff_portal.php" <?php echo ($_SERVER['REQUEST_URI'] === '/staff_portal.php' ? 'class="active"' : ''); ?>>Staff Portal</a>
                <?php endif; ?>
                <?php if ($user_type === 'patient'): ?>
                    <a href="patient_portal.php" <?php echo ($_SERVER['REQUEST_URI'] === '/patient_portal.php' ? 'class="active"' : ''); ?>>Patient Portal</a>
                <?php endif; ?>
                <?php if (!$logged_in): ?>
                    <a href="login.php" class="login-button">Login</a>
                <?php else: ?>
                    <a href="logout.php">Logout</a>
                <?php endif; ?>
            </div>
        </nav>
    </div>

    <?php if ($logged_in === true): ?>
        <?php include "./includes/user_card.php"; ?>
    <?php endif; ?>
</header>
    </body>
<script> function toggleMenu() {
    const navContainer = document.querySelector('.nav-container');
    const hamburger = document.querySelector('.hamburger');
    
    navContainer.classList.toggle('active');
    
    // Optional: Animate hamburger to X
    const spans = hamburger.getElementsByTagName('span');
    if (navContainer.classList.contains('active')) {
        spans[0].style.transform = 'rotate(45deg) translate(5px, 5px)';
        spans[1].style.opacity = '0';
        spans[2].style.transform = 'rotate(-45deg) translate(7px, -6px)';
    } else {
        spans[0].style.transform = 'none';
        spans[1].style.opacity = '1';
        spans[2].style.transform = 'none';
    }
}

// Close menu when clicking outside
document.addEventListener('click', function(event) {
    const navContainer = document.querySelector('.nav-container');
    const hamburger = document.querySelector('.hamburger');
    
    if (!navContainer.contains(event.target) && !hamburger.contains(event.target)) {
        navContainer.classList.remove('active');
        const spans = hamburger.getElementsByTagName('span');
        spans[0].style.transform = 'none';
        spans[1].style.opacity = '1';
        spans[2].style.transform = 'none';
    }
});</script>