<?php
session_start();
require_once '../core/dbConfig.php';
require_once '../core/models.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

$docId = isset($_GET['doc_id']) ? intval($_GET['doc_id']) : 0;
$document = getDocumentById($docId);
$userId = $_SESSION['user_id'];

$isOwner = $document['owner_id'] == $userId;
$sharedUsers = getSharedUsers($docId);
$isSharedUser = false;
foreach ($sharedUsers as $sharedUser) {
    if ($sharedUser['id'] == $userId) {
        $isSharedUser = true;
        break;
    }
}
if (!$isOwner && !$isSharedUser) {
    die("Access denied. You are not authorized to view this chat.");
}

$messages = getMessages($docId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Messages - <?= htmlspecialchars($document['title']) ?></title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f9;
            color: #333;
        }

        .messages-container {
            max-width: 800px;
            margin: 60px auto;
            padding: 40px;
            background-color: #ffffff;
            border-radius: 14px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.07);
        }

        h2 {
            font-size: 24px;
            color: #1a73e8;
            margin-bottom: 20px;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            font-size: 14px;
            color: #555;
            text-decoration: none;
        }

        .back-link:hover {
            color: #1a73e8;
        }

        .chat-box {
            border: 1px solid #ddd;
            height: 300px;
            overflow-y: scroll;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 10px;
        }

        .chat-message {
            margin-bottom: 12px;
            font-size: 14px;
        }

        .chat-message strong {
            color: #1a73e8;
        }

        .chat-message small {
            color: #777;
            margin-left: 6px;
        }

        .chat-form {
            margin-top: 20px;
        }

        textarea {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 14px;
            resize: none;
            box-sizing: border-box;
        }

        button[type="submit"] {
            margin-top: 10px;
            background-color: #1a73e8;
            color: #fff;
            border: none;
            padding: 10px 18px;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button[type="submit"]:hover {
            background-color: #0c5dc0;
        }
    </style>
</head>
<body>
    <div class="messages-container">
        <h2>Messages for: <?= htmlspecialchars($document['title']) ?></h2>
        <a class="back-link" href="index.php">← Back to Dashboard</a>

        <div class="chat-box" id="chatBox">
            <?php while ($msg = $messages->fetch_assoc()): ?>
                <div class="chat-message">
                    <strong><?= htmlspecialchars($msg['name']) ?>:</strong>
                    <?= htmlspecialchars($msg['message']) ?>
                    <small>(<?= $msg['sent_at'] ?>)</small>
                </div>
            <?php endwhile; ?>
        </div>

        <form id="chatForm" class="chat-form">
            <input type="hidden" id="docId" value="<?= $docId ?>">
            <textarea id="message" placeholder="Type your message..." rows="3" required></textarea>
            <button type="submit">Send</button>
        </form>
    </div>

    <script>
    $('#chatForm').submit(function(e) {
        e.preventDefault();

        const message = $('#message').val();
        const docId = $('#docId').val();

        if (message.trim() === '') return;

        $.post('../core/handleForms.php', {
            action: 'send_message',
            doc_id: docId,
            message: message
        }, function(res) {
            const result = JSON.parse(res);
            if (result.status === 'success') {
                location.reload(); // Or replace with dynamic append
            } else {
                alert("Failed to send message.");
            }
        });
    });
    </script>
</body>
</html>
