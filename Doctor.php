<?php
$page_title = "Doctors";
include 'header.php';
include 'db_connect.php'; // Ensure database connection is active

// --- FETCH DOCTORS FROM DATABASE ---
$sql = "SELECT * FROM doctors";
$result = mysqli_query($conn, $sql);
?>

<style>
    /* Page Header */
    .page-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #eef2ff 100%);
        padding: 60px 0;
        text-align: center;
        margin-bottom: 50px;
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
    }

    /* Grid Layout */
    .doctors-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 30px;
        max-width: 1200px;
        margin: 0 auto 80px auto;
        padding: 0 20px;
    }

    /* Card Styling */
    .doc-card {
        background: var(--white);
        padding: 30px 20px;
        border-radius: 20px;
        text-align: center;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        transition: 0.3s;
        border: 1px solid rgba(0, 0, 0, 0.03);
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .doc-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(12, 90, 219, 0.15);
    }

    .doc-card img {
        width: 110px;
        height: 110px;
        object-fit: cover;
        border-radius: 50%;
        margin-bottom: 15px;
        border: 4px solid #eef2ff;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .doc-card h3 {
        font-size: 20px;
        color: var(--text-dark);
        margin-bottom: 5px;
        font-weight: 700;
    }

    .doc-card span {
        color: var(--primary-color);
        font-weight: 600;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        background: rgba(12, 90, 219, 0.05);
        padding: 5px 15px;
        border-radius: 50px;
        margin-bottom: 20px;
        display: inline-block;
    }

    /* Buttons Container */
    .card-btns {
        display: flex;
        gap: 10px;
        width: 100%;
        margin-top: auto;
    }

    .btn-action {
        flex: 1;
        padding: 10px 0;
        border-radius: 50px;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        transition: 0.3s;
        border: 2px solid transparent;
        text-align: center;
    }

    /* Primary Button (Book) */
    .btn-book {
        background: var(--primary-color);
        color: white;
        box-shadow: 0 4px 10px rgba(12, 90, 219, 0.3);
    }

    .btn-book:hover {
        background: var(--primary-dark);
        transform: translateY(-2px);
    }

    /* Secondary Button (Profile) */
    .btn-profile {
        background: transparent;
        border-color: var(--primary-color);
        color: var(--primary-color);
    }

    .btn-profile:hover {
        background: var(--primary-color);
        color: white;
    }
</style>

<div class="page-header">
    <h1>Meet Our Specialists</h1>
    <p>Experienced professionals dedicated to your health and well-being.</p>
</div>

<div class="doctors-grid">

    <?php
    if (mysqli_num_rows($result) > 0) {
        while ($doc = mysqli_fetch_assoc($result)) {
    ?>
            <div class="doc-card">
                <img src="<?php echo htmlspecialchars($doc['image']); ?>" alt="<?php echo htmlspecialchars($doc['name']); ?>">
                <h3><?php echo htmlspecialchars($doc['name']); ?></h3>
                <span><?php echo htmlspecialchars($doc['specialty']); ?></span>

                <div class="card-btns">
                    <a href="doctor_profile.php?id=<?php echo $doc['id']; ?>" class="btn-action btn-profile">View Profile</a>

                    <a href="book_appointment.php" class="btn-action btn-book">Book Now</a>
                </div>
            </div>
    <?php
        }
    } else {
        echo "<p style='text-align:center; width:100%;'>No doctors found in database.</p>";
    }
    ?>

</div>

<?php include 'footer.php'; ?>