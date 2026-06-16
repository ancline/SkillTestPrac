<?php
$servername = "localhost";
$username = "root";
$pass = "";
$db = "event";

$connection = new mysqli($servername, $username, $pass, $db);

$msg="";
$page = $_GET['page'] ?? 'menu';

if($connection->connect_error){
    die("Connection failed: " .$connection->connect_error );
}

// -----------Add Event------------------
if(isset($_POST['add_event'])){
    $code = trim($_POST['evCode']);
    $name = trim($_POST['evName']);
    $date = trim($_POST['evDate']);
    $ven = trim($_POST['evVenue']);
    $fee = trim($_POST['evRFee']);
    $connection->query("INSERT INTO events (evCode, evName, evDate, evVenue, evRFee) VALUES ('$code', '$name', '$date', '$ven', '$fee')");
    $msg = "Event added successfully";
    $page = 'events';
}

// -----------------Delete Event--------------------
if(isset($_GET['delete_event'])){
    $code = $_GET['delete_event'];
    $connection->query("DELETE FROM events WHERE evCode = '$code'");
    $msg = "Event deleted";
    $page = 'events';
}

//--------------------Update Event--------------------
if(isset($_POST['update_event'])){
    $code = trim($_POST['evCode']);
    $name = trim($_POST['evName']);
    $date = trim($_POST['evDate']);
    $ven = trim($_POST['evVenue']);
    $fee = trim($_POST['evRFee']);
    $connection->query("UPDATE events SET evName = '$name', evDate = '$date', evVenue = '$ven', evRFee = '$fee' WHERE evCode = '$code'");
    $msg = "Event updated successfully";
    $page = 'events';
}

$edit_event = null;
if(isset($_GET['edit_event'])){
    $r = $connection->query("SELECT * FROM events WHERE evCode = " . intval($_GET['edit_event']));
    $edit_event = $r->fetch_assoc();
    $page = 'events';
}

$search_event= $_POST['search_event'] ?? '';
$event_sql = "SELECT * FROM events";
if($search_event) $event_sql .= " WHERE evCode LIKE '%$search_event%' OR evName LIKE '%$search_event%' OR evDate LIKE '%$search_event%' OR evVenue LIKE '%$search_event%'";
$event = $connection->query($event_sql);
$event_count = $event->num_rows;

//----------Add Participants------------------
if(isset($_POST['add_part'])){
    $idNum = trim($_POST['partID']);
    $fn = trim($_POST['partFName']);
    $ln = trim($_POST['partLName']);
    $dRate = trim($_POST['partDate']);
    $connection->query("INSERT INTO participants (partID, partFName, partLName, partDRate) VALUES ('$idNum', '$fn', '$ln', '$dRate')");
    $msg = "Participant Added Successfully";
    $page = 'participants';
}


// ---------------Delete Participants----------------
if(isset($_GET['delete_participant'])){
    $idNum = $_GET['delete_event'];
    $connection->query("DELETE FROM participants WHERE partID = '$idNum'");
    $msg = "Participant Deleted";
    $page = 'participants';
}

//-----------------Update Participants------------
if(isset($_POST['update_participant'])){
    $idNum = trim($_POST['partID']);
    $code = trim($_POST['evCode']);
    $fn = trim($_POST['partFName']);
    $ln = trim($_POST['partLName']);
    $dRate = trim($_POST['partDRate']);
    $connection->query("UPDATE participants SET evCode = '$code', partFName = '$fn', partLName = '$ln', partDRate = '$dRate' WHERE partID = '$idNum'");
    $msg = 'Paticipant updated Successfully';
    $page = 'participants';
}

$edit_part = null;
if(isset($_GET['edit_part'])){
    $r = $connection->query("SELECT * FROM participants WHERE partID = " . intval($_GET['edit_part']));
    $edit_part = $r->fetch_assoc();
    $page = 'participants';
}

$search_part = $_POST['search_part'] ?? '';
$part_sql = "SELECT * FROM participants";
if($search_part) $part_sql .= " WHERE partID LIKE '%$search_part%' OR evCode LIKE '%$search_part%' OR partFName LIKE '%$search_part%' OR partLName LIKE '%$search_part%' OR partDRate LIKE '%$search_part%'";
$part = $connection->query($part_sql);
$part_count = $event->num_rows;

// ------------------Registration -----------------
if(isset($_POST['add_reg'])){
    $regCode = trim($_POST['regCode']);
    $id = trim($_POST['partID']);
    $date = trim($_POST['regDate']);
    $paid = trim($_POST['regFPaid']);
    $mode = trim($_POST['regPMode']);
    $connection->query("INSERT INTO registration (regcode, partID, regDate, regFPaid, regPMode) VALUES ('$regCode', '$id', '$date', '$paid', '$mode')");
    $msg = "Registration Added successfully";
    $page = 'registration';
}

// ---------------------Delete Registration-----------
if(isset($_POST['update_reg'])){
    $regCode = trim($_POST['regCode']);
    $id = trim($_POST['partID']);
    $date = trim($_POST['regDate']);
    $paid = trim($_POST['regFPaid']);
    $mode = trim($_POST['regPMode']);
    $connection->query("UPDATE registration SET partID = '$id', regDate = '$date', regFPaid = '$paid', regPMode = '$mode' WHERE regCode = '$regCode'");
    $msg = "Registration Deleted";
    $page = 'registration';
}
?>