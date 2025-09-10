<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard_" . $_SESSION['role'] . ".php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Assignment Portal - Welcome</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(to right, #74ebd5, #acb6e5);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: scale(1.03);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }

        .btn {
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="container text-center">
    <h1 class="text-white mb-5 fw-bold">📚 Assignment Submission Portal</h1>

    <div class="row justify-content-center g-4">
        <!-- Login Card -->
        <div class="col-md-4">
            <div class="card p-4 shadow-sm">
                <div class="card-body">
                    <i class="bi bi-box-arrow-in-right fs-1 text-primary mb-3"></i>
                    <h4 class="card-title mb-3">Already Registered?</h4>
                    <p class="card-text">Log in to upload or review assignments.</p>
                    <a href="login.php" class="btn btn-primary w-100 mt-2">Login</a>
                </div>
            </div>
        </div>

        <!-- Register Card -->
        <div class="col-md-4">
            <div class="card p-4 shadow-sm">
                <div class="card-body">
                    <i class="bi bi-person-plus-fill fs-1 text-success mb-3"></i>
                    <h4 class="card-title mb-3">New Here?</h4>
                    <p class="card-text">Create an account as student or teacher.</p>
                    <a href="register.php" class="btn btn-success w-100 mt-2">Register</a>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
