<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db_connect.php';


if (isset($_GET['id'])) {
    $doctor_id = mysqli_real_escape_string($conn, $_GET['id']);

    $sql = "SELECT * FROM doctors WHERE id = '$doctor_id'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $doctor = mysqli_fetch_assoc($result);
        $page_title = $doctor['name'];
    } else {
        $page_title = "Doctor Not Found";
        include 'header.php';
        echo "<h2 style='text-align:center; padding:50px; color:#ef4444;'>Doctor not found!</h2>";
        include 'footer.php';
        exit();
    }
} else {
    header("Location: Doctor.php");
    exit();
}

include 'header.php';


$msg = "";
if (isset($_POST['submit_review'])) {
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $user_name = isset($_SESSION['username']) ? $_SESSION['username'] : 'User';
        $rating = $_POST['rating'];
        $review_text = mysqli_real_escape_string($conn, $_POST['review']);

        $insert_sql = "INSERT INTO reviews (doctor_id, user_id, user_name, rating, review_text) 
                       VALUES ('$doctor_id', '$user_id', '$user_name', '$rating', '$review_text')";

        if (mysqli_query($conn, $insert_sql)) {
            $msg = "<div class='alert-success'><i class='fas fa-check-circle'></i> Review submitted successfully!</div>";
            echo "<meta http-equiv='refresh' content='2'>";
        } else {
            $msg = "<div class='alert-error'>Error submitting review.</div>";
        }
    } else {
        echo "<script>window.location.href='login.php';</script>";
        exit();
    }
}


$reviews_sql = "SELECT * FROM reviews WHERE doctor_id = '$doctor_id' ORDER BY created_at DESC";
$reviews_result = mysqli_query($conn, $reviews_sql);
?>

<style>
    body {
        background-color: #f9fbfd;
    }


    .profile-header-bg {
        background: linear-gradient(135deg, #0c5adb 0%, #062f75 100%);
        height: 300px;
        width: 100%;
        border-radius: 0 0 50% 50% / 20px;
        position: absolute;
        top: 0;
        left: 0;
        z-index: -1;
    }


    .profile-wrapper {
        max-width: 1100px;
        margin: 120px auto 60px auto;
        background: white;
        border-radius: 20px;
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.08);
        padding: 0;
        display: grid;
        grid-template-columns: 350px 1fr;
        overflow: hidden;
    }


    .profile-sidebar {
        background: #f8faff;
        text-align: center;
        padding: 50px 30px;
        border-right: 1px solid #edf2f9;
    }

    .doc-img {
        width: 180px;
        height: 180px;
        object-fit: cover;
        border-radius: 50%;
        border: 6px solid white;
        box-shadow: 0 10px 25px rgba(12, 90, 219, 0.2);
        margin-bottom: 20px;
    }

    .doc-name {
        font-size: 24px;
        font-weight: 800;
        color: var(--text-dark);
        margin-bottom: 5px;
    }

    .doc-specialty {
        display: inline-block;
        background: rgba(12, 90, 219, 0.1);
        color: var(--primary-color);
        padding: 6px 15px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 14px;
        margin-bottom: 30px;
    }

    .info-list {
        list-style: none;
        text-align: left;
        padding: 0 20px;
    }

    .info-list li {
        margin-bottom: 18px;
        font-size: 15px;
        color: #555;
        display: flex;
        align-items: flex-start;
        gap: 15px;
    }

    .info-list i {
        color: var(--primary-color);
        font-size: 18px;
        min-width: 20px;
        margin-top: 3px;
    }

    /* BUTTONS GROUP */
    .sidebar-btns {
        margin-top: 30px;
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .btn-book {
        display: block;
        width: 100%;
        padding: 15px;
        background: var(--primary-color);
        color: white;
        text-decoration: none;
        border-radius: 12px;
        font-weight: 700;
        box-shadow: 0 8px 20px rgba(12, 90, 219, 0.25);
        transition: 0.3s;
    }

    .btn-book:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 25px rgba(12, 90, 219, 0.35);
    }


    .btn-msg {
        display: block;
        width: 100%;
        padding: 14px;
        background: white;
        color: var(--primary-color);
        text-decoration: none;
        border: 2px solid var(--primary-color);
        border-radius: 12px;
        font-weight: 700;
        transition: 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        cursor: pointer;
    }

    .btn-msg:hover {
        background: #eef4ff;
        transform: translateY(-3px);
    }


    .btn-msg.login-req {
        border-color: #9ca3af;
        color: #6b7280;
    }

    .btn-msg.login-req:hover {
        background: #f3f4f6;
        color: var(--primary-color);
        border-color: var(--primary-color);
    }


    .profile-content {
        padding: 50px;
    }

    .section-title {
        font-size: 18px;
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .section-title::after {
        content: '';
        flex: 1;
        height: 1px;
        background: #e0e0e0;
        margin-left: 10px;
    }

    .bio-text {
        font-size: 16px;
        line-height: 1.8;
        color: #666;
        margin-bottom: 40px;
    }


    .reviews-container {
        margin-top: 50px;
    }

    .review-form-box {
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 40px;
    }

    .rating-wrapper {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 15px;
    }

    .rating-label {
        font-weight: 600;
        font-size: 15px;
    }

    .rate {
        float: left;
        height: 46px;
    }

    .rate:not(:checked)>input {
        position: absolute;
        top: -9999px;
    }

    .rate:not(:checked)>label {
        float: right;
        width: 1em;
        overflow: hidden;
        white-space: nowrap;
        cursor: pointer;
        font-size: 28px;
        color: #ddd;
    }

    .rate:not(:checked)>label:before {
        content: '★ ';
    }

    .rate>input:checked~label {
        color: #ffc107;
    }

    .rate>input:checked+label:hover,
    .rate>input:checked+label:hover~label {
        color: #c59b08;
    }

    textarea.review-input {
        width: 100%;
        padding: 15px;
        border: 1px solid #ddd;
        border-radius: 10px;
        resize: none;
        height: 120px;
        font-family: inherit;
        font-size: 15px;
        transition: 0.3s;
    }

    textarea.review-input:focus {
        border-color: var(--primary-color);
        outline: none;
    }

    .submit-btn {
        background: #111;
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 50px;
        cursor: pointer;
        font-weight: 600;
        margin-top: 15px;
        float: right;
    }

    .submit-btn:hover {
        background: #333;
    }

    .review-card {
        background: white;
        border-bottom: 1px solid #eee;
        padding: 25px 0;
        display: flex;
        gap: 20px;
    }

    .review-card:last-child {
        border-bottom: none;
    }

    .reviewer-avatar {
        width: 50px;
        height: 50px;
        background: #f0f2f5;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        color: #555;
        font-size: 20px;
    }

    .review-content {
        flex: 1;
    }

    .reviewer-name {
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 2px;
    }

    .review-meta {
        font-size: 12px;
        color: #999;
        margin-bottom: 8px;
    }

    .review-stars {
        color: #ffc107;
        font-size: 13px;
        margin-bottom: 8px;
    }

    .review-body {
        color: #555;
        line-height: 1.6;
        font-size: 15px;
    }

    .alert-success {
        background: #dcfce7;
        color: #16a34a;
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 20px;
        font-weight: 500;
    }

    .alert-error {
        background: #fee2e2;
        color: #b91c1c;
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 20px;
    }

    @media (max-width: 900px) {
        .profile-wrapper {
            grid-template-columns: 1fr;
            margin-top: 40px;
        }

        .profile-sidebar {
            border-right: none;
            border-bottom: 1px solid #eee;
        }
    }
</style>

<div class="profile-header-bg"></div>

<div class="profile-wrapper">
    <div class="profile-sidebar">
        <img src="<?php echo htmlspecialchars($doctor['image']); ?>" alt="Doctor" class="doc-img">
        <h1 class="doc-name"><?php echo htmlspecialchars($doctor['name']); ?></h1>
        <span class="doc-specialty"><?php echo htmlspecialchars($doctor['specialty']); ?></span>

        <ul class="info-list">
            <li><i class="fas fa-graduation-cap"></i> <?php echo htmlspecialchars($doctor['qualification'] ?? 'MBBS'); ?></li>
            <li><i class="fas fa-briefcase"></i> <?php echo htmlspecialchars($doctor['experience'] ?? '5'); ?>+ Years Experience</li>
            <li><i class="fas fa-map-marker-alt"></i> MediCare+ Main Hospital</li>
            <li><i class="fas fa-language"></i> English, Sinhala</li>
        </ul>

        <div class="sidebar-btns">
            <a href="book_appointment.php" class="btn-book">Book Appointment</a>

            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="inbox.php?doctor_id=<?php echo $doctor['id']; ?>" class="btn-msg">
                    <i class="far fa-comments"></i> Message Doctor
                </a>
            <?php else: ?>
                <a href="login.php" class="btn-msg login-req" onclick="return confirm('You must log in first to message the doctor.');">
                    <i class="fas fa-lock"></i> Login to Message
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="profile-content">
        <div class="section-title"><i class="far fa-user"></i> About Doctor</div>
        <p class="bio-text"><?php echo nl2br(htmlspecialchars($doctor['bio'] ?? 'Experienced specialist dedicated to patient care.')); ?></p>

        <div class="section-title"><i class="far fa-clock"></i> Availability</div>
        <div style="display: flex; gap: 15px; flex-wrap: wrap; margin-bottom: 40px;">
            <span style="background:#f0f7ff; color:#0c5adb; padding:10px 20px; border-radius:10px; font-size:14px; font-weight:600; border:1px solid #dceeff;">Mon - Fri: 4:00 PM - 8:00 PM</span>
            <span style="background:#f0fff4; color:#16a34a; padding:10px 20px; border-radius:10px; font-size:14px; font-weight:600; border:1px solid #dcfce7;">Sat: 9:00 AM - 1:00 PM</span>
        </div>

        <div class="section-title"><i class="far fa-comment-alt"></i> Patient Reviews</div>
        <div class="reviews-container">
            <?php echo $msg; ?>

            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="review-form-box">
                    <form method="POST">
                        <div class="rating-wrapper">
                            <span class="rating-label">How was your experience?</span>
                            <div class="rate">
                                <input type="radio" id="star5" name="rating" value="5" required />
                                <label for="star5" title="Excellent">5 stars</label>
                                <input type="radio" id="star4" name="rating" value="4" />
                                <label for="star4" title="Good">4 stars</label>
                                <input type="radio" id="star3" name="rating" value="3" />
                                <label for="star3" title="Average">3 stars</label>
                                <input type="radio" id="star2" name="rating" value="2" />
                                <label for="star2" title="Poor">2 stars</label>
                                <input type="radio" id="star1" name="rating" value="1" />
                                <label for="star1" title="Very Poor">1 star</label>
                            </div>
                        </div>
                        <textarea name="review" class="review-input" placeholder="Write your feedback here..." required></textarea>
                        <button type="submit" name="submit_review" class="submit-btn">Submit Review</button>
                        <div style="clear:both;"></div>
                    </form>
                </div>
            <?php else: ?>
                <div style="background: #fff3cd; color: #856404; padding: 20px; border-radius: 12px; text-align: center; margin-bottom: 30px; border: 1px solid #ffeeba;">
                    <i class="fas fa-lock"></i> Please <a href="login.php" style="color:#856404; font-weight:bold; text-decoration:underline;">Login</a> to write a review.
                </div>
            <?php endif; ?>

            <?php if (mysqli_num_rows($reviews_result) > 0): ?>
                <?php while ($review = mysqli_fetch_assoc($reviews_result)): ?>
                    <div class="review-card">
                        <div class="reviewer-avatar">
                            <?php echo strtoupper(substr($review['user_name'], 0, 1)); ?>
                        </div>
                        <div class="review-content">
                            <div class="reviewer-name"><?php echo htmlspecialchars($review['user_name']); ?></div>
                            <div class="review-meta"><?php echo date('F d, Y', strtotime($review['created_at'])); ?></div>
                            <div class="review-stars">
                                <?php for ($i = 0; $i < 5; $i++) echo ($i < $review['rating']) ? '<i class="fas fa-star"></i>' : '<i class="far fa-star" style="color:#ddd;"></i>'; ?>
                            </div>
                            <div class="review-body"><?php echo htmlspecialchars($review['review_text']); ?></div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="text-align: center; padding: 30px; color: #999;">
                    <i class="far fa-comment-dots" style="font-size: 40px; margin-bottom: 10px; display:block;"></i>
                    No reviews yet. Be the first to share your experience!
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>