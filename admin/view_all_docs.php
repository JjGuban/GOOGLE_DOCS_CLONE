<?php
session_start();
require_once '../core/dbConfig.php';
require_once '../core/models.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

$admin = getUserById($_SESSION['user_id']);
$allDocs = getAllDocuments();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Documents - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .view-all-docs {
            max-width: 1200px;
            margin: 40px auto;
            padding: 30px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .view-all-docs h2 {
            font-size: 26px;
            color: #1a73e8;
            margin-bottom: 20px;
        }

        .view-all-docs a.back-link {
            text-decoration: none;
            color: #4285F4;
            font-size: 14px;
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

        .table-actions a {
            color: #4285F4;
            margin-right: 10px;
            text-decoration: none;
            font-weight: 500;
        }

        .table-actions a:hover {
            text-decoration: underline;
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

            .table-actions {
                flex-direction: column;
                gap: 8px;
            }
        }
    </style>
</head>
<body>
    <div class="view-all-docs">
        <a href="index.php" class="back-link">← Back to Admin Dashboard</a>
        <h2>All Documents in the System</h2>

        <?php if ($allDocs->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Owner</th>
                        <th>Last Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($doc = $allDocs->fetch_assoc()): ?>
                        <tr>
                            <td data-label="Title"><?= htmlspecialchars($doc['title']) ?></td>
                            <td data-label="Owner"><?= htmlspecialchars($doc['owner_name']) ?></td>
                            <td data-label="Last Updated"><?= $doc['updated_at'] ?></td>
                            <td data-label="Actions" class="table-actions">
                                <?php if ($doc['owner_id'] == $_SESSION['user_id']): ?>
                                    <a href="../users/edit_doc.php?doc_id=<?= $doc['id'] ?>">Edit</a>
                                <?php endif; ?>
                                <a href="../users/activity_logs.php?doc_id=<?= $doc['id'] ?>">Logs</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No documents found in the system.</p>
        <?php endif; ?>
    </div>
</body>
</html>
