<?php
$page_title = "Patient Messages";
session_start();
include 'db_connect.php';

// 1. SECURITY CHECK (Doctor Only)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    header("Location: login.php");
    exit();
}

$doctor_id = $_SESSION['user_id'];
$selected_patient_id = isset($_GET['patient_id']) ? intval($_GET['patient_id']) : null;

// --- HANDLE SEND MESSAGE ---
if (isset($_POST['send_msg']) && $selected_patient_id && !empty(trim($_POST['message']))) {
    $msg = mysqli_real_escape_string($conn, $_POST['message']);
    // Sender is 'doctor'
    mysqli_query($conn, "INSERT INTO messages (user_id, doctor_id, sender, message) VALUES ('$selected_patient_id', '$doctor_id', 'doctor', '$msg')");
    header("Location: doctor_inbox.php?patient_id=$selected_patient_id");
    exit();
}

// 2. FETCH SIDEBAR LIST (Patients who have chatted)
$sidebar_sql = "
    SELECT u.id, u.full_name, m.message, m.created_at
    FROM users u
    JOIN (
        SELECT user_id, message, created_at
        FROM messages
        WHERE id IN (
            SELECT MAX(id) FROM messages WHERE doctor_id = '$doctor_id' AND user_id != 0 GROUP BY user_id
        )
    ) m ON u.id = m.user_id
    ORDER BY m.created_at DESC
";
$sidebar_result = mysqli_query($conn, $sidebar_sql);

// 3. FETCH ACTIVE CHAT
$active_patient = null;
$msg_query = null;

if ($selected_patient_id) {
    $pat_query = mysqli_query($conn, "SELECT full_name, email FROM users WHERE id = '$selected_patient_id'");
    $active_patient = mysqli_fetch_assoc($pat_query);
    // Fetch conversation
    $msg_query = mysqli_query($conn, "SELECT * FROM messages WHERE user_id='$selected_patient_id' AND doctor_id='$doctor_id' ORDER BY created_at ASC");
}

include 'header.php';
?>

<style>
    body {
        background-color: #f3f4f6;
        height: 100vh;
        overflow: hidden;
    }

    .inbox-layout {
        display: flex;
        width: 98%;
        height: calc(100vh - 80px);
        margin: 0 auto;
        background: #fff;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
    }

    /* SIDEBAR */
    .sidebar {
        width: 350px;
        border-right: 1px solid #e5e7eb;
        display: flex;
        flex-direction: column;
        background: #fff;
    }

    .sidebar-header {
        padding: 20px;
        border-bottom: 1px solid #e5e7eb;
        background: #f9fafb;
    }

    .chat-item {
        display: block;
        padding: 15px 20px;
        text-decoration: none;
        border-bottom: 1px solid #f0f0f0;
        transition: 0.2s;
    }

    .chat-item:hover,
    .chat-item.active {
        background: #eff6ff;
        border-left: 4px solid #0c5adb;
    }

    .p-name {
        font-weight: 700;
        color: #1f2937;
        display: block;
    }

    .p-preview {
        font-size: 13px;
        color: #6b7280;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: block;
    }

    /* CHAT AREA */
    .chat-panel {
        flex: 1;
        display: flex;
        flex-direction: column;
        background: #fff;
    }

    .chat-header {
        padding: 15px 30px;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #fff;
    }

    .messages-container {
        flex: 1;
        padding: 30px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 10px;
        background: #f9fafb;
    }

    .msg-row {
        display: flex;
        width: 100%;
    }

    .msg-row.doctor {
        justify-content: flex-end;
    }

    /* Doctor is right */
    .msg-row.patient {
        justify-content: flex-start;
    }

    /* Patient is left */

    .msg-bubble {
        max-width: 70%;
        padding: 12px 18px;
        font-size: 15px;
        border-radius: 15px;
        line-height: 1.5;
        position: relative;
    }

    .msg-row.doctor .msg-bubble {
        background: #0c5adb;
        color: white;
        border-radius: 15px 15px 0 15px;
    }

    .msg-row.patient .msg-bubble {
        background: #e5e7eb;
        color: #1f2937;
        border-radius: 15px 15px 15px 0;
    }

    .input-area {
        padding: 20px;
        background: #fff;
        border-top: 1px solid #e5e7eb;
        display: flex;
        gap: 10px;
    }

    .text-input {
        flex: 1;
        padding: 12px 20px;
        border-radius: 50px;
        border: 1px solid #d1d5db;
        outline: none;
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

<div class="inbox-layout">
    <div class="sidebar">
        <div class="sidebar-header">
            <h2 style="margin:0; font-size:20px; color:#1f2937;">My Patients</h2>
        </div>
        <div style="overflow-y:auto; flex:1;">
            <?php if (mysqli_num_rows($sidebar_result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($sidebar_result)): ?>
                    <a href="doctor_inbox.php?patient_id=<?php echo $row['id']; ?>" class="chat-item <?php echo ($selected_patient_id == $row['id']) ? 'active' : ''; ?>">
                        <span class="p-name"><?php echo htmlspecialchars($row['full_name']); ?></span>
                        <span class="p-preview"><?php echo htmlspecialchars($row['message']); ?></span>
                    </a>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="padding:20px; text-align:center; color:#9ca3af;">No chats yet.</div>
            <?php endif; ?>
        </div>
    </div>

    <div class="chat-panel">
        <?php if ($selected_patient_id && $active_patient): ?>
            <div class="chat-header">
                <div>
                    <h3 style="margin:0;"><?php echo htmlspecialchars($active_patient['full_name']); ?></h3>
                    <small style="color:#6b7280;">Patient</small>
                </div>
            </div>

            <div class="messages-container" id="msgBox">
                <?php while ($msg = mysqli_fetch_assoc($msg_query)): ?>
                    <div class="msg-row <?php echo ($msg['sender'] == 'doctor') ? 'doctor' : 'patient'; ?>">
                        <div class="msg-bubble">
                            <?php echo nl2br(htmlspecialchars($msg['message'])); ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>

            <form method="POST" class="input-area">
                <input type="text" name="message" class="text-input" placeholder="Type a message..." autocomplete="off" required>
                <button type="submit" name="send_msg" class="send-btn"><i class="fas fa-paper-plane"></i></button>
            </form>
            <script>
                document.getElementById("msgBox").scrollTop = document.getElementById("msgBox").scrollHeight;
            </script>
        <?php else: ?>
            <div style="margin:auto; text-align:center; color:#9ca3af;">
                <i class="far fa-comments" style="font-size:48px; margin-bottom:10px;"></i>
                <p>Select a patient to start chatting.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php include 'footer.php'; ?>