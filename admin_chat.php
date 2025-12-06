<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_login.php");
    exit();
}

if (isset($_POST['send_reply'])) {
    $p_id = $_POST['patient_id'];
    $reply = mysqli_real_escape_string($conn, $_POST['message']);
    mysqli_query($conn, "INSERT INTO messages (user_id, doctor_id, sender, message) VALUES ('$p_id', '0', 'admin', '$reply')");
    header("Location: admin_chat.php?patient_id=$p_id");
    exit();
}

$list_sql = "SELECT DISTINCT u.id, u.full_name, u.email 
             FROM messages m 
             JOIN users u ON m.user_id = u.id 
             WHERE m.doctor_id = '0' 
             ORDER BY m.created_at DESC";
$patients_list = mysqli_query($conn, $list_sql);

$active_msgs = null;
$current_patient = null;
if (isset($_GET['patient_id'])) {
    $pid = $_GET['patient_id'];
    $u_res = mysqli_query($conn, "SELECT full_name FROM users WHERE id='$pid'");
    $current_patient = mysqli_fetch_assoc($u_res);
    $active_msgs = mysqli_query($conn, "SELECT * FROM messages WHERE user_id='$pid' AND doctor_id='0' ORDER BY created_at ASC");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Support Inbox</title>
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
            align-items: center;
        }

        .patient-list {
            overflow-y: auto;
            flex: 1;
        }

        .p-item {
            padding: 15px 20px;
            border-bottom: 1px solid #f0f0f0;
            display: block;
            text-decoration: none;
            color: #333;
            transition: 0.2s;
        }

        .p-item:hover,
        .p-item.active {
            background: #e0f2fe;
            border-left: 4px solid #0c5adb;
        }

        .chat-area {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .chat-head {
            padding: 20px;
            background: white;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
            font-size: 18px;
        }

        .messages {
            flex: 1;
            padding: 30px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .bubble {
            max-width: 60%;
            padding: 10px 15px;
            border-radius: 10px;
            font-size: 14px;
        }

        .patient-msg {
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
            <span><i class="fas fa-inbox"></i> Support Requests</span>
            <a href="admin_dashboard.php" style="color:#94a3b8;"><i class="fas fa-arrow-left"></i></a>
        </div>
        <div class="patient-list">
            <?php while ($p = mysqli_fetch_assoc($patients_list)): ?>
                <a href="?patient_id=<?php echo $p['id']; ?>" class="p-item <?php echo (isset($_GET['patient_id']) && $_GET['patient_id'] == $p['id']) ? 'active' : ''; ?>">
                    <strong><?php echo $p['full_name']; ?></strong><br>
                    <small style="color:#888;"><?php echo $p['email']; ?></small>
                </a>
            <?php endwhile; ?>
        </div>
    </div>

    <div class="chat-area">
        <?php if ($active_msgs): ?>
            <div class="chat-head">Chat with <?php echo $current_patient['full_name']; ?></div>
            <div class="messages" id="msgs">
                <?php while ($m = mysqli_fetch_assoc($active_msgs)): ?>
                    <div class="bubble <?php echo ($m['sender'] == 'admin') ? 'admin-msg' : 'patient-msg'; ?>">
                        <?php echo htmlspecialchars($m['message']); ?>
                    </div>
                <?php endwhile; ?>
            </div>
            <form method="POST" class="reply-box">
                <input type="hidden" name="patient_id" value="<?php echo $_GET['patient_id']; ?>">
                <input type="text" name="message" placeholder="Type reply..." required autocomplete="off">
                <button type="submit" name="send_reply">Send</button>
            </form>
            <script>
                document.getElementById('msgs').scrollTop = document.getElementById('msgs').scrollHeight;
            </script>
        <?php else: ?>
            <div style="margin:auto; color:#999; text-align:center;">
                <i class="fas fa-comments" style="font-size:50px; margin-bottom:10px;"></i><br>
                Select a patient to view messages
            </div>
        <?php endif; ?>
    </div>

</body>

</html>