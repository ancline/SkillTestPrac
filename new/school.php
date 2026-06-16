<?php
$servername="localhost";
$username="root";
$password="";
$database="school";

$conn = new mysqli($servername, $username, $password, $database);
if($conn->connect_error){
    die("Connection Failed: " . $conn->connect_error);
}

$msg = "";
$active = $_GET['tab'] ?? 'teachers';
// -------------------Teachers ------------------------
//Add Teacher
if(isset($_POST['add_teacher'])){
    $id = trim($_POST['teacherID']);
    $fn = trim($_POST['teacherFName']);
    $ln = trim($_POST['teacherLName']);
    $sub = trim($_POST['teacherSubject']);
    $email = trim($_POST['teacherEmail']);
    $conn->query("INSERT INTO teacher (teacherID, teacherFName, teacherLName, teacherSubject, teacherEmail) VALUES ('$id', '$fn', '$ln', '$sub', '$email')");
    $msg = "Teacher added successfully";
}

// Delete Teacher
if(isset($_GET['delete_teacher'])){
    $id = trim($_GET['teacherID']);
    $conn->query("DELETE FROM teacher WHERE teacherID = '$id'");
    $msg = "Teacher deleted from the database";
    $active = 'teachers';
}

//Update Teacher
if(isset($_POST['update_teacher'])){
    $id = trim($_POST['teacherID']);
    $fn = trim($_POST['teacherFName']);
    $ln = trim($_POST['teacherLName']);
    $sub = trim($_POST['teacherSubject']);
    $email = trim($_POST['teacherEmail']);
    $conn->query("UPDATE teacher SET teacherFName = '$fn', teacherLName = '$ln', teacherSubject = '$sub', teacherEmail = '$email' WHERE teacherID = '$id'");
    $msg = "Teacher info updated successfully";
    $active = 'teachers';
}

$edit_teacher = null;
if(isset($_GET['edit_teacher'])){
    $r = $conn->query("SELECT * FROM teacher WHERE teacherID=" . intval($_GET['edit_teacher']));
    $edit_teacher = $r->fetch_assoc();
    $active = 'teachers';
}

$search_teach = $_POST['search_teach'] ?? '';
$teach_sql = "SELECT * FROM teacher";
if($search_teach) $teach_sql .= " WHERE teacherFName LIKE '%$search_teach%' OR teacherLName LIKE '%$search_teach%' OR teacherSubject LIKE '%$search_teach%' OR teacherEmail LIKE '%$search_teach%'";
$teacher = $conn->query("$teach_sql");
$teach_count = $teacher->num_rows;


// ---------------------------Student-------------------
//Add Student
if(isset($_POST['add_student'])){
    $id = trim($_POST['studentID']);
    $fn = trim($_POST['studentFName']);
    $ln = trim($_POST['studentLName']);
    $email = trim($_POST['studentEmail']);
    $date = trim($_POST['studentBDate']);
    $conn->query("INSERT INTO student (studentID, studentFName, studentLName, studentEmail, studentBDate) VALUES ('$id', '$fn', '$ln', '$email', '$date')");
    $active = 'students';
}

//Update Student
if(isset($_POST['update_student'])){
    $id = trim($_POST['studentID']);
    $fn = trim($_POST['studentFName']);
    $ln = trim($_POST['studentLName']);
    $email = trim($_POST['studentEmail']);
    $date = trim($_POST['studentBDate']);
    $conn->query("UPDATE student SET studentFName = '$fn', ")
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>
<body class="bg-light">
    <nav class="navbar navber-dark bg-primary">
        <div class="container">
            <span class="navber-brand fw-bold">School Management System</span>
        </div>
    </nav>
    
    <div class="container my-4">
        <?php if($msg): ?>
            <div class="alert alert-success alert-dismissisble fade show">
                <?= htmlspecialchars($msg) ?>
                <button type="button" class="btn btn-close"></button>
            </div>
        <?php endif; ?>

        <?php $tabs = ['teachers' => 'Teachers', 'students' => 'Student', 'enroll' => 'Enroll']; ?>
        <ul class="nav nav-tabs mb-4">
            <?php foreach($tabs as $key => $label): ?>
                <li class="nav-items">
                    <a class="nav-link <?= $active === $key ? 'active' : '' ?>" href="?tab=<?= $key ?>"><?= $label ?></a>
                </li>
            <?php endforeach; ?>
        </ul>

        <?php if ($active === 'teachers'): ?>
            <div class="row g-3">
                <div class="col-md-7">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <strong>Teachers<span class="badge bg-primary"><?= $teach_count ?></span></strong>
                            <form method="POST" class="d-flex fap-2">
                                <input type="text" name="search_teach" class="form-control form-control-sm" placeholder="Search..." value="<?= htmlspecialchars($search_teach) ?>">
                                <button class="btn tbn-sm btn-outline-primary">Search</button>
                                <?php if($search_teach): ?><a href="?tab=teachers" class="btn btn-sm btn-outline-secondary">Clear</a><?php endif; ?>
                            </form>
                        </div>
                        <div class="card-body p-0">
                            <div class="table table-bordered table-hover mb-0">
                                <table class="table table-hover table-bordered mb-0">
                                    <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>First Name</th>
                                        <th>Last Name</th>
                                        <th>Subject</th>
                                        <th>Email</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($row = $teacher->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($row['teacherID']) ?></td>
                                            <td><?= htmlspecialchars($row['teacherFName']) ?></td>
                                            <td><?= htmlspecialchars($row['teacherLName']) ?></td>
                                            <td><?= htmlspecialchars($row['teacherSubject']) ?></td>
                                            <td><?= htmlspecialchars($row['teacherEmail']) ?></td>
                                            <td>
                                                <a href="?tab=teachers&edit_teacher=<?= $row['teacherID'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                                <a href="?tab=teachers&edit_teacher=<?= $row['teacherID'] ?>" class="btn btn-danger btn-sm">Delete</a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                        <div class="card">
                            <div class="card-header"><?= $edit_teacher ? 'Edit Teacher' : 'Add Teacher' ?></div>
                            <div class="card-body">
                                <form method="POST">
                                    <?php if($edit_teacher): ?>
                                        <input type="hidden" name="teacherID" value="<?= $edit_teacher['teacherID'] ?>">
                                    <?php else: ?>
                                        <div class="mb-2">
                                            <label class="form-label">Teacher ID</label>
                                            <input type="number" name="teacherID" class="form-control" required>
                                        </div>
                                    <?php endif; ?>
                                    <div class="mb-2">
                                        <label class="form-label">First Name</label>
                                        <input type="text" name="teacherFName" class="form-control" required value="<?= htmlspecialchars($edit_teacher) ?? '' ?>">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">Last Name</label>
                                        <input type="text" name="teacherLName" class="form-control" required value="<?= htmlspecialchars($edit_teacher) ?? '' ?>">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">Subject</label>
                                        <input type="text" name="teacherSubject" class="form-control" required value="<?= htmlspecialchars($edit_teacher) ?? ''?>">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">Email</label>
                                        <input type="email" name="teacherEmail" class="form-control" required value="<?= htmlspecialchars($edit_teacher) ?? ''?>">
                                    </div>
                                    <?php if($edit_teacher): ?>
                                        <button type="submit" name="update_docotr" class="btn btn-warning w-100">Update</button>
                                        <a href="?tab=teachers" class="btn btn-secondary w-100 mt-2">Cancel</a>
                                    <?php else: ?>
                                        <button type="submit" name="add_teacher" class="btn btn-primary w-100">Add Teacher</button>
                                    <?php endif; ?>
                                </form>
                            </div>
                        </div>
                    </div>
            </div>
        <?php endif; ?>

        <?php if($active === 'students'): ?>
            <div class="row g-3">
                <div class="col-md-7">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center flex-wrap hap-2">
                            <strong>Students<span class="badge bg-primary"><?= $student_count ?></span></strong>
                            <form method="POST">
                                <input type="text" name="search_student" class="form-control-sm" placeholder="Search..." value="<?= htmlspecialchars($search_student) ?>">
                                <button class="btn btn-sm btn-outline-primary">Search</button>
                                <?php if($search_student): ?><a href="?tab=students" class="btn btn-sm btn-outline-secondary">Clear</a><?php endif; ?>
                            </form>
                        </div>
                        <div class="card-body p-0">
                            <div class="table table-bordered table-hover mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Student ID</th>
                                        <th>First Name</th>
                                        <th>Last Name</th>
                                        <th>Email</th>
                                        <th>Birthday</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($row = $students->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($row['studentID']) ?></td>
                                            <td><?= htmlspecialchars($row['studentFName']) ?></td>
                                            <td><?= htmlspecialchars($row['studentLName']) ?></td>
                                            <td><?= htmlspecialchars($row['studentEmail']) ?></td>
                                            <td><?= htmlspecialchars($row['studentEmail']) ?></td>
                                            <td>
                                                <a href="?tab=students&edit_student=<?= $row['studentID'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                                <a href="?tab=students&delete_student=<?= $row['studentID'] ?>" class="btn btn-warning btn sm">Delete</a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>