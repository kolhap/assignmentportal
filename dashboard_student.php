<?php
require 'db.php';
session_start();

// Redirect if not logged in or not student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

// Upload assignment logic
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['assignment'])) {
    $file_name = basename($_FILES['assignment']['name']);
    $upload_dir = "uploads/";

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $target_file = $upload_dir . time() . "_" . $file_name;

    if (move_uploaded_file($_FILES['assignment']['tmp_name'], $target_file)) {
        $user_id = $_SESSION['user_id'];
        $stmt = $conn->prepare("INSERT INTO assignments (user_id, file_name) VALUES (?, ?)");
        $stmt->bind_param("is", $user_id, $target_file);
        $stmt->execute();
        $success = "Assignment uploaded successfully!";
    } else {
        $error = "Failed to upload file.";
    }
}

// Fetch user's assignments
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM assignments WHERE user_id = ? ORDER BY uploaded_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center">
        <h3>Welcome, <?= htmlspecialchars($_SESSION['name']) ?> (Student)</h3>
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>

    <hr>

    <h5>Upload Assignment</h5>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php elseif (!empty($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="mb-4">
        <div class="input-group">
            <input type="file" name="assignment" class="form-control" required>
            <button type="submit" class="btn btn-primary">Upload</button>
        </div>
    </form>

    <h5>Your Uploaded Assignments</h5>
    <table class="table table-bordered bg-white">
        <thead>
            <tr>
                <th>#</th>
                <th>File Name</th>
                <th>Uploaded At</th>
                <th>Download</th>
                <th>Grade</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): $i = 1; ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td><?= basename($row['file_name']) ?></td>
                        <td><?= $row['uploaded_at'] ?></td>
                        <td><a href="<?= $row['file_name'] ?>" class="btn btn-sm btn-success" download>Download</a></td>
                        <td><?= $row['grade'] ?? 'Not graded yet' ?></td>

                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="4" class="text-center">No assignments uploaded.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
