<head>
    <script src="script.js"></script>
    <script src="https://kit.fontawesome.com/7e01d2ce2c.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="style.css">
    <style>
#user_card h1, 
#user_card p {
    word-wrap: break-word;     /* Ensures text wraps */
    overflow-wrap: break-word; /* Modern browsers */
    max-width: 100%;          /* Keeps text within card */
}

@media screen and (max-width: 1500px) {
    #user_card {
        width: 300px;
    }
}

@media (max-width: 768px) {
    #user_card {
        display: none;
    }
}
        </style>
</head>

<?php
require "conn.php";
$first_name = $_SESSION['first_name'];
$last_name = $_SESSION['last_name'];
$phone = $_SESSION['phone'];
$email = $_SESSION['email'];
$user_type = $_SESSION['user_type'];

// Get staff title if user is a staff member
$staff_title = '';
if ($user_type === 'staff') {
    $user_id = $_SESSION['user_id'];
    $query = "SELECT staff_profile_type 
              FROM staffprofiles 
              WHERE user_id = :user_id";
    
    $stmt = $conn->prepare($query);
    $stmt->execute(['user_id' => $user_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        $staff_title = $result['staff_profile_type'] ? $result['staff_profile_type'] : 'Unspecified';
    }
}
?>

<div id="user_card">
    <div id="user_card_controls">
        <button id="user_card_options" onclick="navigate('manage_profile.php')"><i class="fa-solid fa-gear"></i></button>
        <button id="user_card_close" onclick="closeMenu('user_card')"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <img src="<?php echo htmlspecialchars($user_image); ?>" alt="User Image">
    <h1><?php echo $first_name . " " . $last_name ?></h1>
    <?php if ($user_type === 'staff'): ?>
        <p><?php echo htmlspecialchars($staff_title); ?></p>
    <?php endif; ?>
    <p><?php echo $email ?></p>
    <p><?php echo $phone ?></p>
    <button id="user_card_logout" onclick="navigate('logout.php')">Logout</button>
</div>