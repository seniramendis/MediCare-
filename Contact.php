<?php
$page_title = "Contact";
include 'header.php';
include 'db_connect.php';

// --- HANDLE FORM SUBMISSION ---
$msg_alert = "";
if (isset($_POST['send_message'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    $sql = "INSERT INTO contact_messages (name, email, phone, message) VALUES ('$name', '$email', '$phone', '$message')";

    if (mysqli_query($conn, $sql)) {
        $msg_alert = "<div class='alert-success'><i class='fas fa-check-circle'></i> Message sent successfully! We will get back to you soon.</div>";
    } else {
        $msg_alert = "<div class='alert-error'>Error sending message. Please try again.</div>";
    }
}
?>

<style>
    /* PAGE HEADER */
    .page-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #eef2ff 100%);
        padding: 80px 0;
        text-align: center;
        margin-bottom: 60px;
    }

    .page-header h1 {
        font-size: 42px;
        color: var(--primary-color);
        font-weight: 800;
        margin-bottom: 10px;
    }

    .page-header p {
        color: var(--text-light);
        font-size: 18px;
        max-width: 600px;
        margin: 0 auto;
    }

    /* LAYOUT CONTAINER */
    .contact-container {
        max-width: 1200px;
        margin: 0 auto 100px auto;
        padding: 0 20px;
        display: grid;
        grid-template-columns: 1fr 1.5fr;
        /* Info takes less space, Form takes more */
        gap: 50px;
    }

    /* LEFT SIDE: INFO CARDS */
    .info-wrapper {
        display: flex;
        flex-direction: column;
        gap: 30px;
    }

    .info-card {
        background: white;
        padding: 30px;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        display: flex;
        align-items: flex-start;
        gap: 20px;
        transition: 0.3s;
        border: 1px solid #f0f0f0;
    }

    .info-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(12, 90, 219, 0.1);
        border-color: rgba(12, 90, 219, 0.2);
    }

    .icon-box {
        width: 50px;
        height: 50px;
        background: rgba(12, 90, 219, 0.1);
        color: var(--primary-color);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }

    .info-content h3 {
        font-size: 18px;
        margin-bottom: 5px;
        color: var(--text-dark);
    }

    .info-content p {
        color: #666;
        font-size: 15px;
        line-height: 1.6;
    }

    /* RIGHT SIDE: FORM */
    .form-wrapper {
        background: white;
        padding: 50px;
        border-radius: 25px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(0, 0, 0, 0.02);
    }

    .form-title {
        font-size: 24px;
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 30px;
    }

    .input-group {
        margin-bottom: 25px;
    }

    .input-group label {
        display: block;
        margin-bottom: 10px;
        font-weight: 600;
        font-size: 14px;
        color: var(--text-dark);
    }

    .form-control {
        width: 100%;
        padding: 15px 20px;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        background: #f9fafb;
        font-size: 15px;
        transition: 0.3s;
        font-family: inherit;
    }

    .form-control:focus {
        border-color: var(--primary-color);
        background: white;
        outline: none;
        box-shadow: 0 0 0 4px rgba(12, 90, 219, 0.1);
    }

    textarea.form-control {
        resize: none;
        height: 150px;
    }

    .btn-submit {
        width: 100%;
        padding: 16px;
        background: var(--primary-color);
        color: white;
        border: none;
        border-radius: 50px;
        font-size: 16px;
        font-weight: 700;
        cursor: pointer;
        transition: 0.3s;
        box-shadow: 0 10px 20px rgba(12, 90, 219, 0.2);
    }

    .btn-submit:hover {
        background: var(--primary-dark);
        transform: translateY(-3px);
    }

    /* ALERTS */
    .alert-success {
        background: #dcfce7;
        color: #16a34a;
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 30px;
        border: 1px solid #bbf7d0;
        font-weight: 500;
    }

    .alert-error {
        background: #fee2e2;
        color: #b91c1c;
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 30px;
        border: 1px solid #fecaca;
        font-weight: 500;
    }

    /* RESPONSIVE */
    @media (max-width: 900px) {
        .contact-container {
            grid-template-columns: 1fr;
        }

        .form-wrapper {
            padding: 30px;
        }
    }
</style>

<div class="page-header">
    <h1>Get in Touch</h1>
    <p>Have questions? We are here to help you 24/7.</p>
</div>

<div class="contact-container">

    <div class="info-wrapper">
        <div class="info-card">
            <div class="icon-box"><i class="fas fa-map-marker-alt"></i></div>
            <div class="info-content">
                <h3>Our Location</h3>
                <p>No 84, Negombo - Colombo Main Rd,<br>Kandana 11320, Sri Lanka</p>
            </div>
        </div>

        <div class="info-card">
            <div class="icon-box"><i class="fas fa-phone-alt"></i></div>
            <div class="info-content">
                <h3>Phone Number</h3>
                <p>Hotline: 1990<br>Office: +94 112 345 678</p>
            </div>
        </div>

        <div class="info-card">
            <div class="icon-box"><i class="fas fa-envelope"></i></div>
            <div class="info-content">
                <h3>Email Address</h3>
                <p>info@medicareplus.com<br>support@medicareplus.com</p>
            </div>
        </div>

        <img src="https://images.unsplash.com/photo-1516387938699-a93567ec168e?q=80&w=2071&auto=format&fit=crop"
            style="width:100%; height:250px; object-fit:cover; border-radius:20px; margin-top:20px;"
            alt="Support Team">
    </div>

    <div class="form-wrapper">
        <h2 class="form-title">Send us a Message</h2>

        <?php echo $msg_alert; ?>

        <form method="POST">
            <div class="input-group">
                <label>Full Name</label>
                <input type="text" name="name" class="form-control" placeholder="Jehan Fernando" required>
            </div>

            <div class="input-group">
                <label>Email Address</label>
                <input type="email" name="email" class="form-control" placeholder="jehan@example.com" required>
            </div>

            <div class="input-group">
                <label>Phone Number</label>
                <input type="tel" name="phone" class="form-control" placeholder="+94 7X XXX XXXX" required>
            </div>

            <div class="input-group">
                <label>Your Message</label>
                <textarea name="message" class="form-control" placeholder="How can we help you today?" required></textarea>
            </div>

            <button type="submit" name="send_message" class="btn-submit">Submit Request</button>
        </form>
    </div>

</div>

<?php include 'footer.php'; ?>