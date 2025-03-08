<?php
require 'db.php'; // Include database connection

// Create
if (isset($_POST['create'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];

    $sql = "INSERT INTO users (name, email) VALUES ('$name', '$email')";
    $conn->query($sql) or die($conn->error);
    header("Location: admin.php");
}

// Update
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];

    $sql = "UPDATE users SET name='$name', email='$email' WHERE id=$id";
    $conn->query($sql) or die($conn->error);
    header("Location: admin.php");
}

// Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    $sql = "DELETE FROM users WHERE id=$id";
    $conn->query($sql) or die($conn->error);
    header("Location: admin.php");
}

// Read all records
$result = $conn->query("SELECT * FROM users") or die($conn->error);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP CRUD</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <h2 class="text-center">PHP CRUD Application</h2>

        <!-- Create Form -->
        <form action="" method="POST" class="mb-4">
            <div class="mb-3">
                <label>Name</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <button type="submit" name="create" class="btn btn-primary">Add User</button>
        </form>

        <!-- Display Records -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= $row['name'] ?></td>
                    <td><?= $row['email'] ?></td>
                    <td><?= $row['created_at'] ?></td>
                    <td>
                        <a href="admin.php?edit=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="admin.php?delete=<?= $row['id'] ?>" class="btn btn-danger btn-sm"
                            onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Update Form -->
        <?php if (isset($_GET['edit'])): ?>
        <?php
            $id = $_GET['edit'];
            $record = $conn->query("SELECT * FROM users WHERE id=$id") or die($conn->error);
            $data = $record->fetch_assoc();
            ?>
        <form action="" method="POST">
            <input type="hidden" name="id" value="<?= $data['id'] ?>">
            <div class="mb-3">
                <label>Name</label>
                <input type="text" name="name" class="form-control" value="<?= $data['name'] ?>" required>
            </div>
            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="<?= $data['email'] ?>" required>
            </div>
            <button type="submit" name="update" class="btn btn-success">Update User</button>
            <a href="admin.php" class="btn btn-secondary">Cancel</a>
        </form>
        <?php endif; ?>
    </div>
</body>

</html>