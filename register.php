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
    <title>Register - Google Docs Clone</title>
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

        .register-container {
            background-color: white;
            padding: 40px 30px;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 100%;
            max-width: 420px;
        }

        .register-container h2 {
            margin-bottom: 24px;
            font-weight: 600;
            color: #333;
        }

        .register-container input {
            width: 100%;
            padding: 12px 15px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 15px;
        }

        .register-container input:focus {
            outline: none;
            border-color: #1a73e8;
            box-shadow: 0 0 0 2px rgba(26, 115, 232, 0.2);
        }

        .register-container button {
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

        .register-container button:hover {
            background-color: #1669c1;
        }

        .register-container p {
            margin-top: 15px;
            font-size: 14px;
        }

        .register-container a {
            color: #1a73e8;
            text-decoration: none;
        }

        #registerMessage {
            color: red;
            font-size: 14px;
            margin-top: 12px;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h2>Register</h2>
        <form id="registerForm">
            <input type="text" id="name" name="name" placeholder="Full Name" required><br>
            <input type="email" id="email" name="email" placeholder="Email" required><br>
            <input type="password" id="password" name="password" placeholder="Password" required><br>
            <button type="submit">Register</button>
        </form>
        <p>Already have an account? <a href="index.php">Login here</a></p>
        <div id="registerMessage"></div>
    </div>

    <script>
    $(document).ready(function () {
        $('#registerForm').submit(function(e) {
            e.preventDefault();

            const name = $('#name').val().trim();
            const email = $('#email').val().trim();
            const password = $('#password').val();

            if (name === '' || email === '' || password === '') {
                $('#registerMessage').text('Please fill in all fields.');
                return;
            }

            $.post('core/handleForms.php', {
                action: 'register',
                name: name,
                email: email,
                password: password
            }, function(response) {
                try {
                    const res = JSON.parse(response);
                    if (res.status === 'success') {
                        alert("Registration successful. Redirecting to login...");
                        window.location.href = "index.php";
                    } else {
                        $('#registerMessage').text(res.message || "Registration failed.");
                    }
                } catch (err) {
                    $('#registerMessage').text("Unexpected server response.");
                    console.error("Invalid JSON response:", response);
                }
            });
        });
    });
    </script>
</body>
</html>
