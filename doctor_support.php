<?php
$page_title = "Admin Support";
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    header("Location: login.php");
    exit();
}

$doctor_id = $_SESSION['user_id'];
// NOTE: We use user_id='0' to represent "Not a Patient" context for this specific chat link
$admin_flag = 0;

// SEND
if (isset($_POST['send_msg']) && !empty(trim($_POST['message']))) {
    $msg = mysqli_real_escape_string($conn, $_POST['message']);
    // user_id=0 distinguishes this as a Doctor-Admin chat (since user_id is usually patient)
    $sql = "INSERT INTO messages (user_id, doctor_id, sender, message) VALUES ('$admin_flag', '$doctor_id', 'doctor', '$msg')";
    mysqli_query($conn, $sql);
    header("Location: doctor_support.php");
    exit();
}

// FETCH
$chat_q = mysqli_query($conn, "SELECT * FROM messages WHERE user_id='$admin_flag' AND doctor_id='$doctor_id' ORDER BY created_at ASC");

include 'header.php';
?>

<style>
    body {
        background-color: #f0f2f5;
    }

    .chat-container {
        max-width: 700px;
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
        background: #111827;
        color: white;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 15px;
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

    .msg-row.doctor {
        justify-content: flex-end;
    }

    .bubble {
        max-width: 70%;
        padding: 12px 16px;
        border-radius: 15px;
        font-size: 15px;
    }

    .doctor .bubble {
        background: #0c5adb;
        color: white;
        border-radius: 15px 15px 0 15px;
    }

    .admin .bubble {
        background: #e5e7eb;
        color: #1f2937;
        border-radius: 15px 15px 15px 0;
    }

    .input-area {
        padding: 20px;
        background: white;
        border-top: 1px solid #eee;
        display: flex;
        gap: 10px;
    }

    .text-input {
        flex: 1;
        padding: 12px;
        border-radius: 50px;
        border: 1px solid #ddd;
    }

    .send-btn {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background: #0c5adb;
        color: white;
        border: none;
        cursor: pointer;
    }
</style>

<div class="chat-container">
    <div class="chat-header">
        <i class="fas fa-user-shield fa-2x"></i>
        <div>
            <h3>Admin Support</h3><small>For System & Account Issues</small>
        </div>
    </div>

    <div class="messages-box" id="msgBox">
        <?php while ($row = mysqli_fetch_assoc($chat_q)): ?>
            <div class="msg-row <?php echo ($row['sender'] == 'doctor') ? 'doctor' : 'admin'; ?>">
                <div class="bubble"><?php echo htmlspecialchars($row['message']); ?></div>
            </div>
        <?php endwhile; ?>
    </div>

    <form method="POST" class="input-area">
        <input type="text" name="message" class="text-input" placeholder="Type message..." autocomplete="off" required>
        <button type="submit" name="send_msg" class="send-btn"><i class="fas fa-paper-plane"></i></button>
    </form>
</div>
<script>
    document.getElementById("msgBox").scrollTop = document.getElementById("msgBox").scrollHeight;
</script>
<?php include 'footer.php'; ?>