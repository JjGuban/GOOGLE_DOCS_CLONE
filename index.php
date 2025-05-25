<?php
session_start();
if (isset($_SESSION['user_id'])) {
    // Redirect logged-in users
    if ($_SESSION['role'] === 'admin') {
        header('Location: admin/index.php');
    } else {
        header('Location: users/index.php');
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Google Docs Clone</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f6f8;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .login-container {
            background-color: white;
            padding: 40px 30px;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 100%;
            max-width: 400px;
        }

        .login-container h2 {
            margin-bottom: 24px;
            font-weight: 600;
            color: #333;
        }

        .login-container input {
            width: 100%;
            padding: 12px 15px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 15px;
        }

        .login-container input:focus {
            outline: none;
            border-color: #1a73e8;
            box-shadow: 0 0 0 2px rgba(26, 115, 232, 0.2);
        }

        .login-container button {
            width: 100%;
            padding: 12px;
            background-color: #1a73e8;
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.2s ease-in-out;
        }

        .login-container button:hover {
            background-color: #1669c1;
        }

        .login-container p {
            margin-top: 15px;
            font-size: 14px;
        }

        .login-container a {
            color: #1a73e8;
            text-decoration: none;
        }

        #loginMessage {
            color: red;
            font-size: 14px;
            margin-top: 12px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <form id="loginForm">
            <input type="email" id="email" name="email" placeholder="Email" required><br>
            <input type="password" id="password" name="password" placeholder="Password" required><br>
            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="register.php">Register here</a></p>
        <div id="loginMessage"></div>
    </div>

    <script>
    $('#loginForm').submit(function(e) {
        e.preventDefault();

        $.post('core/handleForms.php', {
            action: 'login',
            email: $('#email').val(),
            password: $('#password').val()
        }, function(response) {
            const res = JSON.parse(response);
            if (res.status === 'success') {
                window.location.reload(); // Auto-redirect based on role
            } else {
                $('#loginMessage').text(res.message);
            }
        });
    });
    </script>
</body>
</html>
