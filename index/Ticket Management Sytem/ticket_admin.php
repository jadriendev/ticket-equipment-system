<?php
session_start();
require_once "config.php";

if(!isset($_SESSION['username']) || $_SESSION['usertype'] != "ADMINISTRATOR"){
    header("location: ../../login.php");
}

$username = $_SESSION['username'];
$notify = "";

// Handle Assign Save
if(isset($_POST['assign_save'])){
    $ticket = $_POST['ticket'];
    $tech = $_POST['tech'];

    mysqli_query($link,"UPDATE tbltickets 
        SET status='ON-GOING', assignedto='$tech', dateassigned=NOW() 
        WHERE ticket_number='$ticket'");

    mysqli_query($link,"INSERT INTO tbllogs(datelog,timelog,action,module,performedby,performedto)
        VALUES(
        DATE_FORMAT(NOW(),'%m/%e/%Y'),
        DATE_FORMAT(NOW(),'%h:%i %p'),
        'Assigned ticket $ticket to $tech',
        'Ticket Management',
        '$username',
        '$tech'
        )");

    $notify = "Ticket $ticket assigned to $tech successfully!";
}

// Handle Approve
if(isset($_POST['approve_confirm'])){
    $ticket = $_POST['ticket'];

    mysqli_query($link,"UPDATE tbltickets 
        SET status='Complete', approvedby='$username', dateapproved=NOW() 
        WHERE ticket_number='$ticket'");

    mysqli_query($link,"INSERT INTO tbllogs(datelog,timelog,action,module,performedby,performedto)
        VALUES(
        DATE_FORMAT(NOW(),'%m/%e/%Y'),
        DATE_FORMAT(NOW(),'%h:%i %p'),
        'Approved ticket $ticket',
        'Ticket Management',
        '$username',
        '$username'
        )");

    $notify = "Ticket $ticket approved successfully!";
}

// Handle Delete
if(isset($_POST['delete_confirm'])){
    $ticket = $_POST['ticket'];

    mysqli_query($link,"DELETE FROM tbltickets WHERE ticket_number='$ticket'");

    mysqli_query($link,"INSERT INTO tbllogs(datelog,timelog,action,module,performedby,performedto)
        VALUES(
        DATE_FORMAT(NOW(),'%m/%e/%Y'),
        DATE_FORMAT(NOW(),'%h:%i %p'),
        'Deleted ticket $ticket',
        'Ticket Management',
        '$username',
        '$username'
        )");

    $notify = "Ticket $ticket deleted successfully!";
}

// Search
$search="";
if(isset($_GET['search'])){
    $search=$_GET['search'];
    $result=mysqli_query($link,"SELECT * FROM tbltickets 
        WHERE ticket_number LIKE '%$search%' 
        OR problem LIKE '%$search%' 
        OR status LIKE '%$search%' 
        ORDER BY datecreated DESC");
}else{
    $result=mysqli_query($link,"SELECT * FROM tbltickets ORDER BY datecreated DESC");
}

$pending = mysqli_fetch_assoc(mysqli_query($link,"SELECT COUNT(*) AS cnt FROM tbltickets WHERE status='PENDING'"))['cnt'];
$ongoing = mysqli_fetch_assoc(mysqli_query($link,"SELECT COUNT(*) AS cnt FROM tbltickets WHERE status='ON-GOING'"))['cnt'];
$forapproval = mysqli_fetch_assoc(mysqli_query($link,"SELECT COUNT(*) AS cnt FROM tbltickets WHERE status='FOR APPROVAL'"))['cnt'];
$complete = mysqli_fetch_assoc(mysqli_query($link,"SELECT COUNT(*) AS cnt FROM tbltickets WHERE status='Complete'"))['cnt'];
?>

<!DOCTYPE html>
<html>
<head>
<link rel="shortcut icon" href="https://www.arellano.edu.ph/sites/default/files/inline-images/new-au-logo.png">
<title>Admin Ticket Management</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="flex bg-gray-100 min-h-screen">

<div class="w-64 bg-white shadow-lg flex flex-col justify-between">
<div>
<div class="p-6 text-center font-bold text-xl border-b">TMS - Admin</div>

<nav class="flex flex-col mt-6 gap-2">
<a href="#" class="py-2 px-4 hover:bg-gray-200">Ticket Management</a>
</nav>

</div>

<div class="mb-6">
<hr class="my-4">
<a href="../adminIndex.php" class="py-2 px-4 hover:bg-gray-200 rounded text-red-600 font-semibold">Back</a>
</div>
</div>

<div class="flex-1 p-6">

<h1 class="text-2xl font-bold mb-6">Ticket Management (Administrator)</h1>

<div class="grid grid-cols-4 gap-4 mb-6">

<div class="bg-yellow-400 p-4 rounded text-white font-bold">
Pending: <?php echo $pending; ?>
</div>

<div class="bg-blue-400 p-4 rounded text-white font-bold">
On-Going: <?php echo $ongoing; ?>
</div>

<div class="bg-orange-400 p-4 rounded text-white font-bold">
For Approval: <?php echo $forapproval; ?>
</div>

<div class="bg-green-500 p-4 rounded text-white font-bold">
Complete: <?php echo $complete; ?>
</div>

</div>

<form method="GET" class="mb-6 flex gap-2">
<input type="text" name="search" placeholder="Search ticket number, problem or status" class="border p-2 w-80">
<button class="bg-blue-600 text-white px-4 py-2 rounded">Search</button>
</form>

<table class="w-full bg-white shadow rounded overflow-hidden">

<tr class="bg-gray-200 text-lg">
<th class="p-4">Ticket</th>
<th class="p-4">Problem</th>
<th class="p-4">Date</th>
<th class="p-4">Time</th>
<th class="p-4">Status</th>
<th class="p-4">Action</th>
</tr>

<?php while($row=mysqli_fetch_assoc($result)){ ?>

<tr class="text-center border-b hover:bg-gray-50">

<td class="p-4 font-semibold"><?php echo $row['ticket_number']; ?></td>

<td class="p-4"><?php echo $row['problem']; ?></td>

<td class="p-4">
<?php echo date("m/d/Y",strtotime($row['datecreated'])); ?>
</td>

<td class="p-4">
<?php echo date("h:i A",strtotime($row['datecreated'])); ?>
</td>

<td class="p-4 font-bold">
<?php echo $row['status']; ?>
</td>

<td class="p-4 space-x-2">

<button onclick="openDetails('<?php echo $row['ticket_number']; ?>','<?php echo $row['problem']; ?>','<?php echo $row['details']; ?>','<?php echo $row['createdby']; ?>','<?php echo $row['assignedto']; ?>','<?php echo $row['dateassigned']; ?>','<?php echo $row['datecompleted']; ?>','<?php echo $row['approvedby']; ?>','<?php echo $row['dateapproved']; ?>')" class="bg-blue-600 text-white px-3 py-2 rounded">
Details
</button>

<button onclick="openAssign('<?php echo $row['ticket_number']; ?>')" class="bg-yellow-500 text-white px-3 py-2 rounded">
Assign
</button>

<button onclick="openApprove('<?php echo $row['ticket_number']; ?>')" class="bg-green-600 text-white px-3 py-2 rounded">
Approve
</button>

<button onclick="openDelete('<?php echo $row['ticket_number']; ?>')" class="bg-red-600 text-white px-3 py-2 rounded">
Delete
</button>

</td>
</tr>

<?php } ?>

</table>

</div>

<!-- Assign Modal -->
<div id="assignModal" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center">
<div class="bg-white p-6 rounded w-96">

<h2 class="text-xl font-bold mb-4">Assign Ticket</h2>

<form method="POST">

<input type="hidden" name="ticket" id="assignTicket">

<select name="tech" class="border p-2 w-full mb-4">

<?php
$tech=mysqli_query($link,"SELECT username FROM tblaccounts WHERE usertype='TECHNICAL'");
while($t=mysqli_fetch_assoc($tech)){
echo "<option>".$t['username']."</option>";
}
?>

</select>

<button name="assign_save" class="bg-yellow-500 text-white px-4 py-2 rounded">
Save
</button>

<button type="button" onclick="closeAssign()" class="ml-2 px-4 py-2 border">
Cancel
</button>

</form>

</div>
</div>

<!-- Approve Modal -->
<div id="approveModal" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center">

<div class="bg-white p-6 rounded w-80 text-center">

<h2 class="text-xl font-bold mb-4">
Approve Ticket?
</h2>

<form method="POST">

<input type="hidden" name="ticket" id="approveTicket">

<button name="approve_confirm" class="bg-green-600 text-white px-4 py-2 rounded">
Approve
</button>

<button type="button" onclick="closeApprove()" class="ml-2 border px-4 py-2">
Cancel
</button>

</form>

</div>

</div>

<!-- Delete Modal -->
<div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center">

<div class="bg-white p-6 rounded w-80 text-center">

<h2 class="text-xl font-bold mb-4">
Delete Ticket?
</h2>

<form method="POST">

<input type="hidden" name="ticket" id="deleteTicket">

<button name="delete_confirm" class="bg-red-600 text-white px-4 py-2 rounded">
Delete
</button>

<button type="button" onclick="closeDelete()" class="ml-2 border px-4 py-2">
Cancel
</button>

</form>

</div>

</div>

<!-- Details Modal -->
<div id="detailsModal" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center">

<div class="bg-white p-6 rounded w-96">

<h2 class="text-xl font-bold mb-4">
Ticket Details
</h2>

<p id="d_ticket"></p>
<p id="d_problem"></p>
<p id="d_details"></p>
<p id="d_createdby"></p>
<p id="d_assignedto"></p>
<p id="d_dateassigned"></p>
<p id="d_datecompleted"></p>
<p id="d_approvedby"></p>
<p id="d_dateapproved"></p>

<button onclick="closeDetails()" class="mt-4 border px-4 py-2">
Close
</button>

</div>

</div>

<script>

function openAssign(ticket){
document.getElementById("assignModal").classList.remove("hidden")
document.getElementById("assignTicket").value=ticket
}

function closeAssign(){
document.getElementById("assignModal").classList.add("hidden")
}

function openApprove(ticket){
document.getElementById("approveModal").classList.remove("hidden")
document.getElementById("approveTicket").value=ticket
}

function closeApprove(){
document.getElementById("approveModal").classList.add("hidden")
}

function openDelete(ticket){
document.getElementById("deleteModal").classList.remove("hidden")
document.getElementById("deleteTicket").value=ticket
}

function closeDelete(){
document.getElementById("deleteModal").classList.add("hidden")
}

function openDetails(ticket,problem,details,createdby,assignedto,dateassigned,datecompleted,approvedby,dateapproved){

document.getElementById("detailsModal").classList.remove("hidden")

document.getElementById("d_ticket").innerText="Ticket: "+ticket
document.getElementById("d_problem").innerText="Problem: "+problem
document.getElementById("d_details").innerText="Details: "+details
document.getElementById("d_createdby").innerText="Created By: "+createdby
document.getElementById("d_assignedto").innerText="Assigned To: "+assignedto
document.getElementById("d_dateassigned").innerText="Date Assigned: "+dateassigned
document.getElementById("d_datecompleted").innerText="Date Completed: "+datecompleted
document.getElementById("d_approvedby").innerText="Approved By: "+approvedby
document.getElementById("d_dateapproved").innerText="Date Approved: "+dateapproved

}

function closeDetails(){
document.getElementById("detailsModal").classList.add("hidden")
}

<?php if($notify!=""){ ?>

alert("<?php echo $notify; ?>");

<?php } ?>

</script>

</body>
</html>