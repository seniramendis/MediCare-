<?php
$page_title = "Reviews";
include 'header.php';
include 'db_connect.php';

// --- HANDLE REVIEW SUBMISSION ---
$msg = "";
if (isset($_POST['submit_review'])) {
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $user_name = $_SESSION['username'];
        $rating = $_POST['rating'];
        $review_text = mysqli_real_escape_string($conn, $_POST['review']);

        $sql = "INSERT INTO hospital_reviews (user_id, user_name, rating, review_text) 
                VALUES ('$user_id', '$user_name', '$rating', '$review_text')";

        if (mysqli_query($conn, $sql)) {
            $msg = "<div class='alert-success'>Thank you! Your review has been posted.</div>";
            // Refresh to show new review immediately
            echo "<meta http-equiv='refresh' content='2'>";
        } else {
            $msg = "<div class='alert-error'>Error: " . mysqli_error($conn) . "</div>";
        }
    } else {
        header("Location: login.php");
    }
}

// --- FETCH REVIEWS ---
$sql_fetch = "SELECT * FROM hospital_reviews ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql_fetch);
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

    /* CONTAINER */
    .review-container {
        max-width: 1200px;
        margin: 0 auto 80px auto;
        padding: 0 20px;
    }

    /* FORM SECTION */
    .form-section {
        max-width: 700px;
        margin: 0 auto 60px auto;
        background: white;
        padding: 40px;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        border: 1px solid #f0f0f0;
        text-align: center;
    }

    /* STAR RATING CSS */
    .rate {
        display: inline-block;
        height: 46px;
        padding: 0 10px;
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
        font-size: 30px;
        color: #ccc;
    }

    .rate:not(:checked)>label:before {
        content: '★ ';
    }

    .rate>input:checked~label {
        color: #ffc700;
    }

    .rate:not(:checked)>label:hover,
    .rate:not(:checked)>label:hover~label {
        color: #deb217;
    }

    .rate>input:checked+label:hover,
    .rate>input:checked+label:hover~label,
    .rate>input:checked~label:hover,
    .rate>input:checked~label:hover~label,
    .rate>label:hover~input:checked~label {
        color: #c59b08;
    }

    textarea {
        width: 100%;
        padding: 15px;
        border: 1px solid #ddd;
        border-radius: 10px;
        resize: none;
        height: 100px;
        margin-top: 20px;
        font-family: inherit;
        font-size: 15px;
        background: #f9f9f9;
        transition: 0.3s;
    }

    textarea:focus {
        background: white;
        border-color: var(--primary-color);
        outline: none;
    }

    .btn-submit {
        background: var(--primary-color);
        color: white;
        border: none;
        padding: 12px 40px;
        border-radius: 50px;
        cursor: pointer;
        font-weight: 600;
        margin-top: 20px;
        transition: 0.3s;
    }

    .btn-submit:hover {
        background: var(--primary-dark);
        transform: translateY(-3px);
    }

    /* REVIEW GRID */
    .review-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 30px;
    }

    .review-card {
        background: var(--white);
        padding: 30px;
        border-radius: 15px;
        box-shadow: var(--shadow-soft);
        border: 1px solid rgba(0, 0, 0, 0.03);
        transition: 0.3s;
        position: relative;
    }

    .review-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
    }

    /* Quote Icon */
    .review-card::before {
        content: '“';
        position: absolute;
        top: 20px;
        right: 20px;
        font-size: 80px;
        color: rgba(12, 90, 219, 0.05);
        font-family: serif;
        line-height: 0;
    }

    /* User Info */
    .user-info {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 15px;
    }

    .user-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        object-fit: cover;
        background: #eef2ff;
        color: var(--primary-color);
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        border: 2px solid white;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    }

    .user-text h4 {
        font-size: 16px;
        font-weight: 700;
        color: var(--text-dark);
        margin: 0;
    }

    .user-text span {
        font-size: 12px;
        color: #999;
    }

    .stars {
        color: #ffc700;
        font-size: 14px;
        margin-bottom: 15px;
    }

    .review-text {
        color: var(--text-light);
        font-size: 15px;
        line-height: 1.6;
        font-style: italic;
    }

    /* Alerts */
    .alert-success {
        background: #dcfce7;
        color: #16a34a;
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 20px;
    }

    .alert-error {
        background: #fee2e2;
        color: #b91c1c;
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 20px;
    }
</style>

<div class="page-header">
    <h1>What Our Patients Say</h1>
    <p>Real stories from our community about their experience at MediCare+.</p>
</div>

<div class="review-container">

    <div class="form-section">
        <?php echo $msg; ?>

        <?php if (isset($_SESSION['user_id'])): ?>
            <h3 style="margin-bottom: 20px; color: var(--text-dark);">Share Your Experience</h3>
            <form method="POST">
                <div class="rate">
                    <input type="radio" id="star5" name="rating" value="5" required />
                    <label for="star5" title="text">5 stars</label>
                    <input type="radio" id="star4" name="rating" value="4" />
                    <label for="star4" title="text">4 stars</label>
                    <input type="radio" id="star3" name="rating" value="3" />
                    <label for="star3" title="text">3 stars</label>
                    <input type="radio" id="star2" name="rating" value="2" />
                    <label for="star2" title="text">2 stars</label>
                    <input type="radio" id="star1" name="rating" value="1" />
                    <label for="star1" title="text">1 star</label>
                </div>
                <br>
                <textarea name="review" placeholder="Write your feedback about our hospital facilities and service..." required></textarea>
                <button type="submit" name="submit_review" class="btn-submit">Post Review</button>
            </form>
        <?php else: ?>
            <div style="padding: 20px; background: #fff3cd; border-radius: 10px; color: #856404;">
                <i class="fas fa-lock"></i> Please <a href="login.php" style="color: var(--primary-color); font-weight:bold; text-decoration:underline;">Login</a> to write a review.
            </div>
        <?php endif; ?>
    </div>

    <div class="review-grid">
        <?php
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
        ?>
                <div class="review-card">
                    <div class="user-info">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($row['user_name']); ?>&background=0c5adb&color=fff" class="user-avatar" alt="User">
                        <div class="user-text">
                            <h4><?php echo htmlspecialchars($row['user_name']); ?></h4>
                            <span><?php echo date('M d, Y', strtotime($row['created_at'])); ?></span>
                        </div>
                    </div>

                    <div class="stars">
                        <?php
                        for ($i = 0; $i < 5; $i++) {
                            echo ($i < $row['rating']) ? '<i class="fas fa-star"></i>' : '<i class="far fa-star" style="color:#ddd;"></i>';
                        }
                        ?>
                    </div>

                    <p class="review-text">"<?php echo htmlspecialchars($row['review_text']); ?>"</p>
                </div>
        <?php
            }
        } else {
            echo "<p style='text-align:center; width:100%; color:#777;'>No reviews yet. Be the first to review us!</p>";
        }
        ?>
    </div>

</div>

<?php include 'footer.php'; ?>