<?php
// 1. Load Data FIRST (Before header)
include 'blog_data.php';

// 2. Check ID and Get Article Details
if (isset($_GET['id']) && isset($posts[$_GET['id']])) {
    $id = $_GET['id'];
    $article = $posts[$id];

    // 3. ASSIGN THE ARTICLE NAME TO THE PAGE TITLE
    $page_title = $article['title'];
} else {
    // If article doesn't exist, go back to Blog
    echo "<script>window.location.href='Blog.php';</script>";
    exit();
}

// 4. NOW include the header (It will use the $page_title we just set)
include 'header.php';
?>

<style>
    /* SINGLE POST STYLES */
    .article-container {
        max-width: 800px;
        margin: 0 auto 80px auto;
        padding: 0 20px;
    }

    .article-header {
        text-align: center;
        margin-bottom: 40px;
        margin-top: 60px;
    }

    .article-meta {
        color: #666;
        font-size: 14px;
        margin-bottom: 15px;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-weight: 600;
    }

    .article-meta span {
        color: var(--primary-color);
    }

    .article-title {
        font-size: 40px;
        font-weight: 800;
        color: var(--text-dark);
        line-height: 1.2;
        margin-bottom: 30px;
    }

    .article-image {
        width: 100%;
        height: 450px;
        object-fit: cover;
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        margin-bottom: 50px;
    }

    .article-body {
        font-size: 18px;
        line-height: 1.8;
        color: #444;
    }

    /* Typography inside the article body */
    .article-body h3 {
        font-size: 26px;
        color: var(--text-dark);
        margin-top: 40px;
        margin-bottom: 15px;
    }

    .article-body h4 {
        font-size: 22px;
        color: var(--text-dark);
        margin-top: 30px;
        margin-bottom: 10px;
    }

    .article-body p {
        margin-bottom: 20px;
    }

    .article-body ul {
        margin-bottom: 20px;
        padding-left: 20px;
    }

    .article-body li {
        margin-bottom: 10px;
    }

    .btn-back {
        display: inline-block;
        margin-top: 50px;
        text-decoration: none;
        color: var(--text-dark);
        font-weight: 600;
        border: 1px solid #ddd;
        padding: 10px 25px;
        border-radius: 50px;
        transition: 0.3s;
    }

    .btn-back:hover {
        background: var(--text-dark);
        color: white;
        border-color: var(--text-dark);
    }
</style>

<div class="article-container">

    <div class="article-header">
        <div class="article-meta">
            <span><?php echo $article['category']; ?></span> &nbsp;/&nbsp; <?php echo $article['date']; ?>
        </div>
        <h1 class="article-title"><?php echo $article['title']; ?></h1>
    </div>

    <img src="<?php echo $article['image']; ?>" alt="Article Image" class="article-image">

    <div class="article-body">
        <?php echo $article['content']; ?>
    </div>

    <a href="Blog.php" class="btn-back">← Back to Blog</a>

</div>

<?php include 'footer.php'; ?>