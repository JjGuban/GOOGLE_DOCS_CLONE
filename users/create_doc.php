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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f9;
            color: #333;
        }

        .create-doc-container {
            max-width: 800px;
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

        input[type="text"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 15px;
            font-family: inherit;
            box-sizing: border-box;
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
            min-height: 300px;
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 12px;
            font-size: 15px;
            background-color: #fff;
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
            margin-top: 15px;
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

            <div id="editor" contenteditable="true"></div>
            <input type="hidden" id="content" name="content">
            <button type="submit">Create Document</button>
        </form>
        <div id="createMessage"></div>
        <a class="back-link" href="index.php">← Back to Dashboard</a>
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

    $('#createDocForm').submit(function(e) {
        e.preventDefault();
        $('#content').val($('#editor').html());

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
