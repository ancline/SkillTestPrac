<?php
$servername="localhost";
$username="root";
$password="";
$database="clinic";

$connection = new mysqli($servername, $username, $password, $database);
if($connection->connect_error){
    die("Connection Failed: " . $connection->connect_error);
}

$message="";
$active = $_GET['tab'] ?? 'doctors';

// ------------DOCTORS--------------
//Add Doctor
if(isset($_POST['add_doctor'])){
    $id = trim($_POST['docID']);
    $fn = trim($_POST['docFName']);
    $ln = trim($_POST['docLName']);
    $addr = trim($_POST['docAddress']);
    $sp = trim($_POST['docSpecial']);
    $connection->query("INSERT INTO doctor (docID, docFName, docLName, docAddress, docSpecial) VALUE ('$id', '$fn', '$ln', '$addr', '$sp')");
    $message = "Doctor added successfully";
    $active = 'doctors';
}

//Update Doctor
if(isset($_POST['update_doctor'])){
    $id = trim($_POST['docID']);
    $fn = trim($_POST['docFName']);
    $ln = trim($_POST['docLName']);
    $addr = trim($_POST['docAddress']);
    $sp = trim($_POST['docSpecial']);
    $connection->query("UPDATE doctor SET docFName = '$fn', docLName = '$ln', docAddress = '$addr', 'docSpecial = '$sp' WHERE docID = '$id''");
    $message = "Doctor Info Updated Successfully";
    $active = 'doctors';
}

//Delete Doctor
if(isset($_GET['delete_doctor'])){
    $id = trim($_GET['delete_doctor']);
    $connection->query("DELETE FROM doctor WHERE docID = '$id'");
    $active = 'doctors';
}

//fetch
$edit_doctor = null;
if(isset($_GET['edit_doctor'])){
    $r = $connection->query("SELECT * FROM doctor WHERE docID = " . intval($_GET['edit_doctor']));
    $edit_doctor = $r->fetch_assoc();
    $active = 'doctors';
}

$search_doc = $_POST['search_doc'] ?? '';
$doc_sql = "SELECT * FROM doctor";
if($search_doc) $doc_sql .= " WHERE docFName LIKE '%$search_doc%' OR docLName LIKE '%$search_doc%' OR docAddress LIKE '%$search_doc%' OR docSpecial LIKE '%$search_doc%'";
$doctor = $connection->query($doc_sql);
$doc_count = $doctor->num_rows;


//---------------------PATIENTS-------------------
//Add Patients
if(isset($_POST['add_patient'])){
    $id = trim($_POST['patID']);
    $fn = trim($_POST['patFName']);
    $ln = trim($_POST['patLName']);
    $date = trim($_POST['patBDate']);
    $ph = trim($_POST['patTelNo']);
    $connection->query("INSERT INTO patient (patID, patFName, patLName, patBDate, patTelNo) VALUES ('$id', '$fn', '$ln', '$date', '$ph')");
    $message = "Patient Added Successfully";
    $active = 'patients';
}

//Update Patients
if(isset($_POST['update_patient'])){
    $id = trim($_POST['patID']);
    $fn = trim($_POST['patFName']);
    $ln = trim($_POST['patLName']);
    $date = trim($_POST['patBDate']);
    $ph = trim($_POST['patTelNo']);
    $connection->query("UPDATE patient SET patFName = '$fn', patLName = '$ln', patBDate = '$date', patTelNo = '$ph' WHERE patID = '$id'");
    $message = "Patient Updated Successfully";
    $active = 'patients';
}

//Delete Patient
if(isset($_GET['delete_patient'])){
    $id = trim($_GET['delete_patient']);
    $connection->query("DELETE FROM patient WHERE patID = '$id'");
    $message = "Patient Delete from database";
    $active = 'patients';
}

//fetch
$edit_patient = null;
if(isset($_GET['edit_patient'])){
    $r = $connection->query("SELECT * FROM patient WHERE patID = ". intval($_GET['edit_patient']));
    $edit_patient = $r->fetch_assoc();
    $active = 'patients';
}

$search_pat = $_POST['search_pat'];
$pat_sql = "SELECT * FROM patient";
if($search_pat) $pat_sql .= " WHERE patFName LIKE '%$search_pat%' OR patLName LIKE '%$search_pat%' OR patTelNo LIKE '%$search_pat%'";
$patients = $connection->query("$pat_sql");
$pat_count = $patients->num_rows;

// ---------------------CONSULTATIONS--------------------------------
//Add Consultation
if(isset($_POST['add_consultation'])){
    
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clinic Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container my-4">
        <?php if($message): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?= htmlspecialchars($message) ?>
                <button type="button" class="btn btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
    </div>

    <!-- Tab nav -->
     <?php $tabs = ['doctors' => 'Doctors', 'patients' => 'Patients', 'consultations' => 'Consultations'];?>
     <ul class="nav nav-tabs mb-4">
        <?php foreach($tabs as $key => $label): ?>
            <li class="nav-item">
                <a class="nav-link <?= $active === $key ? 'active' : '' ?>" href="?tab=<?= $key ?>"><?= $label ?></a>
            </li>
        <?php endforeach; ?>
     </ul>

        <!-- Doctor tab-->
        <?php if($active === 'doctors'): ?>
            <div class="row g-3">
                <div class="col-md-7">
                    
                </div>
            </div>
        <? endif; ?>
</body>
</html>