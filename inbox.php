<?php
$page_title = "Messages";
session_start();
include 'db_connect.php';

// 1. SECURITY CHECK (Prevents access if not logged in)
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='login.php';</script>";
    exit();
}

$user_id = $_SESSION['user_id'];
$selected_doctor_id = isset($_GET['doctor_id']) ? intval($_GET['doctor_id']) : null;

// --- HANDLE SEND MESSAGE ---
if (isset($_POST['send_msg']) && $selected_doctor_id && !empty(trim($_POST['message']))) {
    $msg = mysqli_real_escape_string($conn, $_POST['message']);
    mysqli_query($conn, "INSERT INTO messages (user_id, doctor_id, sender, message) VALUES ('$user_id', '$selected_doctor_id', 'patient', '$msg')");

    // FIX: JavaScript redirect prevents "Headers already sent" error
    echo "<script>window.location.href='inbox.php?doctor_id=$selected_doctor_id';</script>";
    exit();
}

// --- HANDLE DELETE MESSAGE ---
if (isset($_POST['delete_msg_id']) && $selected_doctor_id) {
    $del_id = intval($_POST['delete_msg_id']);
    mysqli_query($conn, "DELETE FROM messages WHERE id='$del_id' AND user_id='$user_id'");

    // FIX: JavaScript redirect prevents "Headers already sent" error
    echo "<script>window.location.href='inbox.php?doctor_id=$selected_doctor_id';</script>";
    exit();
}

// 2. FETCH SIDEBAR LIST
$sidebar_sql = "
    SELECT d.id, d.name, d.image, d.specialty, m.message, m.created_at
    FROM doctors d
    JOIN (
        SELECT doctor_id, message, created_at
        FROM messages
        WHERE id IN (
            SELECT MAX(id) FROM messages WHERE user_id = '$user_id' GROUP BY doctor_id
        )
    ) m ON d.id = m.doctor_id
    ORDER BY m.created_at DESC
";
$sidebar_result = mysqli_query($conn, $sidebar_sql);

// 3. FETCH ACTIVE CHAT
$active_doc = null;
$msg_query = null;

if ($selected_doctor_id) {
    $doc_query = mysqli_query($conn, "SELECT * FROM doctors WHERE id = '$selected_doctor_id'");
    $active_doc = mysqli_fetch_assoc($doc_query);
    $msg_query = mysqli_query($conn, "SELECT * FROM messages WHERE user_id='$user_id' AND doctor_id='$selected_doctor_id' ORDER BY created_at ASC");
}

include 'header.php';
?>

<style>
    :root {
        --primary-color: #0d6efd;
        --primary-light: #eff6ff;
        --text-dark: #111827;
        --text-muted: #6b7280;
        --border-color: #e5e7eb;
        --bg-color: #f3f4f6;
    }

    body {
        background-color: var(--bg-color);
        overflow: hidden;
        height: 100vh;
    }

    /* --- LAYOUT & SIDEBAR --- */
    .inbox-layout {
        display: flex;
        /* Wide layout (98%) */
        width: 98%;
        height: calc(100vh - 80px);
        margin: 0 auto;
        background: #fff;
        overflow: hidden;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.03);
    }

    .sidebar {
        width: 400px;
        border-right: 1px solid var(--border-color);
        display: flex;
        flex-direction: column;
        background: #fff;
        flex-shrink: 0;
    }

    .sidebar-header {
        padding: 20px 25px;
        border-bottom: 1px solid var(--border-color);
    }

    .sidebar-header h2 {
        font-size: 22px;
        font-weight: 800;
        color: var(--text-dark);
        margin-bottom: 15px;
        letter-spacing: -0.5px;
    }

    .search-wrapper {
        position: relative;
    }

    .search-input {
        width: 100%;
        padding: 12px 15px 12px 40px;
        border-radius: 8px;
        border: 1px solid var(--border-color);
        background: #f9fafb;
        font-size: 14px;
        color: var(--text-dark);
        outline: none;
        transition: all 0.2s;
    }

    .search-input:focus {
        background: #fff;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
    }

    .search-icon {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        font-size: 14px;
    }

    .conversation-list {
        flex: 1;
        overflow-y: auto;
        padding: 10px 0;
    }

    .chat-item {
        display: flex;
        align-items: center;
        padding: 18px 25px;
        text-decoration: none;
        transition: 0.2s;
        border-left: 3px solid transparent;
        cursor: pointer;
    }

    .chat-item:hover {
        background-color: #f9fafb;
    }

    .chat-item.active {
        background-color: var(--primary-light);
        border-left-color: var(--primary-color);
    }

    .avatar-wrapper {
        position: relative;
        margin-right: 15px;
    }

    .avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        object-fit: cover;
        border: 1px solid var(--border-color);
    }

    .info {
        flex: 1;
        min-width: 0;
    }

    .name-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 4px;
    }

    .name {
        font-size: 15px;
        font-weight: 700;
        color: var(--text-dark);
    }

    .time-meta {
        font-size: 11px;
        color: var(--text-muted);
    }

    .preview {
        font-size: 13px;
        color: var(--text-muted);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: block;
    }

    .chat-item.active .name {
        color: var(--primary-color);
    }

    /* --- CHAT AREA --- */
    .chat-panel {
        flex: 1;
        display: flex;
        flex-direction: column;
        background: #ffffff;
        position: relative;
    }

    .chat-header {
        height: 85px;
        padding: 0 30px;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #fff;
        z-index: 10;
    }

    .header-user {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .header-user h3 {
        font-size: 18px;
        font-weight: 800;
        margin: 0;
        color: var(--text-dark);
        line-height: 1.2;
    }

    .specialty-text {
        font-size: 13px;
        color: var(--text-muted);
        font-weight: 500;
        display: block;
    }

    .messages-container {
        flex: 1;
        padding: 30px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 12px;
        background-color: #f9fafb;
    }

    .msg-row {
        display: flex;
        width: 100%;
        align-items: flex-end;
        gap: 10px;
    }

    .msg-row.patient {
        justify-content: flex-end;
    }

    .msg-bubble {
        max-width: 75%;
        padding: 14px 20px;
        font-size: 15px;
        line-height: 1.5;
        position: relative;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }

    .msg-row.doctor .msg-bubble {
        background: #fff;
        color: var(--text-dark);
        border: 1px solid var(--border-color);
        border-radius: 12px 12px 12px 0;
    }

    .msg-row.patient .msg-bubble {
        background: #1f2937;
        /* Monochrome Elegant Black/Grey */
        color: white;
        border-radius: 12px 12px 0 12px;
    }

    .msg-time {
        display: block;
        font-size: 11px;
        margin-top: 5px;
        opacity: 0.6;
        text-align: right;
    }

    .delete-form {
        opacity: 0;
        transition: opacity 0.2s;
        margin-bottom: 5px;
    }

    .msg-row:hover .delete-form {
        opacity: 1;
    }

    .trash-btn {
        background: none;
        border: none;
        color: #9ca3af;
        cursor: pointer;
        padding: 5px;
        font-size: 13px;
    }

    .trash-btn:hover {
        color: #ef4444;
    }

    .input-area {
        padding: 25px 30px;
        background: #fff;
        border-top: 1px solid var(--border-color);
    }

    .input-wrapper {
        display: flex;
        align-items: center;
        background: #f3f4f6;
        border-radius: 50px;
        padding: 6px 6px 6px 25px;
        border: 1px solid transparent;
        transition: 0.2s;
    }

    .input-wrapper:focus-within {
        background: #fff;
        border-color: var(--primary-color);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    }

    .text-input {
        flex: 1;
        border: none;
        background: transparent;
        outline: none;
        font-size: 15px;
        color: var(--text-dark);
        padding: 12px 0;
    }

    .send-btn {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: var(--primary-color);
        color: white;
        border: none;
        font-size: 16px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-left: 10px;
        transition: 0.2s;
    }

    .send-btn:hover {
        transform: scale(1.05);
        background: #0b5ed7;
    }

    ::-webkit-scrollbar {
        width: 6px;
    }

    ::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 4px;
    }

    ::-webkit-scrollbar-track {
        background: transparent;
    }

    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: var(--text-muted);
    }

    .empty-state i {
        font-size: 48px;
        margin-bottom: 20px;
        opacity: 0.3;
    }

    @media (max-width: 1000px) {
        .sidebar {
            width: 90px;
        }

        .sidebar-header h2,
        .search-wrapper,
        .name-row,
        .preview {
            display: none;
        }

        .chat-item {
            justify-content: center;
            padding: 15px 0;
            border-left: none;
        }

        .chat-item.active {
            border-bottom: 3px solid var(--primary-color);
            border-left: none;
        }

        .avatar-wrapper {
            margin: 0;
        }

        .header-details h3 {
            font-size: 16px;
        }
    }
</style>

<div class="inbox-layout">

    <div class="sidebar">
        <div class="sidebar-header">
            <h2>Messages</h2>
            <div class="search-wrapper">
                <i class="fas fa-search search-icon"></i>
                <input type="text" class="search-input" placeholder="Search...">
            </div>
        </div>

        <div class="conversation-list">
            <?php if (mysqli_num_rows($sidebar_result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($sidebar_result)): ?>
                    <a href="inbox.php?doctor_id=<?php echo $row['id']; ?>"
                        class="chat-item <?php echo ($selected_doctor_id == $row['id']) ? 'active' : ''; ?>">

                        <div class="avatar-wrapper">
                            <img src="<?php echo htmlspecialchars($row['image']); ?>" class="avatar">
                        </div>

                        <div class="info">
                            <div class="name-row">
                                <span class="name"><?php echo htmlspecialchars($row['name']); ?></span>
                                <?php if (isset($row['created_at'])): ?>
                                    <span class="time-meta"><?php echo date('M d', strtotime($row['created_at'])); ?></span>
                                <?php endif; ?>
                            </div>
                            <span class="preview"><?php echo htmlspecialchars($row['message']); ?></span>
                        </div>
                    </a>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="padding: 30px; text-align: center; color: #9ca3af; font-size: 14px;">No messages yet.</div>
            <?php endif; ?>
        </div>
    </div>

    <div class="chat-panel">
        <?php if ($selected_doctor_id && $active_doc): ?>

            <div class="chat-header">
                <div class="header-user">
                    <img src="<?php echo htmlspecialchars($active_doc['image']); ?>" class="avatar" style="width: 45px; height: 45px;">
                    <div>
                        <h3><?php echo htmlspecialchars($active_doc['name']); ?></h3>
                        <span class="specialty-text"><?php echo htmlspecialchars($active_doc['specialty']); ?></span>
                    </div>
                </div>

                <div style="color: #9ca3af;">
                    <i class="fas fa-ellipsis-v" style="cursor: pointer; padding: 10px;"></i>
                </div>
            </div>

            <div class="messages-container" id="msgBox">
                <?php while ($msg = mysqli_fetch_assoc($msg_query)): ?>
                    <div class="msg-row <?php echo ($msg['sender'] == 'patient') ? 'patient' : 'doctor'; ?>">

                        <?php if ($msg['sender'] == 'patient'): ?>
                            <form method="POST" onsubmit="return confirm('Delete this message?');" class="delete-form">
                                <input type="hidden" name="delete_msg_id" value="<?php echo $msg['id']; ?>">
                                <button type="submit" class="trash-btn" title="Delete message"><i class="fas fa-trash-alt"></i></button>
                            </form>
                        <?php endif; ?>

                        <div class="msg-bubble">
                            <?php echo nl2br(htmlspecialchars($msg['message'])); ?>
                            <span class="msg-time">
                                <?php echo date('h:i A', strtotime($msg['created_at'])); ?>
                                <?php if ($msg['sender'] == 'patient'): ?>
                                    <i class="fas fa-check" style="margin-left: 3px; font-size: 10px;"></i>
                                <?php endif; ?>
                            </span>
                        </div>

                    </div>
                <?php endwhile; ?>
            </div>

            <div class="input-area">
                <form method="POST" class="input-wrapper">
                    <input type="text" name="message" class="text-input" placeholder="Type your message..." autocomplete="off" required>
                    <button type="submit" name="send_msg" class="send-btn">
                        <i class="fas fa-paper-plane" style="margin-left: -2px; font-size: 15px;"></i>
                    </button>
                </form>
            </div>

            <script>
                var box = document.getElementById("msgBox");
                box.scrollTop = box.scrollHeight;
            </script>

        <?php else: ?>

            <div class="empty-state">
                <i class="far fa-comments"></i>
                <h3 style="color: #374151; font-weight: 700; margin-bottom: 5px;">It's nice to chat</h3>
                <p>Select a doctor from the list to view conversations.</p>
            </div>

        <?php endif; ?>
    </div>

</div>

<?php include 'footer.php'; ?>