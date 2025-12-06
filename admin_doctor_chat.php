<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_login.php");
    exit();
}

// REPLY
if (isset($_POST['send_reply'])) {
    $doc_id = $_POST['doc_id'];
    $reply = mysqli_real_escape_string($conn, $_POST['message']);
    // user_id=0 maintains the channel
    mysqli_query($conn, "INSERT INTO messages (user_id, doctor_id, sender, message) VALUES ('0', '$doc_id', 'admin', '$reply')");
    header("Location: admin_doctor_chat.php?doc_id=$doc_id");
    exit();
}

// FETCH DOCTOR LIST (Doctors who have user_id=0 messages)
$list_sql = "SELECT DISTINCT d.id, d.name, d.specialty 
             FROM messages m 
             JOIN doctors d ON m.doctor_id = d.id 
             WHERE m.user_id = '0' 
             ORDER BY m.created_at DESC";
$doc_list = mysqli_query($conn, $list_sql);

// FETCH ACTIVE CHAT
$active_msgs = null;
$current_doc = null;
if (isset($_GET['doc_id'])) {
    $did = $_GET['doc_id'];
    $current_doc = mysqli_fetch_assoc(mysqli_query($conn, "SELECT name FROM doctors WHERE id='$did'"));
    $active_msgs = mysqli_query($conn, "SELECT * FROM messages WHERE user_id='0' AND doctor_id='$did' ORDER BY created_at ASC");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Doctor Support Inbox</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><rect width=%22100%22 height=%22100%22 rx=%2220%22 fill=%22%230c5adb%22/><path fill=%22%23ffffff%22 d=%22M35 20h30v25h25v30h-25v25h-30v-25h-25v-30h25z%22/></svg>">

    <script src="https://kit.fontawesome.com/9e166a3863.js" crossorigin="anonymous"></script>
    <style>
        body {
            margin: 0;
            font-family: sans-serif;
            display: flex;
            height: 100vh;
            background: #f1f5f9;
        }

        .sidebar {
            width: 300px;
            background: white;
            border-right: 1px solid #ddd;
            display: flex;
            flex-direction: column;
        }

        .sidebar-header {
            padding: 20px;
            background: #0f172a;
            color: white;
            display: flex;
            justify-content: space-between;
        }

        .doc-item {
            padding: 15px;
            border-bottom: 1px solid #eee;
            text-decoration: none;
            color: #333;
            display: block;
        }

        .doc-item:hover,
        .doc-item.active {
            background: #eff6ff;
            border-left: 4px solid #0c5adb;
        }

        .chat-area {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .messages {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            background: #f8fafc;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .bubble {
            max-width: 60%;
            padding: 10px 15px;
            border-radius: 10px;
        }

        .doc-msg {
            align-self: flex-start;
            background: white;
            border: 1px solid #ddd;
        }

        .admin-msg {
            align-self: flex-end;
            background: #0c5adb;
            color: white;
        }

        .reply-box {
            padding: 20px;
            background: white;
            border-top: 1px solid #ddd;
            display: flex;
            gap: 10px;
        }

        input {
            flex: 1;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        button {
            background: #0c5adb;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <div class="sidebar-header">
            <span><i class="fas fa-user-md"></i> Doctor Support</span>
            <a href="admin_dashboard.php" style="color:#aaa;"><i class="fas fa-arrow-left"></i></a>
        </div>
        <div style="overflow-y:auto; flex:1;">
            <?php while ($d = mysqli_fetch_assoc($doc_list)): ?>
                <a href="?doc_id=<?php echo $d['id']; ?>" class="doc-item <?php echo (isset($_GET['doc_id']) && $_GET['doc_id'] == $d['id']) ? 'active' : ''; ?>">
                    <strong>Dr. <?php echo $d['name']; ?></strong><br>
                    <small style="color:#888;"><?php echo $d['specialty']; ?></small>
                </a>
            <?php endwhile; ?>
        </div>
    </div>

    <div class="chat-area">
        <?php if ($active_msgs): ?>
            <div style="padding:20px; background:white; border-bottom:1px solid #ddd; font-weight:bold;">Chat with Dr. <?php echo $current_doc['name']; ?></div>
            <div class="messages" id="msgs">
                <?php while ($m = mysqli_fetch_assoc($active_msgs)): ?>
                    <div class="bubble <?php echo ($m['sender'] == 'admin') ? 'admin-msg' : 'doc-msg'; ?>">
                        <?php echo htmlspecialchars($m['message']); ?>
                    </div>
                <?php endwhile; ?>
            </div>
            <form method="POST" class="reply-box">
                <input type="hidden" name="doc_id" value="<?php echo $_GET['doc_id']; ?>">
                <input type="text" name="message" placeholder="Reply to doctor..." required autocomplete="off">
                <button type="submit" name="send_reply">Send</button>
            </form>
            <script>
                document.getElementById('msgs').scrollTop = document.getElementById('msgs').scrollHeight;
            </script>
        <?php else: ?>
            <div style="margin:auto; color:#999;">Select a doctor to view support requests</div>
        <?php endif; ?>
    </div>

</body>

</html>