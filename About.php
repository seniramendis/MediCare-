<?php
// SET THE BROWSER TAB TITLE HERE
$page_title = "About Us";
include 'header.php';
?>

<style>
    /* ===== PAGE HEADER ===== */
    .page-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #eef2ff 100%);
        padding: 80px 0;
        text-align: center;
        margin-bottom: 60px;
    }

    .page-header h1 {
        font-size: 48px;
        color: var(--primary-color);
        font-weight: 800;
        margin-bottom: 15px;
        letter-spacing: -1px;
    }

    .page-header p {
        color: var(--text-light);
        font-size: 20px;
        max-width: 600px;
        margin: 0 auto;
    }

    /* ===== MAIN CONTENT ===== */
    .about-section {
        max-width: 1200px;
        margin: 0 auto 100px auto;
        padding: 0 20px;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 80px;
        align-items: center;
    }

    .about-content h2 {
        font-size: 36px;
        margin-bottom: 25px;
        color: var(--text-dark);
        line-height: 1.2;
    }

    .about-content p {
        color: var(--text-light);
        margin-bottom: 30px;
        line-height: 1.8;
        font-size: 16px;
    }

    /* IMAGE STYLING (API IMAGE) */
    .about-image {
        position: relative;
    }

    .about-image img {
        width: 100%;
        border-radius: 20px;
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
        object-fit: cover;
        height: 500px;
        transition: transform 0.3s ease;
    }

    .about-image:hover img {
        transform: scale(1.02);
    }

    /* FEATURES GRID */
    .feature-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }

    .feature-item {
        background: #fff;
        padding: 15px;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .feature-item i {
        color: var(--success-color, #10b981);
        background: rgba(16, 185, 129, 0.1);
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .feature-item span {
        font-weight: 600;
        color: var(--text-dark);
        font-size: 15px;
    }

    /* ===== LOCATION & MAP SECTION ===== */
    .location-wrapper {
        max-width: 1200px;
        margin: 0 auto 80px auto;
        padding: 0 20px;
        display: grid;
        grid-template-columns: 1fr 2fr;
        gap: 0;
        border-radius: 25px;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
    }

    .location-info {
        background: var(--primary-color);
        color: white;
        padding: 50px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .info-box {
        margin-bottom: 30px;
    }

    .info-box h4 {
        font-size: 18px;
        opacity: 0.9;
        margin-bottom: 5px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .info-box p {
        font-size: 16px;
        font-weight: 600;
        margin-left: 28px;
    }

    /* MAP */
    .map-container iframe {
        width: 100%;
        height: 100%;
        min-height: 500px;
        border: 0;
    }

    /* GET DIRECTIONS BUTTON */
    .btn-direction {
        background: white;
        color: var(--primary-color);
        padding: 15px 30px;
        border-radius: 50px;
        font-weight: 700;
        font-size: 16px;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        transition: 0.3s;
        margin-top: 20px;
        text-decoration: none;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    }

    .btn-direction:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
    }

    @media (max-width: 900px) {

        .about-section,
        .location-wrapper {
            grid-template-columns: 1fr;
        }

        .about-image {
            order: -1;
            margin-bottom: 30px;
        }
    }
</style>

<div class="page-header">
    <h1>About MediCare+</h1>
    <p>Innovating Healthcare, Dedicating Compassion.</p>
</div>

<div class="about-section">
    <div class="about-content">
        <h2>World-Class Care <br>For Your Family</h2>
        <p>MediCare+ was established with a singular vision: to bring world-class healthcare within reach of everyone. Over the last 15 years, we have grown from a small clinic to a multi-specialty hospital, driven by our commitment to patient safety and advanced technology.</p>

        <div class="feature-grid">
            <div class="feature-item"><i class="fas fa-user-md"></i> <span>Expert Specialists</span></div>
            <div class="feature-item"><i class="fas fa-microscope"></i> <span>Advanced Labs</span></div>
            <div class="feature-item"><i class="fas fa-ambulance"></i> <span>24/7 Ambulance</span></div>
            <div class="feature-item"><i class="fas fa-clock"></i> <span>Emergency Care</span></div>
            <div class="feature-item"><i class="fas fa-heartbeat"></i> <span>Modern OT</span></div>
            <div class="feature-item"><i class="fas fa-wifi"></i> <span>Digital Records</span></div>
        </div>
    </div>

    <div class="about-image">
        <img src="https://images.pexels.com/photos/15194940/pexels-photo-15194940.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1" alt="Hospital Interior">
    </div>
</div>

<div class="location-wrapper">
    <div class="location-info">
        <h3>Visit Our Center</h3>
        <br>
        <div class="info-box">
            <h4><i class="fas fa-map-marker-alt"></i> Address</h4>
            <p>No 84, Negombo - Colombo Main Rd,<br> Kandana 11320</p>
        </div>
        <div class="info-box">
            <h4><i class="fas fa-phone-alt"></i> Call Us</h4>
            <p>+94 112 345 678</p>
        </div>
        <div class="info-box">
            <h4><i class="fas fa-clock"></i> Hours</h4>
            <p>Open 24 Hours / 7 Days</p>
        </div>

        <button onclick="getDirections()" class="btn-direction">
            <i class="fas fa-location-arrow"></i> Get Directions
        </button>
        <p id="geo-error" style="color: #ffcccb; font-size: 13px; margin-top: 15px; display:none;"></p>
    </div>

    <div class="map-container">
        <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3959.943183594611!2d79.8945169147743!3d7.045024594910245!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae2f7630dcdfd3d%3A0x8037bdcd4c74ea82!2sNo%2084%2C%20Negombo%20-%20Colombo%20Main%20Rd%2C%20Kandana!5e0!3m2!1sen!2slk!4v1646835281923!5m2!1sen!2slk"
            allowfullscreen=""
            loading="lazy">
        </iframe>
    </div>
</div>

<script>
    function getDirections() {
        const errorMsg = document.getElementById('geo-error');

        // 1. UPDATED COORDINATES FOR KANDANA (No 84)
        const hospitalLat = 7.0450;
        const hospitalLng = 79.8971;

        if (navigator.geolocation) {
            errorMsg.style.display = 'none';
            navigator.geolocation.getCurrentPosition(showRoute, showError);
        } else {
            errorMsg.style.display = 'block';
            errorMsg.innerHTML = "Geolocation is not supported by your browser.";
        }

        function showRoute(position) {
            const userLat = position.coords.latitude;
            const userLng = position.coords.longitude;

            // Open Google Maps with route
            const mapUrl = `https://www.google.com/maps/dir/?api=1&origin=${userLat},${userLng}&destination=${hospitalLat},${hospitalLng}&travelmode=driving`;
            window.open(mapUrl, '_blank');
        }

        function showError(error) {
            errorMsg.style.display = 'block';
            if (error.code === error.PERMISSION_DENIED) {
                errorMsg.innerHTML = "Please allow location access to get directions.";
            } else {
                errorMsg.innerHTML = "Unable to retrieve your location.";
            }
        }
    }
</script>

<?php include 'footer.php'; ?>