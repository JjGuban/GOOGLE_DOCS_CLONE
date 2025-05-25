<?php
session_start();
require_once '../core/dbConfig.php';
require_once '../core/models.php';

// Only allow admin access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

$adminId = $_SESSION['user_id'];
$users = getAllUsersExcept($adminId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users - Admin</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .admin-manage-users {
            max-width: 960px;
            margin: 50px auto;
            padding: 40px;
            background-color: #fff;
            border-radius: 14px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.07);
        }

        h2 {
            color: #1a73e8;
            font-size: 26px;
            margin-bottom: 20px;
        }

        a.back-btn {
            display: inline-block;
            margin-bottom: 20px;
            padding: 10px 16px;
            background-color: #1a73e8;
            color: white;
            border-radius: 6px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        a.back-btn:hover {
            background-color: #0c5dc0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border-radius: 10px;
            overflow: hidden;
        }

        th, td {
            padding: 14px 16px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }

        th {
            background-color: #f1f3f6;
            color: #444;
            font-weight: 600;
        }

        tr:hover {
            background-color: #f9f9f9;
        }

        /* Custom Toggle Switch */
        .suspend-toggle {
            position: relative;
            width: 45px;
            height: 24px;
            appearance: none;
            background: #ccc;
            outline: none;
            border-radius: 50px;
            transition: 0.3s;
            cursor: pointer;
        }

        .suspend-toggle:checked {
            background: #4caf50;
        }

        .suspend-toggle::before {
            content: '';
            position: absolute;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            top: 3px;
            left: 3px;
            background: white;
            transition: 0.3s;
        }

        .suspend-toggle:checked::before {
            left: 24px;
        }

        @media (max-width: 600px) {
            table, thead, tbody, th, td, tr {
                display: block;
            }

            th {
                display: none;
            }

            td {
                padding: 10px;
                position: relative;
                border-bottom: none;
            }

            td::before {
                content: attr(data-label);
                font-weight: bold;
                color: #777;
                display: block;
                margin-bottom: 5px;
            }

            tr {
                margin-bottom: 15px;
                border: 1px solid #ddd;
                border-radius: 8px;
                padding: 10px;
                background: #fafafa;
            }
        }
    </style>
</head>
<body>
    <div class="admin-manage-users">
        <h2>Manage User Accounts</h2>
        <a href="index.php" class="back-btn">← Back to Admin Dashboard</a>

        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Suspended?</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = $users->fetch_assoc()): ?>
                    <tr>
                        <td data-label="Name"><?= htmlspecialchars($user['name']) ?></td>
                        <td data-label="Email"><?= htmlspecialchars($user['email']) ?></td>
                        <td data-label="Role"><?= htmlspecialchars($user['role']) ?></td>
                        <td data-label="Suspended">
                            <input type="checkbox" class="suspend-toggle" 
                                   data-id="<?= $user['id'] ?>" 
                                   <?= $user['suspended'] ? 'checked' : '' ?>>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script>
        $('.suspend-toggle').on('change', function () {
            const userId = $(this).data('id');
            const suspend = $(this).is(':checked') ? 1 : 0;

            $.post('../core/handleForms.php', {
                action: 'toggle_suspend',
                user_id: userId,
                suspend: suspend
            }, function (response) {
                const res = JSON.parse(response);
                if (res.status === 'success') {
                    alert('User status updated.');
                } else {
                    alert('Failed to update user status.');
                }
            });
        });
    </script>
</body>
</html>
