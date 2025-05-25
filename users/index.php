<?php
session_start();
require_once '../core/dbConfig.php';
require_once '../core/models.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: ../index.php');
    exit;
}

$user = getUserById($_SESSION['user_id']);
$documents = getUserDocuments($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard - Google Docs Clone</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f9;
            color: #333;
        }

        .user-dashboard {
            max-width: 960px;
            margin: 50px auto;
            padding: 40px;
            background-color: #ffffff;
            border-radius: 14px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.07);
        }

        h2 {
            font-size: 28px;
            margin-bottom: 20px;
            color: #1a73e8;
        }

        h3 {
            margin-top: 40px;
            margin-bottom: 15px;
            font-size: 22px;
            color: #444;
            border-bottom: 2px solid #1a73e8;
            padding-bottom: 5px;
        }

        .btn {
            display: inline-block;
            background-color: #1a73e8;
            color: #fff;
            padding: 10px 16px;
            border-radius: 6px;
            text-decoration: none;
            margin: 10px 10px 10px 0;
            font-size: 14px;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #0c5dc0;
        }

        .btn-secondary {
            background-color: #f44336;
        }

        .btn-secondary:hover {
            background-color: #d32f2f;
        }

        ul {
            list-style: none;
            padding-left: 0;
        }

        li {
            background-color: #f8f9fc;
            border: 1px solid #e0e0e0;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        li div {
            max-width: 70%;
        }

        li strong {
            font-size: 16px;
            color: #222;
        }

        .actions a {
            margin-left: 10px;
            font-size: 13px;
            padding: 6px 10px;
            border-radius: 5px;
            text-decoration: none;
            color: #fff;
            background-color: #4caf50;
            transition: background-color 0.3s ease;
        }

        .actions a:hover {
            background-color: #3e8e41;
        }

        .actions a:nth-child(2) {
            background-color: #2196f3;
        }

        .actions a:nth-child(2):hover {
            background-color: #0b7dda;
        }

        .actions a:nth-child(3) {
            background-color: #ff9800;
        }

        .actions a:nth-child(3):hover {
            background-color: #e68900;
        }

        .actions a:nth-child(4) {
            background-color: #9c27b0;
        }

        .actions a:nth-child(4):hover {
            background-color: #7b1fa2;
        }

        @media (max-width: 600px) {
            li {
                flex-direction: column;
                align-items: flex-start;
            }

            .actions {
                margin-top: 10px;
            }

            li div {
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="user-dashboard">
        <h2>Welcome, <?= htmlspecialchars($user['name']) ?></h2>
        <a href="../logout.php" class="btn btn-secondary">Logout</a>
        <a href="create_doc.php" class="btn">+ Create New Document</a>

        <h3>Your Documents</h3>

        <?php if ($documents->num_rows > 0): ?>
            <ul>
                <?php while ($doc = $documents->fetch_assoc()): ?>
                    <li>
                        <div>
                            <strong><?= htmlspecialchars($doc['title']) ?></strong><br>
                            Last updated: <?= $doc['updated_at'] ?>
                        </div>
                        <div class="actions">
                            <a href="edit_doc.php?doc_id=<?= $doc['id'] ?>">Edit</a>
                            <a href="share_doc.php?doc_id=<?= $doc['id'] ?>">Share</a>
                            <a href="messages.php?doc_id=<?= $doc['id'] ?>">Messages</a>
                            <a href="activity_logs.php?doc_id=<?= $doc['id'] ?>">Logs</a>
                        </div>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p>You have no documents yet. Click “Create New Document” to get started.</p>
        <?php endif; ?>
    </div>
</body>
</html>
