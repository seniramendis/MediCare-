<?php
$page_title = "Home";
include 'header.php'; ?>

<style>
    .hero-section {
        position: relative;
        min-height: 90vh;
        display: flex;
        align-items: center;

        background: linear-gradient(135deg, #f8f9fa 0%, #eef2ff 100%);
        overflow: hidden;
        padding: 0 8%;
    }


    .hero-section::after {
        content: '';
        position: absolute;
        top: -100px;
        right: -100px;
        width: 600px;
        height: 600px;
        background: radial-gradient(circle, rgba(12, 90, 219, 0.05) 0%, rgba(255, 255, 255, 0) 70%);
        border-radius: 50%;
        z-index: 1;
    }

    .hero-container {
        display: grid;
        grid-template-columns: 1.2fr 1fr;
        gap: 60px;
        align-items: center;
        width: 100%;
        z-index: 2;
    }

    .hero-text h1 {
        font-size: 56px;
        font-weight: 800;
        line-height: 1.15;
        color: var(--text-dark);
        margin-bottom: 25px;
    }

    .hero-text h1 span {
        color: var(--primary-color);
        position: relative;
        display: inline-block;
    }


    .hero-text h1 span::after {
        content: '';
        position: absolute;
        width: 100%;
        height: 10px;
        bottom: 5px;
        left: 0;
        background: rgba(255, 77, 109, 0.2);

        z-index: -1;
    }

    .hero-text p {
        font-size: 18px;
        color: var(--text-light);
        margin-bottom: 40px;
        max-width: 90%;
        line-height: 1.7;
    }

    .btn-group {
        display: flex;
        gap: 20px;
    }

    .btn-outline {
        background: transparent;
        border: 2px solid var(--primary-color);
        color: var(--primary-color);
        padding: 12px 30px;
        font-size: 16px;
        font-weight: 600;
        border-radius: 50px;
        text-decoration: none;
        transition: 0.3s;
    }

    .btn-outline:hover {
        background: var(--primary-color);
        color: white;
    }


    .hero-image {
        position: relative;
        display: flex;
        justify-content: center;
    }

    .hero-image img {
        width: 100%;
        max-width: 650px;
        border-radius: 20px;

        box-shadow: 0 20px 80px rgba(0, 0, 0, 0.15);
        animation: float 6s ease-in-out infinite;
        object-fit: cover;
    }


    .floating-card {
        position: absolute;
        bottom: 60px;
        left: -30px;
        background: white;
        padding: 20px 25px;
        border-radius: 15px;
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
        display: flex;
        align-items: center;
        gap: 15px;
        animation: fadeIn 1s ease-out 0.5s backwards;
        z-index: 3;
    }

    .floating-icon {
        width: 50px;
        height: 50px;
        background: #dcfce7;
        color: #16a34a;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 24px;
    }


    .stats-section {
        background: white;
        padding: 60px 8%;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 30px;
        text-align: center;
    }

    .stat-item h2 {
        font-size: 36px;
        color: var(--primary-color);
        margin-bottom: 5px;
    }

    .stat-item p {
        color: var(--text-light);
        font-weight: 500;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }


    .services-section {
        padding: 100px 8%;
        background: #f9fafb;
    }

    .section-header {
        text-align: center;
        margin-bottom: 60px;
    }

    .section-header h2 {
        font-size: 36px;
        margin-bottom: 15px;
        color: var(--text-dark);
    }

    .section-header p {
        color: var(--text-light);
        max-width: 600px;
        margin: 0 auto;
    }

    .service-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 30px;
    }

    .service-card {
        background: white;
        padding: 40px 30px;
        border-radius: 20px;
        transition: 0.3s;
        border: 1px solid transparent;
        position: relative;
        overflow: hidden;
    }

    .service-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(12, 90, 219, 0.1);
        border-color: rgba(12, 90, 219, 0.1);
    }

    .icon-box {
        width: 70px;
        height: 70px;
        background: rgba(12, 90, 219, 0.05);
        color: var(--primary-color);
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        margin-bottom: 25px;
        transition: 0.3s;
    }

    .service-card:hover .icon-box {
        background: var(--primary-color);
        color: white;
    }

    .service-card h3 {
        font-size: 22px;
        margin-bottom: 15px;
    }

    .service-card p {
        color: var(--text-light);
        font-size: 15px;
        line-height: 1.6;
    }


    .emergency-banner {
        background: linear-gradient(90deg, #1f2937 0%, #111827 100%);
        padding: 60px 8%;
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: white;
        border-radius: 0;
    }

    .banner-content h3 {
        font-size: 32px;
        margin-bottom: 10px;
    }

    .banner-content p {
        opacity: 0.8;
    }

    .phone-btn {
        background: var(--accent-color);
        color: white;
        padding: 15px 35px;
        border-radius: 50px;
        font-weight: 700;
        text-decoration: none;
        font-size: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0 10px 20px rgba(255, 77, 109, 0.3);
    }


    @keyframes float {
        0% {
            transform: translateY(0px);
        }

        50% {
            transform: translateY(-20px);
        }

        100% {
            transform: translateY(0px);
        }
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


    @media (max-width: 1024px) {
        .hero-text h1 {
            font-size: 42px;
        }

        .hero-container {
            grid-template-columns: 1fr;
            text-align: center;
        }

        .hero-text {
            order: 2;
        }

        .hero-image {
            order: 1;
            margin-bottom: 30px;
            display: flex;
            justify-content: center;
        }

        .btn-group {
            justify-content: center;
        }

        .hero-text p {
            margin: 0 auto 30px auto;
        }

        .floating-card {
            display: none;
        }

        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .emergency-banner {
            flex-direction: column;
            text-align: center;
            gap: 30px;
        }
    }
</style>

<div class="main-container">

    <section class="hero-section">
        <div class="hero-container">
            <div class="hero-text">
                <div style="color: var(--accent-color); font-weight: 600; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 1px;">Welcome to MediCare+</div>
                <h1>Your Health is <br><span>Our Top Priority</span></h1>
                <p>Experience world-class healthcare with compassion. We combine advanced medical technology with a human touch to ensure the best possible care for you and your family.</p>

                <div class="btn-group">
                    <a href="book_appointment.php" class="btn">Book Appointment</a>
                    <a href="Doctor.php" class="btn-outline">Find a Doctor</a>
                </div>
            </div>

            <div class="hero-image">
                <img src="https://images.pexels.com/photos/2280568/pexels-photo-2280568.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1" alt="Medical Team">

                <div class="floating-card">
                    <div class="floating-icon"><i class="fas fa-user-md"></i></div>
                    <div>
                        <h4 style="margin:0; font-size:16px;">Expert Doctors</h4>
                        <small style="color:var(--text-light);">24/7 Availability</small>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="stats-section">
        <div class="stats-grid">
            <div class="stat-item">
                <h2>15+</h2>
                <p>Years Experience</p>
            </div>
            <div class="stat-item">
                <h2>100+</h2>
                <p>Specialist Doctors</p>
            </div>
            <div class="stat-item">
                <h2>10k+</h2>
                <p>Happy Patients</p>
            </div>
            <div class="stat-item">
                <h2>24/7</h2>
                <p>Emergency Support</p>
            </div>
        </div>
    </section>

    <section class="services-section">
        <div class="section-header">
            <h2>Our Key Services</h2>
            <p>We provide comprehensive and affordable medical services to ensure your well-being.</p>
        </div>

        <div class="service-container">
            <div class="service-card">
                <div class="icon-box"><i class="fas fa-heartbeat"></i></div>
                <h3>Cardiology</h3>
                <p>Comprehensive care for your heart, from prevention to complex surgeries and rehabilitation.</p>
            </div>

            <div class="service-card">
                <div class="icon-box"><i class="fas fa-ambulance"></i></div>
                <h3>Emergency Care</h3>
                <p>Rapid response teams and fully equipped ambulances ready 24/7 for any medical crisis.</p>
            </div>

            <div class="service-card">
                <div class="icon-box"><i class="fas fa-user-md"></i></div>
                <h3>Expert Consultation</h3>
                <p>Book appointments with top-rated specialists across various departments easily.</p>
            </div>
            <div class="service-card">
                <div class="icon-box"><i class="fas fa-vial"></i></div>
                <h3>Modern Laboratory</h3>
                <p>State-of-the-art diagnostic equipment to provide accurate and quick test results.</p>
            </div>
        </div>
    </section>

    <section class="emergency-banner">
        <div class="banner-content">
            <h3>Need Emergency Assistance?</h3>
            <p>Our dedicated support team is available 24/7 to help you.</p>
        </div>
        <a href="tel:+1234567890" class="phone-btn"><i class="fas fa-phone-alt"></i> +94 112 345 678</a>
    </section>

</div>

<?php include 'footer.php'; ?>