<?php
require 'db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email    = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, name, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($id, $name, $hashed_password, $role);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['name']    = $name;
            $_SESSION['role']    = $role;

            header("Location: dashboard_" . $role . ".php");
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "No account found with that email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Assignment Portal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(to right, #ffecd2, #fcb69f);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card {
            width: 450px;
            margin: auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: scale(1.02);
        }

        .form-icon {
            font-size: 2rem;
            color: #0d6efd;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<div class="card bg-white">
    <div class="text-center">
        <i class="bi bi-box-arrow-in-right form-icon"></i>
        <h4 class="mb-3">Login to Your Account</h4>
    </div>

    <?php
    if (!empty($_GET['msg']) && $_GET['msg'] === 'registered') {
        echo "<div class='alert alert-success'>Registration successful. Please log in.</div>";
    }

    if (!empty($error)) {
        echo "<div class='alert alert-danger'>$error</div>";
    }
    ?>

    <form method="POST" class="mt-3">
        <div class="mb-3">
            <label class="form-label">Email address</label>
            <input type="email" name="email" class="form-control" required placeholder="you@example.com">
        </div>

        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required placeholder="••••••••">
        </div>

        <button type="submit" class="btn btn-primary w-100">Login</button>

        <div class="mt-3 text-center">
            <small>Don't have an account? <a href="register.php">Register here</a></small>
        </div>
    </form>
</div>

</body>
</html>
