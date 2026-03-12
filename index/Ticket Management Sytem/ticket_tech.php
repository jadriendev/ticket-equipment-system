<?php
session_start();
require_once "config.php";

if(!isset($_SESSION['username']) || $_SESSION['usertype'] != "TECHNICAL"){
    header("location: ../../login.php");
    exit();
}

$username = $_SESSION['username'];

// Handle Complete button
if(isset($_POST['complete_confirm'])){
    $ticket = $_POST['ticket'];

    mysqli_query($link, "UPDATE tbltickets 
        SET status='Complete', datecompleted=NOW() 
        WHERE ticket_number='$ticket' AND assignedto='$username'");

        $date = date("m/d/Y");
        $time = date("h:i A");

        mysqli_query($link, "INSERT INTO tbllogs(datelog,timelog,action,module,performedby,performedto) 
        VALUES('$date','$time','Completed ticket $ticket','Ticket Management','$username','$username')");

    $_SESSION['toast'] = "Ticket $ticket marked as Complete!";
    header("Location: ticket_tech.php");
    exit();
}

// Search functionality
$search = "";
if(isset($_GET['search'])){
    $search = $_GET['search'];
    $result = mysqli_query($link, "SELECT * FROM tbltickets 
        WHERE assignedto='$username' AND 
        (ticket_number LIKE '%$search%' OR problem LIKE '%$search%' OR status LIKE '%$search%')
        ORDER BY datecreated DESC");
} else {
    $result = mysqli_query($link, "SELECT * FROM tbltickets 
        WHERE assignedto='$username' 
        ORDER BY datecreated DESC");
}

$pending = mysqli_fetch_assoc(mysqli_query($link,"SELECT COUNT(*) AS cnt FROM tbltickets WHERE assignedto='$username' AND status='PENDING'"))['cnt'];
$ongoing = mysqli_fetch_assoc(mysqli_query($link,"SELECT COUNT(*) AS cnt FROM tbltickets WHERE assignedto='$username' AND status='ON-GOING'"))['cnt'];
$complete = mysqli_fetch_assoc(mysqli_query($link,"SELECT COUNT(*) AS cnt FROM tbltickets WHERE assignedto='$username' AND status='Complete'"))['cnt'];
$forapproval = mysqli_fetch_assoc(mysqli_query($link,"SELECT COUNT(*) AS cnt FROM tbltickets WHERE assignedto='$username' AND status='FOR APPROVAL'"))['cnt'];
?>

<!DOCTYPE html>
<html>
<head>
<link rel="shortcut icon" href="https://www.arellano.edu.ph/sites/default/files/inline-images/new-au-logo.png">
<title>Technical Ticket Management</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="flex bg-gray-100 min-h-screen">

<div class="w-64 bg-white shadow-lg flex flex-col justify-between">

<div>
<div class="p-6 text-center font-bold text-xl border-b">
TMS - Technical
</div>

<nav class="flex flex-col mt-6 gap-2">
<a href="#" class="py-2 px-4 hover:bg-gray-200">
Ticket Management
</a>
</nav>
</div>

<div class="mb-6">
<hr class="my-4">
<a href="../techIndex.php" class="py-2 px-4 hover:bg-gray-200 rounded text-red-600 font-semibold">
Back
</a>
</div>

</div>

<div class="flex-1 p-6">

<h1 class="text-2xl font-bold mb-6">
Ticket Management (Technical)
</h1>

<div class="grid grid-cols-4 gap-4 mb-6">

<div class="bg-yellow-400 p-4 rounded text-white font-bold">
Pending: <?php echo $pending; ?>
</div>

<div class="bg-blue-400 p-4 rounded text-white font-bold">
On-Going: <?php echo $ongoing; ?>
</div>

<div class="bg-green-500 p-4 rounded text-white font-bold">
Complete: <?php echo $complete; ?>
</div>

<div class="bg-orange-400 p-4 rounded text-white font-bold">
For Approval: <?php echo $forapproval; ?>
</div>

</div>

<form method="GET" class="mb-6 flex gap-2">

<input type="text" name="search"
placeholder="Search ticket number, problem or status"
class="border p-2 w-80"
value="<?php echo htmlspecialchars($search); ?>">

<button class="bg-blue-600 text-white px-4 py-2 rounded">
Search
</button>

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

<?php while($row = mysqli_fetch_assoc($result)){ ?>

<tr class="text-center border-b hover:bg-gray-50">

<td class="p-4 font-semibold">
<?php echo $row['ticket_number']; ?>
</td>

<td class="p-4">
<?php echo $row['problem']; ?>
</td>

<td class="p-4">
<?php echo date("m/d/Y", strtotime($row['datecreated'])); ?>
</td>

<td class="p-4">
<?php echo date("h:i A", strtotime($row['datecreated'])); ?>
</td>

<td class="p-4 font-bold">
<?php echo $row['status']; ?>
</td>

<td class="p-4 space-x-2">

<button onclick="openComplete('<?php echo $row['ticket_number']; ?>')" 
class="bg-green-600 text-white px-3 py-2 rounded">

Complete

</button>

</td>

</tr>

<?php } ?>

</table>

</div>

<!-- Complete Modal -->
<div id="completeModal" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center">

<div class="bg-white p-6 rounded w-80 text-center">

<h2 class="text-xl font-bold mb-4">
Complete Ticket?
</h2>

<form method="POST">

<input type="hidden" name="ticket" id="completeTicket">

<button name="complete_confirm"
class="bg-green-600 text-white px-4 py-2 rounded">
Complete
</button>

<button type="button"
onclick="closeComplete()"
class="ml-2 border px-4 py-2">
Cancel
</button>

</form>

</div>

</div>

<!-- Toast Notification -->
<?php if(isset($_SESSION['toast'])): ?>

<div id="toast"
class="fixed bottom-6 right-6 bg-green-600 text-white px-6 py-3 rounded shadow-lg animate-bounce">

<?php echo $_SESSION['toast']; unset($_SESSION['toast']); ?>

</div>

<script>

setTimeout(() => {
document.getElementById('toast').remove();
},3000);

</script>

<?php endif; ?>

<script>

function openComplete(ticket){
document.getElementById("completeModal").classList.remove("hidden");
document.getElementById("completeTicket").value = ticket;
}

function closeComplete(){
document.getElementById("completeModal").classList.add("hidden");
}

</script>

</body>
</html>