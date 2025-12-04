<?php
$page_title = "Blog and Tips";
include 'header.php';
include 'blog_data.php'; // Pulls the data from the file above
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

    /* CONTAINER & GRID */
    .blog-container {
        max-width: 1200px;
        margin: 0 auto 80px auto;
        padding: 0 20px;
    }

    .blog-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
        gap: 40px;
    }

    /* BLOG CARD */
    .blog-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        transition: 0.4s ease;
        border: 1px solid rgba(0, 0, 0, 0.03);
        display: flex;
        flex-direction: column;
    }

    .blog-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(12, 90, 219, 0.15);
    }

    /* IMAGE AREA */
    .blog-img-wrapper {
        position: relative;
        overflow: hidden;
        height: 240px;
    }

    .blog-img-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .blog-card:hover .blog-img-wrapper img {
        transform: scale(1.1);
    }

    .category-badge {
        position: absolute;
        top: 20px;
        left: 20px;
        background: var(--primary-color);
        color: white;
        padding: 6px 14px;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        z-index: 2;
    }

    /* CONTENT AREA */
    .blog-content {
        padding: 30px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .blog-meta {
        font-size: 13px;
        color: #999;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .blog-meta i {
        color: var(--primary-color);
    }

    .blog-title {
        font-size: 22px;
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 15px;
        line-height: 1.4;
    }

    .blog-excerpt {
        font-size: 15px;
        color: var(--text-light);
        line-height: 1.6;
        margin-bottom: 25px;
        flex: 1;
    }

    .read-more-btn {
        color: var(--primary-color);
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: 0.3s;
        font-size: 15px;
    }

    .read-more-btn:hover {
        gap: 12px;
        color: var(--primary-dark);
    }

    /* NEWSLETTER SECTION */
    .newsletter-section {
        background: var(--text-dark);
        color: white;
        padding: 60px 20px;
        margin-top: 80px;
        border-radius: 20px;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .newsletter-content {
        position: relative;
        z-index: 2;
    }

    .newsletter-section h2 {
        font-size: 32px;
        margin-bottom: 10px;
    }

    .newsletter-section p {
        opacity: 0.8;
        margin-bottom: 30px;
    }

    .newsletter-form {
        display: flex;
        justify-content: center;
        gap: 10px;
        max-width: 500px;
        margin: 0 auto;
    }

    .email-input {
        flex: 1;
        padding: 15px 20px;
        border-radius: 50px;
        border: none;
        outline: none;
        font-family: inherit;
    }

    .subscribe-btn {
        background: var(--primary-color);
        color: white;
        border: none;
        padding: 15px 30px;
        border-radius: 50px;
        font-weight: 700;
        cursor: pointer;
        transition: 0.3s;
    }

    .subscribe-btn:hover {
        background: white;
        color: var(--primary-color);
    }

    @media (max-width: 768px) {
        .newsletter-form {
            flex-direction: column;
        }

        .subscribe-btn {
            width: 100%;
        }
    }
</style>

<div class="page-header">
    <h1>Health & Wellness Blog</h1>
    <p>Expert advice, latest news, and tips for a healthier you.</p>
</div>

<div class="blog-container">
    <div class="blog-grid">
        <?php foreach ($posts as $id => $post): ?>
            <div class="blog-card">
                <div class="blog-img-wrapper">
                    <span class="category-badge"><?php echo $post['category']; ?></span>
                    <img src="<?php echo $post['image']; ?>" alt="<?php echo $post['title']; ?>">
                </div>

                <div class="blog-content">
                    <div class="blog-meta">
                        <span><i class="far fa-calendar-alt"></i> <?php echo $post['date']; ?></span>
                        <span>•</span>
                        <span>5 min read</span>
                    </div>

                    <h3 class="blog-title"><?php echo $post['title']; ?></h3>
                    <p class="blog-excerpt"><?php echo $post['excerpt']; ?></p>

                    <a href="article.php?id=<?php echo $id; ?>" class="read-more-btn">Read Article <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="newsletter-section">
        <div class="newsletter-content">
            <h2>Stay Healthy, Stay Updated</h2>
            <p>Subscribe to our weekly newsletter for the latest health tips and hospital news.</p>
            <form class="newsletter-form">
                <input type="email" class="email-input" placeholder="Enter your email address">
                <button type="button" class="subscribe-btn">Subscribe</button>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>