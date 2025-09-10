<?php
require 'db.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name       = trim($_POST['name']);
    $email      = trim($_POST['email']);
    $password   = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role       = $_POST['role'];
    $class_name = null;

    // Only require and store class_name if role is student
    if ($role === 'student') {
        $class_name = trim($_POST['class_name']);
        if (empty($class_name)) {
            $error = "Class name is required for students.";
        }
    }

    if (!$error) {
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, class_name) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $email, $password, $role, $class_name);

        if ($stmt->execute()) {
            header("Location: login.php?msg=registered");
            exit();
        } else {
            $error = "Registration failed. Email might already be used.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - Assignment Portal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(to right, #d4fc79, #96e6a1);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card {
            width: 800px;
            margin: auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
            transition: transform 0.2s ease;
        }

        .card:hover {
            transform: scale(1.01);
        }

        .form-icon {
            font-size: 2rem;
            color: #198754;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<div class="card bg-white">
    <div class="text-center">
        <i class="bi bi-person-plus-fill form-icon"></i>
        <h4 class="mb-3">Create Your Account</h4>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

   <form method="POST" class="mt-3" id="registerForm">
    <div class="mb-3">
        <label class="form-label">Full Name</label>
        <input type="text" name="name" class="form-control" required placeholder="John Doe" value="<?= isset($name) ? htmlspecialchars($name) : '' ?>">
    </div>

    <div class="mb-3">
        <label class="form-label">Select Role</label>
        <select name="role" class="form-select" id="roleSelect" required>
            <option value="" disabled <?= !isset($role) ? 'selected' : '' ?>>Choose role</option>
            <option value="student" <?= (isset($role) && $role === 'student') ? 'selected' : '' ?>>Student</option>
            <option value="teacher" <?= (isset($role) && $role === 'teacher') ? 'selected' : '' ?>>Teacher</option>
        </select>
    </div>

    <div class="mb-3" id="classDiv" style="display:none;">
        <label class="form-label">Class Name</label>
        <input type="text" name="class_name" class="form-control" placeholder="e.g., B.Sc Grade, Physics 101" value="<?= isset($class_name) ? htmlspecialchars($class_name) : '' ?>">
    </div>

    <div class="mb-3">
        <label class="form-label">Email Address</label>
        <input type="email" name="email" class="form-control" required placeholder="you@example.com" value="<?= isset($email) ? htmlspecialchars($email) : '' ?>">
    </div>

    <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required placeholder="••••••••">
    </div>

    <button type="submit" class="btn btn-success w-100">Register</button>

    <div class="mt-3 text-center">
        <small>Already have an account? <a href="login.php">Login here</a></small>
    </div>
</form>

</div>

<script>
    const roleSelect = document.getElementById('roleSelect');
    const classDiv = document.getElementById('classDiv');
    const classInput = classDiv.querySelector('input');

    function toggleClassField() {
        if (roleSelect.value === 'student') {
            classDiv.style.display = 'block';
            classInput.setAttribute('required', 'required');
        } else {
            classDiv.style.display = 'none';
            classInput.removeAttribute('required');
            classInput.value = '';
        }
    }

    roleSelect.addEventListener('change', toggleClassField);

    // Run on page load in case of form validation errors
    window.addEventListener('DOMContentLoaded', toggleClassField);
</script>

</body>
</html>
