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

if (!$document) {
    die("Document not found.");
}

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
    die("Access denied. You're not allowed to edit this document.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Document - <?= htmlspecialchars($document['title']) ?></title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f9;
            color: #333;
        }

        .editor-container {
            max-width: 900px;
            margin: 60px auto;
            padding: 40px;
            background-color: #ffffff;
            border-radius: 14px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.07);
        }

        h2 {
            color: #1a73e8;
            font-size: 26px;
            margin-bottom: 20px;
        }

        #toolbar {
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 6px;
            background: #f1f3f4;
        }

        #toolbar button {
            background: none;
            border: none;
            font-size: 16px;
            margin-right: 10px;
            cursor: pointer;
        }

        #editor {
            width: 100%;
            min-height: 400px;
            border: 1px solid #ccc;
            border-radius: 10px;
            padding: 15px;
            background: #fff;
            font-size: 15px;
            line-height: 1.6;
            box-sizing: border-box;
            outline: none;
        }

        #status {
            margin-top: 12px;
            font-size: 14px;
            color: green;
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: #555;
            font-size: 14px;
        }

        .back-link:hover {
            color: #1a73e8;
        }

        #titleInput {
            width: 100%;
            padding: 12px;
            font-size: 20px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-sizing: border-box;
        }
    </style>
</head>
<body>
    <div class="editor-container">
        <h2>Editing Document</h2>
        <a class="back-link" href="index.php">← Back to Dashboard</a>

        <input type="hidden" id="docId" value="<?= $document['id'] ?>">
        <input type="text" id="titleInput" value="<?= htmlspecialchars($document['title']) ?>" placeholder="Document Title">

        <div id="toolbar">
            <button type="button" onclick="format('bold')" title="Bold"><i class="fas fa-bold"></i></button>
            <button type="button" onclick="format('italic')" title="Italic"><i class="fas fa-italic"></i></button>
            <button type="button" onclick="format('underline')" title="Underline"><i class="fas fa-underline"></i></button>
            <button type="button" onclick="format('insertUnorderedList')" title="Bullet List"><i class="fas fa-list-ul"></i></button>
            <button type="button" onclick="format('formatBlock','<h1>')">H1</button>
            <button type="button" onclick="format('formatBlock','<h2>')">H2</button>
            <button type="button" onclick="format('formatBlock','<h3>')">H3</button>
            <button type="button" onclick="insertImage()" title="Insert Image"><i class="fas fa-image"></i></button>
        </div>

        <div id="editor" contenteditable="true"><?= $document['content'] ?></div>
        <div id="status"></div>
    </div>

    <script>
    function format(command, value = null) {
        document.execCommand(command, false, value);
    }

    function insertImage() {
        const url = prompt("Enter image URL:");
        if (url) {
            document.execCommand('insertImage', false, url);
        }
    }

    let timeoutId;

    function autosave() {
        const content = $('#editor').html();
        const docId = $('#docId').val();
        const title = $('#titleInput').val();

        $.post('../core/handleForms.php', {
            action: 'autosave',
            doc_id: docId,
            content: content,
            title: title
        }, function (res) {
            const result = JSON.parse(res);
            if (result.status === 'success') {
                $('#status').text('Auto-saved at ' + new Date().toLocaleTimeString());
            } else {
                $('#status').text('Save failed');
            }
        });
    }

    $('#editor, #titleInput').on('input', function () {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(autosave, 1000);
    });
    </script>
</body>
</html>
