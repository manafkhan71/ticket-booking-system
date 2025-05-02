<?php
session_start();
require 'config.php';

$msg = '';
$msg_type = ''; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = htmlspecialchars(trim($_POST['username']));
    $password = $_POST['password'];

    if ($_POST['action'] === 'register') {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $qry = $dbconnc->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        try {
            $qry->execute([$username, $hash]);
            $msg = "Registration successful. Please login.";
            $msg_type = 'success';
        } catch (PDOException $e) {
            $msg = "Username already exists.";
            $msg_type = 'error';
        }
    } elseif ($_POST['action'] === 'login') {
        $qry = $dbconnc->prepare("SELECT * FROM users WHERE username = ?");
        $qry->execute([$username]);
        $user = $qry->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: dashboard.php");
            exit;
        } else {
            $msg = "Invalid username or password.";
            $msg_type = 'error';
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Login / Register</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="css/style.css" rel="stylesheet">
</head>

<body class="auth-page">
    <div class="container">
        <h2>Login or Register</h2>

        <?php if (!empty($msg)): ?>
            <p class="message <?= $msg_type ?>"><?= $msg ?></p>
        <?php endif; ?>

        <form method="POST">
            <div class="username-field">
                <input type="text" name="username" placeholder="Username" required>
            </div>

            <div class="password-wrapper">
                <input type="password" name="password" id="password" placeholder="Password" required>
                <span id="togglePassword" class="toggle-eye">
                    <i class="fa fa-eye"></i>
                </span>
            </div>

            <div>
                <button type="submit" name="action" value="login">Login</button>
                <button type="submit" name="action" value="register">Register</button>
            </div>
        </form>
    </div>

    <script>
        const passwordInput = document.getElementById('password');
        const togglePassword = document.getElementById('togglePassword');

        togglePassword.addEventListener('click', () => {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            togglePassword.innerHTML = type === 'password'
                ? '<i class="fa fa-eye"></i>'
                : '<i class="fa fa-eye-slash"></i>';
        });
    </script>
</body>

</html>