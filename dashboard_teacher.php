<?php
require 'db.php';
session_start();

// Redirect if not logged in or not teacher
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: login.php");
    exit();
}

// Handle grade submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assignment_id'], $_POST['grade'])) {
    $assignment_id = intval($_POST['assignment_id']);
    $grade = $_POST['grade'];

    $stmt = $conn->prepare("UPDATE assignments SET grade = ? WHERE id = ?");
    $stmt->bind_param("si", $grade, $assignment_id);
    $stmt->execute();
}

// Get all assignments
$query = "SELECT a.id, a.file_name, a.uploaded_at, a.grade, u.name AS student_name, u.email 
          FROM assignments a
          JOIN users u ON a.user_id = u.id
          ORDER BY a.uploaded_at DESC";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Teacher Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center">
        <h3>Welcome, <?= htmlspecialchars($_SESSION['name']) ?> (Teacher)</h3>
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>

    <hr>

    <h5>All Student Assignments</h5>

    <table class="table table-bordered bg-white">
        <thead>
            <tr>
                <th>#</th>
                <th>Student</th>
                <th>Email</th>
                <th>File</th>
                <th>Uploaded At</th>
                <th>Download</th>
                <th>Grade</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): $i = 1; ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td><?= htmlspecialchars($row['student_name']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= basename($row['file_name']) ?></td>
                        <td><?= $row['uploaded_at'] ?></td>
                        <td><a href="<?= $row['file_name'] ?>" class="btn btn-sm btn-primary" download>Download</a></td>
                        <td>
                            <form method="POST" class="d-flex">
                                <input type="hidden" name="assignment_id" value="<?= $row['id'] ?>">
                                <input type="text" name="grade" value="<?= htmlspecialchars($row['grade']) ?>" class="form-control form-control-sm me-2" placeholder="Grade" style="width: 80px;">
                                <button type="submit" class="btn btn-sm btn-success">Save</button>
                            </form>
                        </td>
                        <td>
                            <?php if (!empty($row['grade'])): ?>
                                <span class="badge bg-success">Graded</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Pending</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="8" class="text-center">No assignments found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
