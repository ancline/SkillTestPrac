<?php
$servername = "localhost";
$username   = "root";
$password   = "";
$database   = "Registration";

$connection = new mysqli($servername, $username, $password, $database);
if ($connection->connect_error) {
    die("Connection Failed: " . $connection->connect_error);
}

$msg = "";
$msg_type = "";
$page = $_GET['page'] ?? 'menu';

// Registration
if(isset($_POST['add'])){
    $id     = trim($_POST['idNum']);
    $campus = trim($_POST['campus']);
    $fn     = trim($_POST['studFName']);
    $ln     = trim($_POST['studLName']);
    $amt    = trim($_POST['amountPaid']);
    $check = $connection->query("SELECT idNum FROM Registration WHERE idNum='$id'");
    if($check->num_rows > 0){
        $msg = "ID $id already exists."; $msg_type = "danger";
    } else {
        $connection->query("INSERT INTO Registration (idNum, campus, studFName, studLName, amountPaid) VALUES ('$id', '$campus', '$fn', '$ln', '$amt')");
        $msg = "Student Registered Successfully";
    }
}

if(isset($_POST['update'])){
    $id     = trim($_POST['idNum']);
    $campus = trim($_POST['campus']);
    $fn     = trim($_POST['studFName']);
    $ln     = trim($_POST['studLName']);
    $amt    = trim($_POST['amountPaid']);
    $connection->query("UPDATE Registration SET  campus ='$campus', studFName = '$fn', studLName = '$ln', amountPaid = '$amt' WHERE idNum = '$id'");
    $msg = "Student info updated successfully";
}

if(isset($_GET['delete'])){
    $id = trim($_GET['delete']);
    $connection->query("DELETE FROM Registration WHERE idNum ='$id'");
    $msg = "Student deleted Successfully";
}

$edit = null;
if(isset($_GET['edit'])){
    $r = $connection->query("SELECT * FROM Registration WHERE idNum='" . trim($_GET['edit']). "'");
    $edit = $r->fetch_assoc();
}

$rows = $connection->query("SELECT * FROM Registration");
$row_count = $rows->num_rows;

//Attendance
$student = null;
if(isset($_POST['check'])){
    $id = trim($_POST['idNum']);
    $result = $connection->query("SELECT * FROM Registration WHERE idNum = '$id'");
    if($result->num_rows === 0){
        $msg = "ID# $id is NOT YET REGISTERED."; 
        $msg_type = "danger";
    } else {
        $student = $result->fetch_assoc();
        if($student['attended'] === 'Yes'){
            $msg ="Student's attendance RECORD ALREADY EXISTS";
            $msg_type="warning";

        } else {
            $connection->query("UPDATE Registration SET attended='Yes' WHERE idNum='$id'");
            $msg="Attendance SUCCESSFULLY RECORDED";
            $msg_type="success";
            $student['attended'] ='Yes';
        }
    }
}

//Raffle
$winner = null;
$selected_campuses = $_POST['campus'] ?? ['Main', 'Banilad', 'LM', 'Pardo'];
if(isset($_POST['reveal'])){
    $list = implode(",", $selected_campuses);
    $result = $connection->query("SELECT * FROM Registration WHERE campus IN ('$list') ORDER BY Rand() LIMIT 1");
    $winner = $result->fetch_assoc();
}

// report by campus
$selected_report = $_post['campuses_report'] ?? [];
$report_rows = null;
if(isset($_POST['generate']) && $selected_report){
    $list = implode(",", $selected_report);
    $report_rows = $connection->query("SELECT * FROM Registration WHERE campus IN ($list) ORDER BY campus");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-primary">
        <div class="container">
            <span class="navbar-brand fw-bold">UC ICT CONGRESS REGISTRATION</span>
            <?php if($page !== 'menu'): ?>
                <a href="?page=menu" class="btn btn-outline-light btn-sm">Back To Menu</a>
            <?php endif; ?>
        </div>
    </nav>

<div class="container my-4">
    <?php if($msg): ?>
        <div class="alert alert-<?= $msg_type ?> alert-dismissible fade show">
            <?= htmlspecialchars($msg) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if($page === 'menu'): ?>
        <div class="card mx-auto" style="max-width:400px">
            <div class="card-header text-center fw-bold">Choose your transaction</div>
            <div class="card-body d-grid gap-2">
                <?php foreach ([
                    'registration' => "Registration",
                    'attendance' => "Attendance",
                    'raffle' => 'Raffle',
                    'by_campus' => "Report (Campus)",
                    'summary' => 'Rport Summary',
                ] as $p => $label): ?>
                    <a href="?page=<?= $p ?>" class="btn btn-outline-primary"><?= $label ?></a>
                <?php endforeach; ?>
            </div>
        </div>

    <?php elseif($page === 'registration'): ?>
        <div class="row g-3">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header fw-bold">
                        Registered Students <span class="badge bg-primary"><?= $row_count ?></span>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-bordered table-hover mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID Number</th>
                                    <th>Campus</th>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Amount Paid</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = $rows->fetch_assoc()):?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['idNum']) ?></td>
                                        <td><?= htmlspecialchars($row['campus']) ?></td>
                                        <td><?= htmlspecialchars($row['studFName']) ?></td>
                                        <td><?= htmlspecialchars($row['studLName']) ?></td>
                                        <td><?= htmlspecialchars($row['amountPaid']) ?></td>
                                        <td>
                                            <a href="?page=registration&edit=<?= $row['idNum'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                            <a href="?page=registration&delete=<?= $row['idNum'] ?>" class="btn btn-danger btn-sm">Delete</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header"><?= $edit ? 'Edit Student' : 'Add Student' ?></div>
                    <div class="card-body">
                        <form method="POST">
                            <?php if($edit): ?>
                                <input type="hidden" name="idNum" value="<?= $edit['idNum'] ?>">
                            <?php else: ?>
                                <div class="mb-2">
                                    <label class="form-label">ID Number</label>
                                    <input type="number" name="idNum" class="form-control" required>
                                </div>
                            <?php endif; ?>
                            <div class="mb-2">
                                <label class="form-label"> First Name</label>
                                <input type="text" name="studFName" class="form-control" required value="<?= htmlspecialchars($row['studFName'] ?? '') ?>">
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Last Name</label>
                                <input type="text" name="studLName" class="form-control" required value="<?= htmlspecialchars($row['studLName'] ?? '') ?>">
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Campus</label>
                                <select name="campus" class="form-control" required>
                                    <option value="">Select Option</option>
                                    <?php foreach (['Main', 'Banilad', 'LM', 'Pardo'] as $c): ?>
                                        <option value="<?= $c ?>" <?= ($edit['campus'] ?? '') === $c ? 'selected' : '' ?>><?= $c ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Amount Paid</label>
                                <input type="number" name="amountPaid" class="form-control" required value="<?= htmlspecialchars($edit['amountPaid'] ?? '') ?>">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php elseif($page === 'attendance'): ?>
        <div class="card mx-auto max-width:400px">
            <div class="card-header">
                <div class="card-body">
                    <form method="POST" class="d-flex gap-2 mb-3">
                        <input type="text" name="idNum" class="form-control" placeholder="Enter Student ID#" required>
                        <button type="submit" name="check" class="btn btn-primary">Check</button>
                    </form>
                    <?php if($student): ?>
                        <div class="text-center mt-4">
                            <table class="table table-bordered">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Campus</th>
                                        <th>Attended</th>
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><?= htmlspecialchars($student['idNum']) ?></td>
                                        <td><?= htmlspecialchars($student['studFName'] . ' ' . $student['studLName']) ?></td>
                                        <td><?= htmlspecialchars($student['campus']) ?></td>
                                        <td><?= htmlspecialchars($student['attended']) ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php elseif ($page === 'raffle'): ?>
        <div class="card mx-auto" style="max-width:400px">
            <div class="card-header fw-bold">
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Set filters here:</label><br>
                            
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
</body>
</html>