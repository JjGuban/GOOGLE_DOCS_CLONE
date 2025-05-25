<?php
session_start();
require_once '../core/dbConfig.php';
require_once '../core/models.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: ../index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Document - Google Docs Clone</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f9;
            color: #333;
        }

        .create-doc-container {
            max-width: 600px;
            margin: 60px auto;
            padding: 40px;
            background-color: #fff;
            border-radius: 14px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.07);
        }

        h2 {
            color: #1a73e8;
            font-size: 26px;
            margin-bottom: 25px;
        }

        input[type="text"],
        textarea {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 15px;
            font-family: inherit;
            box-sizing: border-box;
        }

        button[type="submit"] {
            background-color: #1a73e8;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button[type="submit"]:hover {
            background-color: #0c5dc0;
        }

        #createMessage {
            margin-top: 15px;
            color: red;
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
    </style>
</head>
<body>
    <div class="create-doc-container">
        <h2>Create a New Document</h2>
        <form id="createDocForm">
            <input type="text" id="title" name="title" placeholder="Document Title" required>
            <textarea id="content" name="content" placeholder="Write something..." rows="10" required></textarea>
            <button type="submit">Create Document</button>
        </form>
        <div id="createMessage"></div>
        <a class="back-link" href="index.php">← Back to Dashboard</a>
    </div>

    <script>
    $('#createDocForm').submit(function(e) {
        e.preventDefault();

        $.post('../core/handleForms.php', {
            action: 'create_document',
            title: $('#title').val(),
            content: $('#content').val()
        }, function(response) {
            const res = JSON.parse(response);
            if (res.status === 'success') {
                alert('Document created successfully!');
                window.location.href = 'index.php';
            } else {
                $('#createMessage').text(res.message || "Failed to create document.");
            }
        });
    });
    </script>
</body>
</html>
