<?php
session_start();

// Include database connection
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare and execute the query
    $stmt = $conn->prepare("SELECT id, username, password_hash FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if user exists
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $user['password_hash'])) {
            // Set session variables
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_id'] = $user['id'];

            // Redirect to dashboard
            header("Location: dashboard.php");
            exit();
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "Invalid username.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Login - Kalasan</title>
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no" name="viewport" />

    <!-- Fonts and icons -->
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,200" rel="stylesheet" />
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" rel="stylesheet">

    <!-- CSS Files -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" />

    <style>
        body {
            background-image: url('assets/img/background.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
            font-family: 'Montserrat', sans-serif;
        }

        .background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: -1;
        }

        .tree {
            position: absolute;
            bottom: 0;
            animation: jump 2s infinite ease-in-out;
        }

        .tree:nth-child(1) {
            left: 10%;
            animation-delay: 0s;
        }

        .tree:nth-child(2) {
            left: 40%;
            animation-delay: 0.5s;
        }

        .tree:nth-child(3) {
            left: 70%;
            animation-delay: 1s;
        }

        @keyframes jump {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-50px);
            }
        }

        .sunlight {
            position: absolute;
            top: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(255, 255, 0, 0.2), rgba(255, 255, 0, 0.1), transparent);
            animation: sunlight 6s infinite alternate ease-in-out;
        }

        @keyframes sunlight {
            0% { transform: scale(1.1); opacity: 0.8; }
            100% { transform: scale(1.3); opacity: 1; }
        }

        .card {
            background-color: rgba(255, 255, 255, 0.95);
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            padding: 30px;
            width: 100%;
            max-width: 400px;
        }

        .card h4 {
            text-align: center;
            color: #4caf50;
        }

        .btn-block {
            margin-top: 20px;
            background-color: #4caf50;
            color: white;
            border: none;
        }

        .btn-block:hover {
            background-color: #388e3c;
        }

        .forgot a {
            color: #4caf50;
            text-decoration: none;
        }

        .forgot a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="card">
        <h4>Log In</h4>
        <form action="" method="POST">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" class="form-control" placeholder="Username" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" class="form-control" placeholder="Password" required>

            <button type="submit" class="btn btn-block">Log In</button>
        </form>
        <div class="forgot text-center mt-3">
            <a href="./register.php">Register</a>
        </div>
    </div>


    <!-- Core JS Files -->
    <script src="assets/js/core/jquery.min.js"></script>
    <script src="assets/js/core/popper.min.js"></script>
    <script src="assets/js/core/bootstrap.min.js"></script>
    <script src="assets/js/plugins/perfect-scrollbar.jquery.min.js"></script>
</body>

</html>
