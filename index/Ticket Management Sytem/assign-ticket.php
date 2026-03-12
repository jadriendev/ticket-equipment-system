<?php
session_start();
require_once "config.php";

if(!isset($_SESSION['username']) || $_SESSION['usertype'] != "ADMINISTRATOR"){
header("location: ../../login.php");
}

$username = $_SESSION['username'];

if(isset($_GET['ticket_number'])){
$ticket=$_GET['ticket_number'];
}

if(isset($_POST['save'])){

$ticket=$_POST['ticket'];
$tech=$_POST['tech'];

mysqli_query($link,"UPDATE tbltickets 
SET status='ON-GOING',
assignedto='$tech',
dateassigned=NOW()
WHERE ticket_number='$ticket'");

mysqli_query($link,"INSERT INTO tbllogs(username,action,logdate)
VALUES('$username','Assigned ticket $ticket to $tech',NOW())");

header("location: ticket_admin.php");
}

?>

<!DOCTYPE html>
<html>

<head>

<title>Assign Ticket</title>

<script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen">

<div class="bg-white p-8 rounded shadow w-96">

<h1 class="text-xl font-bold mb-6">
Assign Ticket
</h1>

<form method="POST">

<input type="hidden" name="ticket" value="<?php echo $ticket; ?>">

<label class="block mb-2">
Select Technical
</label>

<select name="tech" class="border p-2 w-full mb-6">

<?php

$tech=mysqli_query($link,"SELECT username FROM tblaccounts WHERE usertype='TECHNICAL'");

while($t=mysqli_fetch_assoc($tech)){

echo "<option>".$t['username']."</option>";

}

?>

</select>

<button name="save" class="bg-yellow-500 text-white px-4 py-2 rounded w-full mb-3">
Assign Ticket
</button>

<a href="ticket_admin.php" class="block text-center border py-2 rounded">
Cancel
</a>

</form>

</div>

</body>

</html>