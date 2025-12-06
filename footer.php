<style>
    /* ===== FOOTER STYLES ===== */
    .footer-section {
        background-color: #111827;
        /* Dark Slate */
        color: #9ca3af;
        /* Muted Grey text */
        font-family: 'Poppins', sans-serif;
        padding-top: 70px;
        margin-top: auto;
        /* Pushes footer to bottom if page is short */
    }

    .footer-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 40px;
        padding-bottom: 50px;
        border-bottom: 1px solid #374151;
    }

    /* Column 1: Brand */
    .footer-brand .logo {
        font-size: 28px;
        font-weight: 800;
        color: #ffffff;
        text-decoration: none;
        display: inline-block;
        margin-bottom: 20px;
    }

    .footer-brand .logo span {
        color: #0c5adb;
        /* Primary Blue */
    }

    .footer-brand p {
        line-height: 1.6;
        margin-bottom: 25px;
        font-size: 14px;
    }

    .social-links {
        display: flex;
        gap: 15px;
    }

    .social-links a {
        width: 40px;
        height: 40px;
        background: #1f2937;
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        text-decoration: none;
        transition: 0.3s ease;
    }

    .social-links a:hover {
        background: #0c5adb;
        transform: translateY(-3px);
    }

    /* Columns 2, 3, 4: Links & Contact */
    .footer-col h3 {
        color: #ffffff;
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 25px;
        position: relative;
    }

    .footer-col h3::after {
        content: '';
        position: absolute;
        left: 0;
        bottom: -8px;
        width: 40px;
        height: 2px;
        background-color: #0c5adb;
    }

    .footer-links {
        list-style: none;
        padding: 0;
    }

    .footer-links li {
        margin-bottom: 12px;
    }

    .footer-links a {
        color: #9ca3af;
        text-decoration: none;
        font-size: 14px;
        transition: 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .footer-links a:hover {
        color: #ffffff;
        padding-left: 5px;
        /* Slide effect */
    }

    .footer-links a::before {
        content: '\203A';
        /* Chevron symbol */
        font-size: 18px;
        color: #0c5adb;
        transition: 0.3s;
    }

    .contact-info li {
        display: flex;
        align-items: flex-start;
        gap: 15px;
        margin-bottom: 15px;
        font-size: 14px;
        line-height: 1.5;
    }

    .contact-info i {
        color: #0c5adb;
        font-size: 16px;
        margin-top: 3px;
    }

    /* Copyright Bar */
    .copyright-bar {
        text-align: center;
        padding: 25px 0;
        font-size: 14px;
        background-color: #111827;
    }

    .copyright-bar p {
        margin: 0;
    }

    .copyright-bar span {
        color: #0c5adb;
        font-weight: 600;
    }

    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .footer-container {
            grid-template-columns: 1fr;
            /* Stack columns on mobile */
            text-align: center;
        }

        .footer-col h3::after {
            left: 50%;
            transform: translateX(-50%);
        }

        .social-links {
            justify-content: center;
        }

        .contact-info li {
            justify-content: center;
        }
    }
</style>

<footer class="footer-section">
    <div class="footer-container">

        <div class="footer-brand">
            <a href="Home.php" class="logo"><span>M</span>edi<span>C</span>are+</a>
            <p>Leading the way in medical excellence. We provide world-class healthcare with a compassionate touch, ensuring the well-being of you and your family.</p>

            <div class="social-links">
                <a href="https://www.facebook.com"><i class="fab fa-facebook-f"></i></a>
                <a href="https://www.x.com"><i class="fab fa-twitter"></i></a>
                <a href="https://www.instagram.com"><i class="fab fa-instagram"></i></a>
                <a href="https://www.linkedin.com"><i class="fab fa-linkedin-in"></i></a>
            </div>
        </div>

        <div class="footer-col">
            <h3>Quick Links</h3>
            <ul class="footer-links">
                <li><a href="Home.php">Home</a></li>
                <li><a href="About.php">About Us</a></li>
                <li><a href="Doctor.php">Find a Doctor</a></li>
                <li><a href="Review.php">Patient Reviews</a></li>
                <li><a href="Blog.php">Health Blog</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h3>Our Services</h3>
            <ul class="footer-links">
                <li><a href="#">Cardiology</a></li>
                <li><a href="#">Neurology</a></li>
                <li><a href="#">Emergency Care</a></li>
                <li><a href="#">Laboratory</a></li>
                <li><a href="#">Dental Care</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h3>Contact Us</h3>
            <ul class="contact-info" style="list-style: none; padding: 0;">
                <li>
                    <i class="fas fa-map-marker-alt"></i>
                    <span>No 84, Negombo - Colombo Main Rd,<br>Kandana 11320, Sri Lanka</span>
                </li>
                <li>
                    <i class="fas fa-phone-alt"></i>
                    <span>+94 112 345 678</span>
                </li>
                <li>
                    <i class="fas fa-envelope"></i>
                    <span>info@medicareplus.com</span>
                </li>
                <li>
                    <i class="fas fa-clock"></i>
                    <span>Open 24 Hours / 7 Days</span>
                </li>
            </ul>
        </div>

    </div>

    <div class="copyright-bar">
        <p>&copy; <?php echo date("Y"); ?> <span>MediCare+</span>. All Rights Reserved. Designed for Excellence.</p>
    </div>
</footer>

</body>

</html>