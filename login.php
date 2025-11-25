<?php
session_start();
include 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        header("Location: index.php");
        exit();
    } else {
        $error = "Invalid email or password";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Ticketly</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }

        .login-container {
            border-radius: 16px;
            padding: 50px;
            width: 100%;
            max-width: 450px;
        }

        .logo {
            width: 48px;
            height: 48px;
            background: #4F46E5;
            border-radius: 12px;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo-lines {
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .logo-line {
            width: 20px;
            height: 2px;
            background: white;
        }

        .logo-line:nth-child(2) {
            width: 14px;
        }

        .brand-name {
            text-align: center;
            font-size: 24px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 10px;
        }

        .page-title {
            text-align: center;
            font-size: 32px;
            font-weight: 800;
            color: #1a1a1a;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 8px;
        }

        .input-wrapper {
            position: relative;
        }

        input[type="email"],
        input[type="password"],
        input[type="text"] {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid #D1D5DB;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.2s;
            background: #F9FAFB;
        }

        input:focus {
            outline: none;
            border-color: #4F46E5;
            background: white;
        }

        .toggle-password {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #9CA3AF;
        }

        .forgot-password {
            text-align: left;
            margin-bottom: 25px;
        }

        .forgot-password a {
            color: #4F46E5;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
        }

        .forgot-password a:hover {
            text-decoration: underline;
        }

        .btn-submit {
            width: 100%;
            background: #4F46E5;
            color: white;
            padding: 16px;
            border-radius: 10px;
            border: none;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-submit:hover {
            background: #4338CA;
            transform: translateY(-1px);
        }

        .signup-link {
            text-align: center;
            margin-top: 20px;
            color: #6B7280;
            font-size: 14px;
        }

        .signup-link a {
            color: #4F46E5;
            text-decoration: none;
            font-weight: 600;
        }

        .signup-link a:hover {
            text-decoration: underline;
        }

        .error-message {
            background: #FEE2E2;
            color: #991B1B;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="logo">
            <div class="logo-lines">
                <div class="logo-line"></div>
                <div class="logo-line"></div>
                <div class="logo-line"></div>
            </div>
        </div>

        <div class="brand-name">Ticketly</div>

        <h1 class="page-title">Log in to your<br>account</h1>

        <?php if ($error): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" placeholder="Enter your email" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <div class="input-wrapper">
                    <input type="password" name="password" id="password" placeholder="Enter your password" required>
                    <svg class="toggle-password" onclick="togglePassword()" width="20" height="20" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                        <circle cx="12" cy="12" r="3" />
                    </svg>
                </div>
            </div>

            <div class="forgot-password">
                <a href="forgot_password.php">Forgot Password?</a>
            </div>

            <button type="submit" class="btn-submit">Login</button>
        </form>

        <div class="signup-link">
            Don't have an account? <a href="signup.php">Sign Up</a>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordField = document.getElementById('password');
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
            } else {
                passwordField.type = 'password';
            }
        }
    </script>
</body>

</html>