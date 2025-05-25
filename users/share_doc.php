<?php
session_start();
require_once '../core/dbConfig.php';
require_once '../core/models.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: ../index.php');
    exit;
}

$docId = isset($_GET['doc_id']) ? intval($_GET['doc_id']) : 0;
$document = getDocumentById($docId);

if (!$document || $document['owner_id'] != $_SESSION['user_id']) {
    die("Access denied or document not found.");
}

$sharedUsers = getSharedUsers($docId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Share Document - <?= htmlspecialchars($document['title']) ?></title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f9;
            color: #333;
        }

        .share-container {
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
            margin-bottom: 10px;
            font-size: 14px;
            color: #555;
            text-decoration: none;
        }

        .back-link:hover {
            color: #1a73e8;
        }

        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            margin-bottom: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 14px;
            box-sizing: border-box;
        }

        #searchResults {
            border: 1px solid #ccc;
            margin-top: 5px;
            max-height: 200px;
            overflow-y: auto;
            border-radius: 8px;
            background-color: #fff;
        }

        .user-item {
            padding: 10px;
            cursor: pointer;
            border-bottom: 1px solid #eee;
        }

        .user-item:hover {
            background-color: #f0f0f0;
        }

        h3 {
            margin-top: 30px;
            font-size: 18px;
            color: #444;
        }

        ul {
            list-style: none;
            padding: 0;
        }

        ul li {
            padding: 8px 0;
            border-bottom: 1px solid #eee;
            font-size: 14px;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="share-container">
        <h2>Share "<?= htmlspecialchars($document['title']) ?>"</h2>
        <a class="back-link" href="index.php">← Back to Dashboard</a>

        <div style="margin-top: 20px;">
            <input type="hidden" id="docId" value="<?= $docId ?>">
            <label for="userSearch">Search User by Name or Email:</label>
            <input type="text" id="userSearch" autocomplete="off" placeholder="Start typing to search...">
        </div>

        <div id="searchResults"></div>

        <h3>Already Shared With:</h3>
        <ul>
            <?php while ($user = $sharedUsers->fetch_assoc()): ?>
                <li><?= htmlspecialchars($user['name']) ?> (<?= $user['email'] ?>)</li>
            <?php endwhile; ?>
        </ul>
    </div>

    <script>
    $('#userSearch').on('input', function () {
        const term = $(this).val();
        if (term.length < 2) {
            $('#searchResults').html('');
            return;
        }

        $.get('../core/handleForms.php', {
            action: 'search_user',
            term: term
        }, function (response) {
            const users = JSON.parse(response);
            let html = '';
            users.forEach(user => {
                html += `<div class="user-item" data-id="${user.id}">
                            ${user.name} (${user.email})
                        </div>`;
            });
            $('#searchResults').html(html);
        });
    });

    $(document).on('click', '.user-item', function () {
        const userId = $(this).data('id');
        const docId = $('#docId').val();

        $.post('../core/handleForms.php', {
            action: 'share_user',
            doc_id: docId,
            user_id: userId
        }, function (response) {
            const res = JSON.parse(response);
            if (res.status === 'success') {
                alert("User added to document.");
                location.reload();
            } else {
                alert("Failed to share document.");
            }
        });
    });
    </script>
</body>
</html>
