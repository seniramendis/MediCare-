<?php
$page_title = "Support Chat";
session_start();
include 'db_connect.php';

// 1. SECURITY CHECK
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$admin_id = 0; // 0 represents Admin

// 2. HANDLE SEND MESSAGE
if (isset($_POST['send_msg']) && !empty(trim($_POST['message']))) {
    $msg = mysqli_real_escape_string($conn, $_POST['message']);
    $sql = "INSERT INTO messages (user_id, doctor_id, sender, message) VALUES ('$user_id', '$admin_id', 'patient', '$msg')";
    mysqli_query($conn, $sql);
    header("Location: support_chat.php");
    exit();
}

// 3. FETCH MESSAGES
$chat_q = mysqli_query($conn, "SELECT * FROM messages WHERE user_id='$user_id' AND doctor_id='$admin_id' ORDER BY created_at ASC");

include 'header.php';
?>

<style>
    body {
        background-color: #f0f2f5;
    }

    .chat-container {
        max-width: 800px;
        margin: 40px auto;
        background: white;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        height: 80vh;
        display: flex;
        flex-direction: column;
    }

    .chat-header {
        background: #1f2937;
        color: white;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .chat-header h3 {
        margin: 0;
        font-size: 18px;
    }

    .support-icon {
        width: 45px;
        height: 45px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }

    .messages-box {
        flex: 1;
        padding: 20px;
        overflow-y: auto;
        background: #f9fafb;
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .msg-row {
        display: flex;
        align-items: flex-end;
    }

    .msg-row.patient {
        justify-content: flex-end;
    }

    .bubble {
        max-width: 70%;
        padding: 12px 16px;
        border-radius: 15px;
        font-size: 15px;
        line-height: 1.5;
        position: relative;
    }

    .patient .bubble {
        background: #0c5adb;
        color: white;
        border-radius: 15px 15px 0 15px;
    }

    .admin .bubble {
        background: #e5e7eb;
        color: #1f2937;
        border-radius: 15px 15px 15px 0;
    }

    .time {
        font-size: 10px;
        opacity: 0.7;
        display: block;
        text-align: right;
        margin-top: 5px;
    }

    .input-area {
        padding: 20px;
        border-top: 1px solid #eee;
        background: white;
        display: flex;
        gap: 10px;
    }

    .input-area input {
        flex: 1;
        padding: 12px 20px;
        border-radius: 50px;
        border: 1px solid #ddd;
        outline: none;
    }

    .input-area button {
        background: #0c5adb;
        color: white;
        border: none;
        width: 45px;
        height: 45px;
        border-radius: 50%;
        cursor: pointer;
    }
</style>

<div class="chat-container">
    <div class="chat-header">
        <div class="support-icon"><i class="fas fa-headset"></i></div>
        <div>
            <h3>Admin Support</h3>
            <span style="font-size: 13px; opacity: 0.8; display: block;">Typically replies within 1 hour</span>
        </div>
    </div>

    <div class="messages-box" id="msgBox">
        <?php if (mysqli_num_rows($chat_q) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($chat_q)): ?>
                <div class="msg-row <?php echo ($row['sender'] == 'patient') ? 'patient' : 'admin'; ?>">
                    <div class="bubble">
                        <?php echo htmlspecialchars($row['message']); ?>
                        <span class="time"><?php echo date('h:i A', strtotime($row['created_at'])); ?></span>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="text-align:center; color:#999; margin-top:50px;">How can we help you today?</p>
        <?php endif; ?>
    </div>

    <form method="POST" class="input-area">
        <input type="text" name="message" placeholder="Type your issue..." autocomplete="off" required>
        <button type="submit" name="send_msg"><i class="fas fa-paper-plane"></i></button>
    </form>
</div>

<script>
    var box = document.getElementById("msgBox");
    box.scrollTop = box.scrollHeight;
</script>

<?php include 'footer.php'; ?>