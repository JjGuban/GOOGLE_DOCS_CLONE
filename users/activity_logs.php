<?php
session_start();
require_once '../core/dbConfig.php';
require_once '../core/models.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

$userRole = $_SESSION['role'] ?? 'user';
$docId = isset($_GET['doc_id']) ? intval($_GET['doc_id']) : 0;
$document = getDocumentById($docId);
$userId = $_SESSION['user_id'];

if (!$document) {
    die("Document not found.");
}

$isOwner = ($document['owner_id'] == $userId);
$isSharedUser = false;

$sharedResult = getSharedUsers($docId);
if ($sharedResult) {
    while ($row = $sharedResult->fetch_assoc()) {
        if ($row['id'] == $userId) {
            $isSharedUser = true;
            break;
        }
    }
}

if (!$isOwner && !$isSharedUser && $userRole !== 'admin') {
    die("Access denied. You do not have permission to view this log.");
}

$logs = getActivityLogs($docId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Activity Logs - <?= htmlspecialchars($document['title']) ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .logs-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 30px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .logs-container h2 {
            color: #202124;
            font-size: 24px;
            margin-bottom: 20px;
        }

        .logs-container a {
            text-decoration: none;
            color: #4285F4;
            font-size: 14px;
            display: inline-block;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        th, td {
            padding: 12px 16px;
            text-align: left;
        }

        thead {
            background-color: #f1f3f4;
            color: #202124;
        }

        tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        tbody tr:hover {
            background-color: #eaf1fb;
        }

        @media (max-width: 768px) {
            table, thead, tbody, th, td, tr {
                display: block;
            }

            thead {
                display: none;
            }

            tr {
                margin-bottom: 15px;
                border: 1px solid #ddd;
                border-radius: 8px;
                padding: 10px;
                background: #fff;
            }

            td {
                display: flex;
                justify-content: space-between;
                padding: 8px 10px;
                border-bottom: 1px solid #eee;
            }

            td::before {
                content: attr(data-label);
                font-weight: bold;
                color: #5f6368;
                width: 40%;
            }
        }
    </style>
</head>
<body>
    <div class="logs-container">
        <a href="index.php">← Back to Dashboard</a>
        <h2>Activity Logs for: <?= htmlspecialchars($document['title']) ?></h2>

        <?php if ($logs && $logs->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Action</th>
                        <th>Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($log = $logs->fetch_assoc()): ?>
                        <tr>
                            <td data-label="User"><?= htmlspecialchars($log['name']) ?></td>
                            <td data-label="Action"><?= htmlspecialchars($log['action']) ?></td>
                            <td data-label="Timestamp"><?= $log['timestamp'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No activity logs found for this document.</p>
        <?php endif; ?>
    </div>
</body>
</html>
