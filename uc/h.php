<?php
$servername = "localhost";
$username   = "root";
$password   = "";
$database   = "Registration";

$connection = new mysqli($servername, $username, $password, $database);
if ($connection->connect_error) {
    die("Connection Failed: " . $connection->connect_error);
}

$message      = "";
$message_type = "success";
$page         = $_GET['page'] ?? 'menu';

if (isset($_POST['add'])) {
    $id     = trim($_POST['idNum']);
    $campus = trim($_POST['campus']);
    $fn     = trim($_POST['studFName']);
    $ln     = trim($_POST['studLName']);
    $amt    = trim($_POST['amountPaid']);
    $check  = $connection->query("SELECT idNum FROM Registration WHERE idNum='$id'");
    if ($check->num_rows > 0) {
        $message = "ID $id already exists."; $message_type = "danger";
    } else {
        $connection->query("INSERT INTO Registration (idNum,campus,studFName,studLName,amountPaid,attended) VALUES ('$id','$campus','$fn','$ln','$amt','No')");
        $message = "Student registered successfully.";
    }
}

if (isset($_POST['update'])) {
    $id     = trim($_POST['idNum']);
    $campus = trim($_POST['campus']);
    $fn     = trim($_POST['studFName']);
    $ln     = trim($_POST['studLName']);
    $amt    = trim($_POST['amountPaid']);
    $connection->query("UPDATE Registration SET campus='$campus',studFName='$fn',studLName='$ln',amountPaid='$amt' WHERE idNum='$id'");
    $message = "Student updated successfully.";
}

if (isset($_GET['delete'])) {
    $id = trim($_GET['delete']);
    $connection->query("DELETE FROM Registration WHERE idNum='$id'");
    $message = "Student deleted successfully.";
}

$edit = null;
if (isset($_GET['edit'])) {
    $r    = $connection->query("SELECT * FROM Registration WHERE idNum='" . trim($_GET['edit']) . "'");
    $edit = $r->fetch_assoc();
}

$rows      = $connection->query("SELECT * FROM Registration");
$row_count = $rows->num_rows;

$student = null;
if (isset($_POST['check'])) {
    $id     = trim($_POST['idNum']);
    $result = $connection->query("SELECT * FROM Registration WHERE idNum='$id'");
    if ($result->num_rows === 0) {
        $message = "ID# $id is NOT YET REGISTERED."; $message_type = "danger";
    } else {
        $student = $result->fetch_assoc();
        if ($student['attended'] === 'Yes') {
            $message = "Student's Attendance RECORD ALREADY EXISTS."; $message_type = "warning";
        } else {
            $connection->query("UPDATE Registration SET attended='Yes' WHERE idNum='$id'");
            $message = "Attendance SUCCESSFULLY RECORDED."; $message_type = "success";
            $student['attended'] = 'Yes';
        }
    }
}

$winner            = null;
$selected_campuses = $_POST['campuses'] ?? ['Main', 'Banilad', 'LM', 'Pardo'];
if (isset($_POST['reveal'])) {
    $list   = implode("','", $selected_campuses);
    $result = $connection->query("SELECT * FROM Registration WHERE campus IN ('$list') ORDER BY RAND() LIMIT 1");
    $winner = $result->fetch_assoc();
}

$selected_report = $_POST['campuses_report'] ?? [];
$report_rows     = null;
if (isset($_POST['generate']) && $selected_report) {
    $list        = implode("','", $selected_report);
    $report_rows = $connection->query("SELECT * FROM Registration WHERE campus IN ('$list') ORDER BY campus");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UC ICT Congress Registration System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-primary px-3">
    <span class="navbar-brand fw-bold">UC ICT Congress Registration System</span>
    <?php if ($page !== 'menu'): ?>
        <a href="?page=menu" class="btn btn-outline-light btn-sm">Back to Menu</a>
    <?php endif; ?>
</nav>

<div class="container my-4">

    <?php if ($message): ?>
        <div class="alert alert-<?= $message_type ?> alert-dismissible fade show">
            <?= htmlspecialchars($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($page === 'menu'): ?>
    <div class="card mx-auto" style="max-width:340px">
        <div class="card-header fw-bold text-center">Choose your Transaction</div>
        <div class="card-body d-grid gap-2">
            <?php foreach ([
                'registration' => 'Registration',
                'attendance'   => 'Attendance',
                'raffle'       => 'Raffle',
                'by_campus'    => 'Report (By Campus)',
                'summary'      => 'Report (Summary)',
            ] as $p => $label): ?>
                <a href="?page=<?= $p ?>" class="btn btn-outline-primary"><?= $label ?></a>
            <?php endforeach; ?>
        </div>
    </div>

    <?php elseif ($page === 'registration'): ?>
    <div class="row g-3">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header fw-bold">
                    Registered Students <span class="badge bg-primary"><?= $row_count ?></span>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0">
                        <thead class="table-dark">
                            <tr><th>ID#</th><th>Name</th><th>Campus</th><th>Amount</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                        <?php while ($row = $rows->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['idNum']) ?></td>
                                <td><?= htmlspecialchars($row['studFName'] . ' ' . $row['studLName']) ?></td>
                                <td><?= htmlspecialchars($row['campus']) ?></td>
                                <td><?= htmlspecialchars($row['amountPaid']) ?></td>
                                <td>
                                    <a href="?page=registration&edit=<?= $row['idNum'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <a href="?page=registration&delete=<?= $row['idNum'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this student?')">Delete</a>
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
                <div class="card-header fw-bold"><?= $edit ? 'Edit Student' : 'Add Student' ?></div>
                <div class="card-body">
                    <form method="POST">
                        <?php if ($edit): ?>
                            <input type="hidden" name="idNum" value="<?= $edit['idNum'] ?>">
                        <?php else: ?>
                            <div class="mb-2">
                                <label class="form-label">ID Number</label>
                                <input type="text" name="idNum" class="form-control form-control-sm" required>
                            </div>
                        <?php endif; ?>
                        <div class="mb-2">
                            <label class="form-label">First Name</label>
                            <input type="text" name="studFName" class="form-control form-control-sm" required value="<?= htmlspecialchars($edit['studFName'] ?? '') ?>">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Last Name</label>
                            <input type="text" name="studLName" class="form-control form-control-sm" required value="<?= htmlspecialchars($edit['studLName'] ?? '') ?>">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Campus</label>
                            <select name="campus" class="form-select form-select-sm" required>
                                <option value="">-- Select --</option>
                                <?php foreach (['Main', 'Banilad', 'LM', 'Pardo'] as $c): ?>
                                    <option value="<?= $c ?>" <?= ($edit['campus'] ?? '') === $c ? 'selected' : '' ?>><?= $c ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Amount Paid</label>
                            <input type="number" name="amountPaid" class="form-control form-control-sm" required value="<?= htmlspecialchars($edit['amountPaid'] ?? '') ?>">
                        </div>
                        <?php if ($edit): ?>
                            <button type="submit" name="update" class="btn btn-warning btn-sm w-100">Update</button>
                            <a href="?page=registration" class="btn btn-secondary btn-sm w-100 mt-2">Cancel</a>
                        <?php else: ?>
                            <button type="submit" name="add" class="btn btn-primary btn-sm w-100">Register</button>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php elseif ($page === 'attendance'): ?>
    <div class="card mx-auto" style="max-width:480px">
        <div class="card-header fw-bold">Attendance</div>
        <div class="card-body">
            <form method="POST" class="d-flex gap-2 mb-3">
                <input type="text" name="idNum" class="form-control form-control-sm" placeholder="Enter Student ID#" required>
                <button type="submit" name="check" class="btn btn-primary btn-sm">Check</button>
            </form>
            <?php if ($student): ?>
                <table class="table table-bordered table-sm">
                    <tr><th>ID#</th><td><?= htmlspecialchars($student['idNum']) ?></td></tr>
                    <tr><th>Name</th><td><?= htmlspecialchars($student['studFName'] . ' ' . $student['studLName']) ?></td></tr>
                    <tr><th>Campus</th><td><?= htmlspecialchars($student['campus']) ?></td></tr>
                    <tr><th>Attended</th><td><?= htmlspecialchars($student['attended']) ?></td></tr>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <?php elseif ($page === 'raffle'): ?>
    <div class="card mx-auto" style="max-width:480px">
        <div class="card-header fw-bold">Raffle</div>
        <div class="card-body">
            <form method="POST">
                <label class="form-label">Set filters here:</label><br>
                <?php foreach (['Main', 'Banilad', 'LM', 'Pardo'] as $c): ?>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="campuses[]" value="<?= $c ?>" <?= in_array($c, $selected_campuses) ? 'checked' : '' ?>>
                        <label class="form-check-label"><?= $c ?></label>
                    </div>
                <?php endforeach; ?>
                <div class="mt-3">
                    <button type="submit" name="reveal" class="btn btn-primary w-100">Reveal the Lucky Winner!</button>
                </div>
            </form>
            <?php if ($winner): ?>
                <div class="text-center mt-3">
                    <table class="table table-bordered table-sm">
                        <thead class="table-dark"><tr><th>ID#</th><th>Name</th><th>Campus</th></tr></thead>
                        <tbody>
                            <tr>
                                <td><?= htmlspecialchars($winner['idNum']) ?></td>
                                <td><?= htmlspecialchars($winner['studFName'] . ' ' . $winner['studLName']) ?></td>
                                <td><?= htmlspecialchars($winner['campus']) ?></td>
                            </tr>
                        </tbody>
                    </table>
                    <h5 class="text-success fw-bold fst-italic">CONGRATULATIONS!!!</h5>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php elseif ($page === 'by_campus'): ?>
    <div class="card">
        <div class="card-header fw-bold">Report (By Campus)</div>
        <div class="card-body">
            <form method="POST" class="mb-3">
                <label class="form-label">Set filters here:</label><br>
                <?php foreach (['Main', 'Banilad', 'LM', 'Pardo'] as $c): ?>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="campuses_report[]" value="<?= $c ?>" <?= in_array($c, $selected_report) ? 'checked' : '' ?>>
                        <label class="form-check-label"><?= $c ?></label>
                    </div>
                <?php endforeach; ?>
                <div class="mt-2">
                    <button type="submit" name="generate" class="btn btn-primary btn-sm">Generate Report</button>
                </div>
            </form>
            <?php if ($report_rows):
                $total = 0; $attendees = 0; $count = 0;
            ?>
                <table class="table table-bordered table-sm">
                    <thead class="table-dark">
                        <tr><th>ID#</th><th>Name</th><th>Campus</th><th>Amount</th><th>Attended</th></tr>
                    </thead>
                    <tbody>
                    <?php while ($row = $report_rows->fetch_assoc()):
                        $total += $row['amountPaid'];
                        if ($row['attended'] === 'Yes') $attendees++;
                        $count++;
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($row['idNum']) ?></td>
                            <td><?= htmlspecialchars($row['studFName'] . ' ' . $row['studLName']) ?></td>
                            <td><?= htmlspecialchars($row['campus']) ?></td>
                            <td><?= htmlspecialchars($row['amountPaid']) ?></td>
                            <td><?= htmlspecialchars($row['attended']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
                <p class="mb-1"># of Registrants: <strong><?= $count ?></strong> | Total Collection: <strong><?= number_format($total, 2) ?></strong></p>
                <p># of Attendees: <strong><?= $attendees ?></strong> | Date Generated: <strong><?= date('m/d/Y') ?></strong></p>
            <?php endif; ?>
        </div>
    </div>

    <?php elseif ($page === 'summary'):
        $campuses = ['Main', 'Banilad', 'LM', 'Pardo'];
        $totals   = ['registered' => 0, 'attended' => 0, 'collection' => 0];
    ?>
    <div class="card">
        <div class="card-header fw-bold text-center">Summary Report (All Campuses)</div>
        <div class="card-body">
            <table class="table table-bordered text-center">
                <thead class="table-dark">
                    <tr><th>Campus</th><th>Registered</th><th>Attended</th><th>Total Collection</th></tr>
                </thead>
                <tbody>
                <?php foreach ($campuses as $c):
                    $r = $connection->query("SELECT COUNT(*) as cnt, COALESCE(SUM(amountPaid),0) as total, SUM(attended='Yes') as att FROM Registration WHERE campus='$c'")->fetch_assoc();
                    $totals['registered'] += $r['cnt'];
                    $totals['attended']   += $r['att'];
                    $totals['collection'] += $r['total'];
                ?>
                    <tr>
                        <td><?= $c ?></td>
                        <td><?= $r['cnt'] ?></td>
                        <td><?= $r['att'] ?></td>
                        <td><?= number_format($r['total'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
                <tfoot class="table-secondary fw-bold">
                    <tr>
                        <td>TOTALS</td>
                        <td><?= $totals['registered'] ?></td>
                        <td><?= $totals['attended'] ?></td>
                        <td><?= number_format($totals['collection'], 2) ?></td>
                    </tr>
                </tfoot>
            </table>
            <p class="text-end text-muted">Date Generated: <?= date('m/d/Y') ?></p>
        </div>
    </div>

    <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>