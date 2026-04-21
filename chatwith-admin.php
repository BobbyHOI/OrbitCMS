<?php
/**
 * OrbitCMS - Support Chat
 */
session_start();
error_reporting(0);
include('includes/config.php');

if(strlen($_SESSION['emplogin']) == 0) {   
    header('location:index.php');
    exit;
}

$email = $_SESSION['emplogin'];

// Handle new message submission
if(isset($_POST['send_message'])) {
    $message = trim($_POST['message']);
    if(!empty($message)) {
        try {
            $sql = "INSERT INTO tblchating (empid, chat, admin) VALUES (:email, :chat, 0)";
            $stmt = $dbh->prepare($sql);
            $stmt->execute([':email' => $email, ':chat' => $message]);
            
            // Redirect to prevent form resubmission
            header('Location: chatwith-admin.php');
            exit;
        } catch (PDOException $e) {
            $error = "Error sending message. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Employee | Support Channel</title>
    <?php include('includes/head.php');?>
    <style>
        .chat-area { height: 400px; overflow-y: auto; background: #fdfdfd; padding: 20px; border-radius: 8px; border: 1px solid #eee; display: flex; flex-direction: column; }
        .bubble { padding: 12px 18px; border-radius: 20px; margin-bottom: 10px; max-width: 70%; position: relative; }
        .bubble.admin { background: #e3f2fd; align-self: flex-start; border-bottom-left-radius: 2px; }
        .bubble.me { background: #fff; align-self: flex-end; border: 1px solid #ddd; border-bottom-right-radius: 2px; }
    </style>
</head>
<body>
    <?php include('includes/header.php');?>
    <?php include('includes/sidebar.php');?>
    
    <main class="mn-inner">
        <div class="row">
            <div class="col s12"><div class="page-title">Admin Support</div></div>
            <div class="col s12 m10 offset-m1">
                <div class="card">
                    <div class="card-content">
                        <span class="card-title">Message History</span>
                         <?php if(isset($error)){?><div class="errorWrap"><strong>ERROR</strong>: <?php echo htmlentities($error); ?> </div><?php } ?>
                        <div class="chat-area" id="chat-box">
                            <?php 
                            $stmt = $dbh->prepare("SELECT * FROM tblchating WHERE empid = :email ORDER BY id ASC");
                            $stmt->execute([':email' => $email]);
                            while($chat = $stmt->fetch()) { ?>
                                <div class="bubble <?php echo ($chat->admin == 1) ? 'admin' : 'me'; ?>">
                                    <small style="display:block; opacity:0.6; margin-bottom:5px;"><?php echo ($chat->admin == 1) ? 'Administrator' : 'Me'; ?></small>
                                    <?php echo htmlentities($chat->chat); ?>
                                </div>
                            <?php } ?>
                        </div>
                        
                        <form method="post">
                            <div class="row" style="margin-top:20px;">
                                <div class="input-field col s10">
                                    <input type="text" id="chat_msg" name="message" placeholder="Type a message..." autocomplete="off">
                                    <label class="active">Reply</label>
                                </div>
                                <div class="col s2 center-align" style="margin-top:15px;">
                                    <button type="submit" name="send_message" class="btn blue waves-effect waves-light"><i class="material-icons">send</i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include('includes/footer.php');?>
    <script>
        // Scroll to the bottom of the chat box
        var chatBox = document.getElementById('chat-box');
        chatBox.scrollTop = chatBox.scrollHeight;
    </script>
</body>
</html>