<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "test1";

$connection = new mysqli($servername, $username, $password, $database);
if($connection->connect_error){
    die("Connection Failed: " . $connection->connect_error);
    exit;
}

$errors = [];

// Delete
if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    $stmt = $connection->prepare("DELETE FROM student WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("location: /new/test1.php");
    exit;
}

// Fetch row for edit
$editRow = null;
if(isset($_GET['edit'])){
    $id = $_GET['edit'];
    $stmt = $connection->prepare("SELECT * FROM student WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $editRow = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// Create
if($_SERVER['REQUEST_METHOD'] == 'POST' && empty($_POST['hidden_id'])){
    $id       = trim($_POST['id']);
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $address  = trim($_POST['address']);

    if(empty($id))      $errors[] = "ID is required.";
    if(empty($name))    $errors[] = "Name is required.";
    if(empty($email))   $errors[] = "Email is required.";
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format.";
    if(empty($address)) $errors[] = "Address is required.";

    if(empty($errors)){
        $stmt = $connection->prepare("INSERT INTO student (id, name, email, address) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $id, $name, $email, $address);
        $stmt->execute();
        $stmt->close();
        header("location: /new/test1.php");
        exit;
    }
}

// Update
if($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['hidden_id'])){
    $id      = $_POST['hidden_id'];
    $name    = trim($_POST['name']);
    $email   = trim($_POST['email']);
    $address = trim($_POST['address']);

    if(empty($name))    $errors[] = "Name is required.";
    if(empty($email))   $errors[] = "Email is required.";
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format.";
    if(empty($address)) $errors[] = "Address is required.";

    if(empty($errors)){
        $stmt = $connection->prepare("UPDATE student SET name = ?, email = ?, address = ? WHERE id = ?");
        $stmt->bind_param("sssi", $name, $email, $address, $id);
        $stmt->execute();
        $stmt->close();
        header("location: /new/test1.php");
        exit;
    }

    // Re-populate editRow so form stays filled on validation error
    $editRow = ['id' => $id, 'name' => $name, 'email' => $email, 'address' => $address];
}

// Read
$students = $connection->query("SELECT * FROM student");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>
<body>
    <div class="container my-5">
        <h2><?= $editRow ? "Edit Student" : "Add Student" ?></h2>

        <?php if(!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST">
            <?php if($editRow): ?>
                <input type="hidden" name="hidden_id" value="<?= htmlspecialchars($editRow['id']) ?>">
            <?php endif; ?>

            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">ID</label>
                <div class="col-sm-6">
                    <?php if($editRow): ?>
                        <input type="number" class="form-control" disabled value="<?= htmlspecialchars($editRow['id']) ?>">
                    <?php else: ?>
                        <input type="number" class="form-control" name="id" value="<?= htmlspecialchars($_POST['id'] ?? '') ?>">
                    <?php endif; ?>
                </div>
            </div>

            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Name</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($editRow['name'] ?? $_POST['name'] ?? '') ?>">
                </div>
            </div>

            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Email</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="email" value="<?= htmlspecialchars($editRow['email'] ?? $_POST['email'] ?? '') ?>">
                </div>
            </div>

            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Address</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="address" value="<?= htmlspecialchars($editRow['address'] ?? $_POST['address'] ?? '') ?>">
                </div>
            </div>

            <div class="row mb-3">
                <div class="offset-sm-3 col-sm-3 d-grid">
                    <button class="btn btn-primary" type="submit"><?= $editRow ? "Edit Student" : "Add Student" ?></button>
                </div>
            </div>
        </form>

        <h2>List of Students</h2>
        <br>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $students->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['address']) ?></td>
                        <td>
                            <a class="btn btn-primary" href="?edit=<?= $row['id'] ?>">Edit</a>
                            <a class="btn btn-danger" href="?delete=<?= $row['id'] ?>">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>