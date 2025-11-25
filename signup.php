<?php
session_start();
include 'config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $check_sql = "SELECT * FROM users WHERE email = '$email'";
        $check_result = mysqli_query($conn, $check_sql);

        if (mysqli_num_rows($check_result) > 0) {
            $error = "Email already exists. Please use a different email.";
        } else {
            $sql = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$password')";

            if (mysqli_query($conn, $sql)) {
                $success = "Account created successfully! Redirecting to login...";
                header("refresh:2;url=login.php");
            } else {
                $error = "Error: " . mysqli_error($conn);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Ticketly</title>
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

        .signup-container {
            width: 100%;
            max-width: 450px;
        }

        .logo {
            width: 56px;
            height: 56px;
            background: #4F46E5;
            border-radius: 14px;
            margin: 0 auto 25px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo-icon {
            width: 30px;
            height: 30px;
            stroke: white;
        }

        .page-title {
            text-align: center;
            font-size: 32px;
            font-weight: 800;
            color: #1a1a1a;
            margin-bottom: 10px;
        }

        .page-subtitle {
            text-align: center;
            font-size: 15px;
            color: #6B7280;
            margin-bottom: 35px;
            line-height: 1.5;
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

        input.error {
            border-color: #EF4444;
            background: #FEF2F2;
        }

        .toggle-password {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #9CA3AF;
        }

        .error-text {
            color: #EF4444;
            font-size: 13px;
            margin-top: 6px;
            display: none;
        }

        .error-text.show {
            display: block;
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
            margin-top: 10px;
        }

        .btn-submit:hover {
            background: #4338CA;
            transform: translateY(-1px);
        }

        .login-link {
            text-align: center;
            margin-top: 20px;
            color: #6B7280;
            font-size: 14px;
        }

        .login-link a {
            color: #4F46E5;
            text-decoration: none;
            font-weight: 600;
        }

        .login-link a:hover {
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

        .success-message {
            background: #D1FAE5;
            color: #065F46;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="signup-container">
        <div class="logo">
            <svg class="logo-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                <line x1="16" y1="2" x2="16" y2="6" />
                <line x1="8" y1="2" x2="8" y2="6" />
                <line x1="3" y1="10" x2="21" y2="10" />
            </svg>
        </div>

        <h1 class="page-title">Create an Account</h1>
        <p class="page-subtitle">Join us to discover and book tickets for your favorite events.</p>

        <?php if ($error): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success-message"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST" action="" id="signupForm">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" id="name" placeholder="Jane Doe" required>
            </div>

            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" id="email" placeholder="jane.doe@email.com" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <div class="input-wrapper">
                    <input type="password" name="password" id="password" placeholder="Enter your password" required>
                    <svg class="toggle-password" onclick="togglePassword('password')" width="20" height="20"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                        <circle cx="12" cy="12" r="3" />
                    </svg>
                </div>
            </div>

            <div class="form-group">
                <label>Confirm Password</label>
                <div class="input-wrapper">
                    <input type="password" name="confirm_password" id="confirm_password"
                        placeholder="Confirm your password" required>
                    <svg class="toggle-password" onclick="togglePassword('confirm_password')" width="20" height="20"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                        <circle cx="12" cy="12" r="3" />
                    </svg>
                </div>
                <div class="error-text" id="passwordError">Passwords do not match.</div>
            </div>

            <button type="submit" class="btn-submit">Create Account</button>
        </form>

        <div class="login-link">
            Already have an account? <a href="login.php">Log In</a>
        </div>
    </div>

    <script>
        function togglePassword(fieldId) {
            const passwordField = document.getElementById(fieldId);
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
            } else {
                passwordField.type = 'password';
            }
        }

        // Real-time password match validation
        document.getElementById('confirm_password').addEventListener('input', function () {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            const errorText = document.getElementById('passwordError');

            if (confirmPassword && password !== confirmPassword) {
                this.classList.add('error');
                errorText.classList.add('show');
            } else {
                this.classList.remove('error');
                errorText.classList.remove('show');
            }
        });

        document.getElementById('password').addEventListener('input', function () {
            const confirmPassword = document.getElementById('confirm_password').value;
            const errorText = document.getElementById('passwordError');

            if (confirmPassword && this.value !== confirmPassword) {
                document.getElementById('confirm_password').classList.add('error');
                errorText.classList.add('show');
            } else {
                document.getElementById('confirm_password').classList.remove('error');
                errorText.classList.remove('show');
            }
        });
    </script>
</body>

</html>