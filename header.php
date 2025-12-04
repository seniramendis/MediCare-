<?php
// 1. Safe Session Start
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Safe Database Connection
include_once 'db_connect.php';

// 3. Get current page for active menu highlighting
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?php echo isset($page_title) ? $page_title . " | MediCare+" : "MediCare+"; ?></title>

    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><rect width=%22100%22 height=%22100%22 rx=%2220%22 fill=%22%230c5adb%22/><path fill=%22%23ffffff%22 d=%22M35 20h30v25h25v30h-25v25h-30v-25h-25v-30h25z%22/></svg>">

    <script src="https://kit.fontawesome.com/9e166a3863.js" crossorigin="anonymous"></script>

    <style>
        /* ===== GLOBAL PROFESSIONAL STYLES (YOUR ORIGINAL DESIGN) ===== */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        :root {
            --primary-color: #0c5adb;
            --primary-dark: #0946a8;
            --accent-color: #ff4d6d;
            --text-dark: #1f2937;
            --text-light: #6b7280;
            --white: #ffffff;
            --glass-bg: rgba(255, 255, 255, 0.95);
            --shadow-soft: 0 10px 40px rgba(0, 0, 0, 0.08);
            --radius: 12px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
            scroll-behavior: smooth;
        }

        body {
            background: linear-gradient(rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0.9)), url('images/background3.png');
            background-repeat: no-repeat;
            background-position: center center;
            background-attachment: fixed;
            background-size: cover;
            color: var(--text-dark);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ===== HEADER ===== */
        .main-header {
            width: 100%;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
            padding: 15px 60px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05);
        }

        .logo {
            font-size: 28px;
            font-weight: 800;
            color: var(--primary-color);
            text-decoration: none;
            letter-spacing: -0.5px;
        }

        .logo span {
            color: var(--accent-color);
        }

        .navbar ul {
            list-style: none;
            display: flex;
            gap: 35px;
            align-items: center;
        }

        .navbar ul li a {
            text-decoration: none;
            color: var(--text-dark);
            font-size: 16px;
            font-weight: 500;
            transition: 0.3s;
            position: relative;
        }

        .navbar ul li a::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -4px;
            left: 0;
            background-color: var(--primary-color);
            transition: width 0.3s;
        }

        .navbar ul li a:hover::after,
        .navbar ul li a.active::after {
            width: 100%;
        }

        .navbar ul li a:hover,
        .navbar ul li a.active {
            color: var(--primary-color);
        }

        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: var(--primary-color);
            border: none;
            color: var(--white);
            font-size: 16px;
            font-weight: 600;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(12, 90, 219, 0.3);
            text-decoration: none;
        }

        .btn:hover {
            background: var(--primary-dark);
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(12, 90, 219, 0.4);
        }

        /* Inbox Icon Style */
        .icon-btn {
            color: var(--text-dark);
            font-size: 20px;
            position: relative;
            transition: 0.3s;
            padding: 5px;
        }

        .icon-btn:hover {
            color: var(--primary-color);
            transform: scale(1.1);
        }

        /* ===== USER DROPDOWN STYLES ===== */
        .user-dropdown {
            position: relative;
            display: inline-block;
        }

        .user-trigger {
            cursor: pointer;
            font-weight: 600;
            color: var(--primary-color);
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 15px;
            border-radius: 30px;
            transition: 0.3s;
            background: rgba(12, 90, 219, 0.05);
        }

        .user-trigger:hover {
            background: rgba(12, 90, 219, 0.1);
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            right: 0;
            top: 120%;
            /* Slight gap for animation */
            background: white;
            min-width: 200px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            z-index: 1001;
            border: 1px solid rgba(0, 0, 0, 0.05);
            overflow: hidden;
            animation: fadeIn 0.2s ease-out forwards;
        }

        /* Show dropdown on hover */
        .user-dropdown:hover .dropdown-menu {
            display: block;
        }

        .dropdown-menu a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 20px;
            color: var(--text-dark);
            text-decoration: none;
            font-size: 14px;
            transition: 0.2s;
            border-bottom: 1px solid #f9fafb;
        }

        .dropdown-menu a:last-child {
            border-bottom: none;
        }

        .dropdown-menu a:hover {
            background: #f9fafb;
            color: var(--primary-color);
            padding-left: 25px;
            /* Slight movement effect */
        }

        .dropdown-menu i {
            width: 20px;
            text-align: center;
            color: #9ca3af;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 768px) {
            .main-header {
                padding: 15px 20px;
                flex-direction: column;
                gap: 10px;
            }

            .navbar ul {
                gap: 15px;
                font-size: 14px;
                flex-wrap: wrap;
                justify-content: center;
            }
        }

        .footer {
            padding: 40px;
            background: #111827;
            color: rgba(255, 255, 255, 0.7);
            text-align: center;
            font-size: 14px;
            margin-top: auto;
        }
    </style>
</head>

<body>

    <header class="main-header">
        <a href="Home.php" class="logo"><span>M</span>edi<span>C</span>are+</a>
        <nav class="navbar">
            <ul>
                <li><a href="Home.php" class="<?= $current_page == 'Home.php' ? 'active' : '' ?>">Home</a></li>
                <li><a href="About.php" class="<?= $current_page == 'About.php' ? 'active' : '' ?>">About</a></li>
                <li><a href="Doctor.php" class="<?= $current_page == 'Doctor.php' ? 'active' : '' ?>">Doctors</a></li>
                <li><a href="Review.php" class="<?= $current_page == 'Review.php' ? 'active' : '' ?>">Reviews</a></li>
                <li><a href="Contact.php" class="<?= $current_page == 'Contact.php' ? 'active' : '' ?>">Contact</a></li>
                <li><a href="Blog.php" class="<?= $current_page == 'Blog.php' ? 'active' : '' ?>">Blog</a></li>

                <?php
                // --- LOGIC CHANGE: ONLY SHOW PROFILE IF NOT ADMIN ---
                // If user is logged in AND their role is NOT 'admin'
                if (isset($_SESSION['user_id']) && $_SESSION['role'] !== 'admin'):
                ?>

                    <li>
                        <a href="inbox.php" class="icon-btn" title="My Messages">
                            <i class="fas fa-comment-alt"></i>
                        </a>
                    </li>

                    <li style="display: flex; align-items: center; gap: 15px;">

                        <?php if ($_SESSION['role'] == 'patient'): ?>
                            <a href="dashboard_patient.php" class="btn" style="padding: 8px 20px; font-size: 14px;">Dashboard</a>
                        <?php elseif ($_SESSION['role'] == 'doctor'): ?>
                            <a href="dashboard_doctor.php" class="btn" style="padding: 8px 20px; font-size: 14px;">Dashboard</a>
                        <?php endif; ?>

                        <div class="user-dropdown">
                            <div class="user-trigger">
                                <span>Hi, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                                <i class="fas fa-chevron-down" style="font-size: 12px;"></i>
                            </div>

                            <div class="dropdown-menu">
                                <a href="edit_profile.php">
                                    <i class="fas fa-user-edit"></i> Edit Profile
                                </a>

                                <a href="logout.php" style="color: #ef4444;">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </a>
                            </div>
                        </div>

                    </li>

                <?php else: ?>
                    <li><a href="login.php" class="btn" style="padding: 8px 20px; font-size: 14px;">Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>