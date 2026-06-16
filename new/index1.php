<?php
$servername = "localhost";
$username= "root";
$password = "";
$database = "test1";

$connection = new mysqli($servername, $username, $password, $database);

if($connection->connect_error){
    die("Connection Failed: " . $connection->connect_error);
    exit;
}

//DELETE
if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    $connection->query("DELETE FROM student WHERE id=$id");
    header("location: /new/index1.php");
    exit;
}

//CREATE
if($_SERVER['REQUEST_METHOD'] == 'POST' && empty($_POST['hidden_id'])){
    $id=$_POST['id'];
    $name=$_POST['name'];
    $email=$_POST['email'];
    $address=$_POST['address'];
    $connection->query("INSERT INTO student (id, name, email, address) VALUES ('$id', '$name', '$email', '$address')");
    header("location: /new/index1.php");
    exit;
}

//UPDATE
if($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['hidden_id'])){
    $id=$_POST['hidden_id'];
    $name=$_POST['name'];
    $email=$_POST['email'];
    $address=$_POST['address'];
    $connection->query("UPDATE student SET name='$name', email='$email', address='$address' WHERE id='$id'");
    header("location: /new/index1.php");
    exit;
}

$editRow = null;
if(isset($_GET['edit'])){
    $id = $_GET['edit'];
    $result = $connection->query("SELECT * FROM student WHERE id='$id'");
    $editRow = $result->fetch_assoc();
}

$students = $connection->query("SELECT * FROM student");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NEW</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</head>
<body>
    <div class="container my-5">
        <h2><?= $editRow ? "Edit Student" : "Add Student" ?></h2>
        <form method="POST">
            <?php if($editRow): ?>
                <input type="hidden" name="hidden_id" value="<?= $editRow["id"] ?>">
            <?php endif; ?>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">ID</label>
                <div class="col-sm-6">
                    <?php if($editRow): ?>
                        <input type="number" class="form-control" disabled value="<?= $editRow["id"] ?>">
                    <?php else: ?>
                        <input type="number" class="form-control" name="id">
                    <?php endif; ?>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Name</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="name" value="<?= $editRow["name"] ?? ""?>">
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Email</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="email" value="<?= $editRow["email"] ?? ""?>">
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Address</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="address" value="<?= $editRow["address"] ?? ""?>">
                </div>
            </div>
            <div class="row mb-3">
                <div class="offset-sm-3 col-sm-3 d-grid">
                    <button type="submit" class="btn btn-primary"><?= $editRow ? "Update" : "Add" ?></button>
                </div>
            </div>

        </form>

        <h2>List</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $students->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= $row['name'] ?></td>
                        <td><?= $row['email'] ?></td>
                        <td><?= $row['address'] ?></td>
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