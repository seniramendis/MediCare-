<?php
$page_title = "Chat with Doctor";

session_start();
include 'db_connect.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
if (!isset($_GET['doctor_id'])) {
    header("Location: Doctor.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$doctor_id = mysqli_real_escape_string($conn, $_GET['doctor_id']);


if (isset($_POST['send_msg']) && !empty(trim($_POST['message']))) {
    $msg = mysqli_real_escape_string($conn, $_POST['message']);
    mysqli_query($conn, "INSERT INTO messages (user_id, doctor_id, sender, message) VALUES ('$user_id', '$doctor_id', 'patient', '$msg')");
    header("Location: chat.php?doctor_id=$doctor_id");
    exit();
}

if (isset($_POST['delete_msg_id'])) {
    $del_id = mysqli_real_escape_string($conn, $_POST['delete_msg_id']);
    mysqli_query($conn, "DELETE FROM messages WHERE id='$del_id' AND user_id='$user_id'");
    header("Location: chat.php?doctor_id=$doctor_id");
    exit();
}


$doc_q = mysqli_query($conn, "SELECT name, image FROM doctors WHERE id='$doctor_id'");
$doctor = mysqli_fetch_assoc($doc_q);

$chat_q = mysqli_query($conn, "SELECT * FROM messages WHERE user_id='$user_id' AND doctor_id='$doctor_id' ORDER BY created_at ASC");


include 'header.php';
?>

<style>
    body {
        background-color: #f0f2f5;
    }

    .chat-page-container {
        display: flex;
        justify-content: center;
        padding-top: 40px;
        padding-bottom: 40px;
        min-height: calc(100vh - 80px);
    }

    .chat-window {
        width: 100%;
        max-width: 600px;
        height: 80vh;
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        border: 1px solid #e5e7eb;
    }


    .chat-top-bar {
        background: white;
        padding: 15px 20px;
        border-bottom: 1px solid #f0f0f0;
        display: flex;
        align-items: center;
        gap: 15px;
        z-index: 10;
    }

    .back-arrow {
        font-size: 20px;
        color: #555;
        transition: 0.2s;
    }

    .back-arrow:hover {
        color: var(--primary-color);
    }

    .doc-avatar-small {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #eef2ff;
    }

    .chat-doc-name {
        font-size: 17px;
        font-weight: 700;
        color: #1f2937;
        margin: 0;
    }


    .messages-area {
        flex: 1;
        padding: 20px;
        background-color: #f9fafb;
        background-image: radial-gradient(#e5e7eb 1px, transparent 1px);
        background-size: 20px 20px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 15px;
    }


    .message-row {
        display: flex;
        width: 100%;
        align-items: center;

        gap: 10px;

    }

    .message-row.patient {
        justify-content: flex-end;
    }

    .message-row.doctor {
        justify-content: flex-start;
    }


    .bubble {
        max-width: 75%;
        padding: 12px 18px;
        font-size: 15px;
        line-height: 1.5;
        position: relative;
    }

    .bubble.doctor {
        background: white;
        color: #1f2937;
        border-radius: 20px 20px 20px 4px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        border: 1px solid #f0f0f0;
    }

    .bubble.patient {
        background: var(--primary-color);
        color: white;
        border-radius: 20px 20px 4px 20px;
        box-shadow: 0 4px 10px rgba(12, 90, 219, 0.2);
    }

    .msg-time {
        font-size: 10px;
        opacity: 0.7;
        margin-top: 5px;
        display: block;
        text-align: right;
    }


    .delete-outside-btn {
        background: none;
        border: none;
        color: #d1d5db;

        cursor: pointer;
        font-size: 14px;
        transition: 0.2s;
        padding: 5px;
        display: flex;
        align-items: center;
    }

    .delete-outside-btn:hover {
        color: #ef4444;

        transform: scale(1.1);
    }


    .input-area {
        background: white;
        padding: 15px 20px;
        border-top: 1px solid #f0f0f0;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .type-box {
        flex: 1;
        padding: 14px 20px;
        background: #f8f9fa;
        border: 1px solid transparent;
        border-radius: 50px;
        font-size: 15px;
        outline: none;
        transition: 0.3s;
    }

    .type-box:focus {
        background: white;
        border-color: var(--primary-color);
    }

    .send-icon-btn {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: var(--primary-color);
        color: white;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        box-shadow: 0 4px 10px rgba(12, 90, 219, 0.3);
        transition: 0.2s;
    }

    .send-icon-btn:hover {
        background: #0946a8;
        transform: scale(1.05);
    }

    .messages-area::-webkit-scrollbar {
        width: 6px;
    }

    .messages-area::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }
</style>

<div class="chat-page-container">
    <div class="chat-window">

        <div class="chat-top-bar">
            <a href="doctor_profile.php?id=<?php echo $doctor_id; ?>" class="back-arrow"><i class="fas fa-arrow-left"></i></a>
            <img src="<?php echo htmlspecialchars($doctor['image']); ?>" class="doc-avatar-small">
            <h3 class="chat-doc-name"><?php echo htmlspecialchars($doctor['name']); ?></h3>
        </div>

        <div class="messages-area" id="scrollBox">
            <?php if (mysqli_num_rows($chat_q) > 0): ?>
                <?php while ($chat = mysqli_fetch_assoc($chat_q)): ?>

                    <div class="message-row <?php echo ($chat['sender'] == 'patient') ? 'patient' : 'doctor'; ?>">

                        <div class="bubble <?php echo ($chat['sender'] == 'patient') ? 'patient' : 'doctor'; ?>">
                            <span><?php echo htmlspecialchars($chat['message']); ?></span>
                            <span class="msg-time"><?php echo date('h:i A', strtotime($chat['created_at'])); ?></span>
                        </div>

                        <?php if ($chat['sender'] == 'patient'): ?>
                            <form method="POST" onsubmit="return confirm('Delete message?');" style="margin:0;">
                                <input type="hidden" name="delete_msg_id" value="<?php echo $chat['id']; ?>">
                                <button type="submit" class="delete-outside-btn" title="Delete">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        <?php endif; ?>

                    </div>

                <?php endwhile; ?>
            <?php else: ?>
                <div style="text-align: center; margin: auto; color: #9ca3af;">
                    <i class="far fa-comments" style="font-size: 48px; opacity: 0.3; margin-bottom: 10px;"></i>
                    <p>Start a new conversation.</p>
                </div>
            <?php endif; ?>
        </div>

        <form method="POST" class="input-area">
            <input type="text" name="message" class="type-box" placeholder="Type a message..." autocomplete="off" required>
            <button type="submit" name="send_msg" class="send-icon-btn">
                <i class="fas fa-paper-plane"></i>
            </button>
        </form>

    </div>
</div>

<script>
    window.onload = function() {
        var box = document.getElementById("scrollBox");
        box.scrollTop = box.scrollHeight;
    }
</script>

<?php include 'footer.php'; ?>