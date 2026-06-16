<?php
$servername = "localhost";
$username   = "root";
$password   = "";
$database   = "clinic";

$connection = new mysqli($servername, $username, $password, $database);
if ($connection->connect_error) {
    die("Connection Failed: " . $connection->connect_error);
}

$message = "";
$active  = $_GET['tab'] ?? 'doctors';

// ── DOCTORS ──────────────────────────────────────────────────────────────────

if (isset($_POST['add_doctor'])) {
    $id   = trim($_POST['docID']);
    $fn   = trim($_POST['docFName']);
    $ln   = trim($_POST['docLName']);
    $addr = trim($_POST['docAddress']);
    $sp   = trim($_POST['docSpecial']);
    $connection->query("INSERT INTO doctor (docID,docFName,docLName,docAddress,docSpecial) VALUES ('$id','$fn','$ln','$addr','$sp')");
    $message = "Doctor added successfully.";
}

if (isset($_POST['update_doctor'])) {
    $id   = trim($_POST['docID']);
    $fn   = trim($_POST['docFName']);
    $ln   = trim($_POST['docLName']);
    $addr = trim($_POST['docAddress']);
    $sp   = trim($_POST['docSpecial']);
    $connection->query("UPDATE doctor SET docFName='$fn',docLName='$ln',docAddress='$addr',docSpecial='$sp' WHERE docID='$id'");
    $message = "Doctor updated successfully.";
    $active   = 'doctors';
}

if (isset($_GET['delete_doctor'])) {
    $id = trim($_GET['delete_doctor']);
    $connection->query("DELETE FROM doctor WHERE docID='$id'");
    $message = "Doctor deleted successfully.";
    $active   = 'doctors';
}

$edit_doctor = null;
if (isset($_GET['edit_doctor'])) {
    $r = $connection->query("SELECT * FROM doctor WHERE docID=" . intval($_GET['edit_doctor']));
    $edit_doctor = $r->fetch_assoc();
    $active = 'doctors';
}

$search_doc = $_POST['search_doc'] ?? '';
$doc_sql    = "SELECT * FROM doctor";
if ($search_doc) $doc_sql .= " WHERE docFName LIKE '%$search_doc%' OR docLName LIKE '%$search_doc%' OR docSpecial LIKE '%$search_doc%' OR docID LIKE '%$search_doc%'";
$doctor    = $connection->query($doc_sql);
$doc_count = $doctor->num_rows;

// ── PATIENTS ─────────────────────────────────────────────────────────────────

if (isset($_POST['add_patient'])) {
    $id   = trim($_POST['patID']);
    $fn   = trim($_POST['patFName']);
    $ln   = trim($_POST['patLName']);
    $date = trim($_POST['patBDate']);
    $ph   = trim($_POST['patTelNo']);
    $connection->query("INSERT INTO patient (patID,patFName,patLName,patBDate,patTelNo) VALUES ('$id','$fn','$ln','$date','$ph')");
    $message = "Patient added successfully.";
    $active   = 'patients';
}

if (isset($_POST['update_patient'])) {
    $id   = trim($_POST['patID']);
    $fn   = trim($_POST['patFName']);
    $ln   = trim($_POST['patLName']);
    $date = trim($_POST['patBDate']);
    $ph   = trim($_POST['patTelNo']);
    $connection->query("UPDATE patient SET patFName='$fn',patLName='$ln',patBDate='$date',patTelNo='$ph' WHERE patID='$id'");
    $message = "Patient updated successfully.";
    $active   = 'patients';
}

if (isset($_GET['delete_patient'])) {
    $id = trim($_GET['delete_patient']);
    $connection->query("DELETE FROM patient WHERE patID='$id'");
    $message = "Patient deleted successfully.";
    $active   = 'patients';
}

$edit_patient = null;
if (isset($_GET['edit_patient'])) {
    $r = $connection->query("SELECT * FROM patient WHERE patID=" . intval($_GET['edit_patient']));
    $edit_patient = $r->fetch_assoc();
    $active = 'patients';
}

$search_pat = $_POST['search_pat'] ?? '';
$pat_sql    = "SELECT * FROM patient";
if ($search_pat) $pat_sql .= " WHERE patFName LIKE '%$search_pat%' OR patLName LIKE '%$search_pat%' OR patBDate LIKE '%$search_pat%' OR patTelNo LIKE '%$search_pat%'";
$patients  = $connection->query($pat_sql);
$pat_count = $patients->num_rows;

// ── CONSULTATIONS ─────────────────────────────────────────────────────────────

if (isset($_POST['add_consultation'])) {
    $id    = trim($_POST['consultID']);
    $pId   = trim($_POST['patID']);
    $dId   = trim($_POST['docID']);
    $cdate = trim($_POST['consultDate']);
    $diag  = trim($_POST['diagnosis']);
    $pres  = trim($_POST['prescription']);
    $connection->query("INSERT INTO consultation (consultID,patID,docID,consultDate,diagnosis,prescription) VALUES ('$id','$pId','$dId','$cdate','$diag','$pres')");
    $message = "Consultation added successfully.";
    $active   = 'consultations';
}

if (isset($_POST['update_consultation'])) {
    $id    = trim($_POST['consultID']);
    $pId   = trim($_POST['patID']);
    $dId   = trim($_POST['docID']);
    $cdate = trim($_POST['consultDate']);
    $diag  = trim($_POST['diagnosis']);
    $pres  = trim($_POST['prescription']);
    $connection->query("UPDATE consultation SET patID='$pId',docID='$dId',consultDate='$cdate',diagnosis='$diag',prescription='$pres' WHERE consultID='$id'");
    $message = "Consultation updated successfully.";
    $active   = 'consultations';
}

if (isset($_GET['delete_consultation'])) {
    $id = trim($_GET['delete_consultation']);
    $connection->query("DELETE FROM consultation WHERE consultID='$id'");
    $message = "Consultation deleted successfully.";
    $active   = 'consultations';
}

$edit_consultation = null;
if (isset($_GET['edit_consultation'])) {
    $r = $connection->query("SELECT * FROM consultation WHERE consultID=" . intval($_GET['edit_consultation']));
    $edit_consultation = $r->fetch_assoc();
    $active = 'consultations';
}

$search_con = $_POST['search_con'] ?? '';
$con_sql    = "SELECT * FROM consultation";
if ($search_con) $con_sql .= " WHERE patID LIKE '%$search_con%' OR docID LIKE '%$search_con%' OR consultDate LIKE '%$search_con%' OR diagnosis LIKE '%$search_con%' OR prescription LIKE '%$search_con%'";
$consultations = $connection->query($con_sql);
$con_count     = $consultations->num_rows;

// Dropdowns for consultation form
$all_doctors  = $connection->query("SELECT docID, CONCAT(docFName,' ',docLName) AS fullName FROM doctor");
$all_patients = $connection->query("SELECT patID, CONCAT(patFName,' ',patLName) AS fullName FROM patient");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clinic Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-primary">
    <div class="container">
        <span class="navbar-brand fw-bold">Clinic Management System</span>
    </div>
</nav>

<div class="container my-4">

    <?php if ($message): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= htmlspecialchars($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Tab nav (loop-driven) -->
    <?php $tabs = ['doctors' => 'Doctors', 'patients' => 'Patients', 'consultations' => 'Consultations']; ?>
    <ul class="nav nav-tabs mb-4">
        <?php foreach ($tabs as $key => $label): ?>
            <li class="nav-item">
                <a class="nav-link <?= $active === $key ? 'active' : '' ?>" href="?tab=<?= $key ?>"><?= $label ?></a>
            </li>
        <?php endforeach; ?>
    </ul>

    <!-- ── DOCTORS TAB ────────────────────────────────────────────────────── -->
    <?php if ($active === 'doctors'): ?>
    <div class="row g-3">
        <!-- Table -->
        <div class="col-md-7">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <strong>Doctors <span class="badge bg-primary"><?= $doc_count ?></span></strong>
                    <form method="POST" class="d-flex gap-2">
                        <input type="text" name="search_doc" class="form-control form-control-sm" placeholder="Search..." value="<?= htmlspecialchars($search_doc) ?>">
                        <button class="btn btn-sm btn-outline-primary">Search</button>
                        <?php if ($search_doc): ?><a href="?tab=doctors" class="btn btn-sm btn-outline-secondary">Clear</a><?php endif; ?>
                    </form>
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered table-hover mb-0">
                        <thead class="table-dark">
                            <tr><th>ID</th><th>First Name</th><th>Last Name</th><th>Address</th><th>Specialization</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                        <?php while ($row = $doctor->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['docID']) ?></td>
                                <td><?= htmlspecialchars($row['docFName']) ?></td>
                                <td><?= htmlspecialchars($row['docLName']) ?></td>
                                <td><?= htmlspecialchars($row['docAddress']) ?></td>
                                <td><?= htmlspecialchars($row['docSpecial']) ?></td>
                                <td>
                                    <a href="?tab=doctors&edit_doctor=<?= $row['docID'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <a href="?tab=doctors&delete_doctor=<?= $row['docID'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this doctor?')">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="col-md-5">
            <div class="card">
                <div class="card-header"><?= $edit_doctor ? 'Edit Doctor' : 'Add Doctor' ?></div>
                <div class="card-body">
                    <form method="POST">
                        <?php if ($edit_doctor): ?>
                            <input type="hidden" name="docID" value="<?= $edit_doctor['docID'] ?>">
                        <?php else: ?>
                            <div class="mb-2">
                                <label class="form-label">Doctor ID</label>
                                <input type="number" name="docID" class="form-control" required>
                            </div>
                        <?php endif; ?>
                        <div class="mb-2">
                            <label class="form-label">First Name</label>
                            <input type="text" name="docFName" class="form-control" required value="<?= htmlspecialchars($edit_doctor['docFName'] ?? '') ?>">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Last Name</label>
                            <input type="text" name="docLName" class="form-control" required value="<?= htmlspecialchars($edit_doctor['docLName'] ?? '') ?>">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Address</label>
                            <input type="text" name="docAddress" class="form-control" required value="<?= htmlspecialchars($edit_doctor['docAddress'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Specialization</label>
                            <input type="text" name="docSpecial" class="form-control" required value="<?= htmlspecialchars($edit_doctor['docSpecial'] ?? '') ?>">
                        </div>
                        <?php if ($edit_doctor): ?>
                            <button type="submit" name="update_doctor" class="btn btn-warning w-100">Update</button>
                            <a href="?tab=doctors" class="btn btn-secondary w-100 mt-2">Cancel</a>
                        <?php else: ?>
                            <button type="submit" name="add_doctor" class="btn btn-primary w-100">Add Doctor</button>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- ── PATIENTS TAB ───────────────────────────────────────────────────── -->
    <?php if ($active === 'patients'): ?>
    <div class="row g-3">
        <!-- Table -->
        <div class="col-md-7">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <strong>Patients <span class="badge bg-primary"><?= $pat_count ?></span></strong>
                    <form method="POST" class="d-flex gap-2">
                        <input type="text" name="search_pat" class="form-control form-control-sm" placeholder="Search..." value="<?= htmlspecialchars($search_pat) ?>">
                        <button class="btn btn-sm btn-outline-primary">Search</button>
                        <?php if ($search_pat): ?><a href="?tab=patients" class="btn btn-sm btn-outline-secondary">Clear</a><?php endif; ?>
                    </form>
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered table-hover mb-0">
                        <thead class="table-dark">
                            <tr><th>ID</th><th>First Name</th><th>Last Name</th><th>Birth Date</th><th>Phone No.</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                        <?php while ($row = $patients->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['patID']) ?></td>
                                <td><?= htmlspecialchars($row['patFName']) ?></td>
                                <td><?= htmlspecialchars($row['patLName']) ?></td>
                                <td><?= htmlspecialchars($row['patBDate']) ?></td>
                                <td><?= htmlspecialchars($row['patTelNo']) ?></td>
                                <td>
                                    <a href="?tab=patients&edit_patient=<?= $row['patID'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <a href="?tab=patients&delete_patient=<?= $row['patID'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this patient?')">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="col-md-5">
            <div class="card">
                <div class="card-header"><?= $edit_patient ? 'Edit Patient' : 'Add Patient' ?></div>
                <div class="card-body">
                    <form method="POST">
                        <?php if ($edit_patient): ?>
                            <input type="hidden" name="patID" value="<?= $edit_patient['patID'] ?>">
                        <?php else: ?>
                            <div class="mb-2">
                                <label class="form-label">Patient ID</label>
                                <input type="number" name="patID" class="form-control" required>
                            </div>
                        <?php endif; ?>
                        <div class="mb-2">
                            <label class="form-label">First Name</label>
                            <input type="text" name="patFName" class="form-control" required value="<?= htmlspecialchars($edit_patient['patFName'] ?? '') ?>">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Last Name</label>
                            <input type="text" name="patLName" class="form-control" required value="<?= htmlspecialchars($edit_patient['patLName'] ?? '') ?>">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Birth Date</label>
                            <input type="date" name="patBDate" class="form-control" value="<?= htmlspecialchars($edit_patient['patBDate'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone No.</label>
                            <input type="text" name="patTelNo" class="form-control" value="<?= htmlspecialchars($edit_patient['patTelNo'] ?? '') ?>">
                        </div>
                        <?php if ($edit_patient): ?>
                            <button type="submit" name="update_patient" class="btn btn-warning w-100">Update</button>
                            <a href="?tab=patients" class="btn btn-secondary w-100 mt-2">Cancel</a>
                        <?php else: ?>
                            <button type="submit" name="add_patient" class="btn btn-primary w-100">Add Patient</button>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- ── CONSULTATIONS TAB ──────────────────────────────────────────────── -->
    <?php if ($active === 'consultations'): ?>
    <div class="row g-3">
        <!-- Table -->
        <div class="col-md-7">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <strong>Consultations <span class="badge bg-primary"><?= $con_count ?></span></strong>
                    <form method="POST" class="d-flex gap-2">
                        <input type="text" name="search_con" class="form-control form-control-sm" placeholder="Search..." value="<?= htmlspecialchars($search_con) ?>">
                        <button class="btn btn-sm btn-outline-primary">Search</button>
                        <?php if ($search_con): ?><a href="?tab=consultations" class="btn btn-sm btn-outline-secondary">Clear</a><?php endif; ?>
                    </form>
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered table-hover mb-0">
                        <thead class="table-dark">
                            <tr><th>ID</th><th>Patient ID</th><th>Doctor ID</th><th>Date</th><th>Diagnosis</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                        <?php while ($row = $consultations->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['consultID']) ?></td>
                                <td><?= htmlspecialchars($row['patID']) ?></td>
                                <td><?= htmlspecialchars($row['docID']) ?></td>
                                <td><?= htmlspecialchars($row['consultDate']) ?></td>
                                <td><?= htmlspecialchars($row['diagnosis']) ?></td>
                                <td>
                                    <a href="?tab=consultations&edit_consultation=<?= $row['consultID'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <a href="?tab=consultations&delete_consultation=<?= $row['consultID'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this consultation?')">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="col-md-5">
            <div class="card">
                <div class="card-header"><?= $edit_consultation ? 'Edit Consultation' : 'Add Consultation' ?></div>
                <div class="card-body">
                    <form method="POST">
                        <?php if ($edit_consultation): ?>
                            <input type="hidden" name="consultID" value="<?= $edit_consultation['consultID'] ?>">
                        <?php else: ?>
                            <div class="mb-2">
                                <label class="form-label">Consultation ID</label>
                                <input type="number" name="consultID" class="form-control" required>
                            </div>
                        <?php endif; ?>
                        <div class="mb-2">
                            <label class="form-label">Patient</label>
                            <select name="patID" class="form-select" required>
                                <option value="">-- Select Patient --</option>
                                <?php while ($p = $all_patients->fetch_assoc()): ?>
                                    <option value="<?= $p['patID'] ?>" <?= (isset($edit_consultation['patID']) && $edit_consultation['patID'] == $p['patID']) ? 'selected' : '' ?>>
                                        <?= $p['patID'] ?> - <?= htmlspecialchars($p['fullName']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Doctor</label>
                            <select name="docID" class="form-select" required>
                                <option value="">-- Select Doctor --</option>
                                <?php while ($d = $all_doctors->fetch_assoc()): ?>
                                    <option value="<?= $d['docID'] ?>" <?= (isset($edit_consultation['docID']) && $edit_consultation['docID'] == $d['docID']) ? 'selected' : '' ?>>
                                        <?= $d['docID'] ?> - <?= htmlspecialchars($d['fullName']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Consultation Date</label>
                            <input type="datetime-local" name="consultDate" class="form-control"
                                value="<?= isset($edit_consultation['consultDate']) ? date('Y-m-d\TH:i', strtotime($edit_consultation['consultDate'])) : '' ?>">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Diagnosis</label>
                            <textarea name="diagnosis" class="form-control" rows="2"><?= htmlspecialchars($edit_consultation['diagnosis'] ?? '') ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Prescription</label>
                            <textarea name="prescription" class="form-control" rows="2"><?= htmlspecialchars($edit_consultation['prescription'] ?? '') ?></textarea>
                        </div>
                        <?php if ($edit_consultation): ?>
                            <button type="submit" name="update_consultation" class="btn btn-warning w-100">Update</button>
                            <a href="?tab=consultations" class="btn btn-secondary w-100 mt-2">Cancel</a>
                        <?php else: ?>
                            <button type="submit" name="add_consultation" class="btn btn-primary w-100">Add Consultation</button>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

</div><!-- /.container -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>