<?php
session_start();
require_once "config.php";

// Check user session
if(!isset($_SESSION['username']) || $_SESSION['usertype'] !== 'USER') {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['username'];

// Handle Search
$search = "";
if(isset($_GET['search'])){
    $search = $_GET['search'];
    $stmt = mysqli_prepare($link, "SELECT * FROM tbltickets WHERE createdby=? AND (ticket_number LIKE ? OR problem LIKE ? OR status LIKE ?) ORDER BY datecreated DESC");
    $like = "%$search%";
    mysqli_stmt_bind_param($stmt, "ssss", $user, $like, $like, $like);
} else {
    $stmt = mysqli_prepare($link, "SELECT * FROM tbltickets WHERE createdby=? ORDER BY datecreated DESC");
    mysqli_stmt_bind_param($stmt, "s", $user);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Fetch Stats
$pending = mysqli_fetch_assoc(mysqli_query($link,"SELECT COUNT(*) AS cnt FROM tbltickets WHERE createdby='$user' AND status='PENDING'"))['cnt'];
$ongoing = mysqli_fetch_assoc(mysqli_query($link,"SELECT COUNT(*) AS cnt FROM tbltickets WHERE createdby='$user' AND status='ON-GOING'"))['cnt'];
$forapproval = mysqli_fetch_assoc(mysqli_query($link,"SELECT COUNT(*) AS cnt FROM tbltickets WHERE createdby='$user' AND status='FOR APPROVAL'"))['cnt'];
$complete = mysqli_fetch_assoc(mysqli_query($link,"SELECT COUNT(*) AS cnt FROM tbltickets WHERE createdby='$user' AND status='Complete'"))['cnt'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Ticket Management - User</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="shortcut icon" href="https://www.arellano.edu.ph/sites/default/files/inline-images/new-au-logo.png">
</head>

<body class="flex bg-gray-100 min-h-screen">

<!-- Sidebar -->
<div class="w-64 bg-white shadow-lg flex flex-col justify-between">

<div>
<div class="p-6 text-center font-bold text-xl border-b">
TMS - User
</div>

<nav class="flex flex-col mt-6 gap-2">
<a href="ticket-management.php" class="py-2 px-4 hover:bg-gray-200 rounded">Dashboard</a>
<a href="create-ticket.php" class="py-2 px-4 hover:bg-gray-200 rounded">Add Ticket</a>
</nav>
</div>

<div class="mb-6">
<hr class="my-4">
<a href="../userIndex.php" class="py-2 px-4 hover:bg-gray-200 rounded text-red-600 font-semibold">
Back
</a>
</div>

</div>

<!-- Main Content -->
<div class="flex-1 p-6">

<h1 class="text-2xl font-bold mb-6">
Ticket Dashboard
</h1>

<!-- Stats -->
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

<!-- Search -->
<div class="flex justify-between mb-4">

<form class="flex gap-2" method="GET">

<input class="p-2 border rounded"
type="text"
name="search"
value="<?php echo htmlspecialchars($search); ?>"
placeholder="Search ticket number / problem / status">

<input class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 cursor-pointer"
type="submit"
value="Search">

</form>

<a href="create-ticket.php"
class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
Add Ticket
</a>

</div>

<!-- Tickets Table -->
<div class="overflow-x-auto shadow-lg bg-white rounded-lg">

<table class="w-full text-center border-collapse">

<thead class="bg-gray-200">

<tr>
<th class="p-3 border">Ticket No</th>
<th class="p-3 border">Problem</th>
<th class="p-3 border">Date Created</th>
<th class="p-3 border">Status</th>
<th class="p-3 border">Action</th>
</tr>

</thead>

<tbody>

<?php
if(mysqli_num_rows($result) > 0){

while($row = mysqli_fetch_assoc($result)){

$date = date("m/d/Y", strtotime($row['datecreated']));

echo "<tr>";

echo "<td class='p-2 border font-semibold'>{$row['ticket_number']}</td>";

echo "<td class='p-2 border'>{$row['problem']}</td>";

echo "<td class='p-2 border'>$date</td>";

echo "<td class='p-2 border font-bold'>{$row['status']}</td>";

echo "<td class='p-2 border flex justify-center gap-1'>

<a href='update-ticket.php?ticket_number={$row['ticket_number']}'
class='bg-blue-600 px-3 py-1 text-white rounded hover:bg-blue-700'>
Update
</a>

<button onclick=\"showDetails('{$row['ticket_number']}')\"
class='bg-gray-600 px-3 py-1 text-white rounded hover:bg-gray-700'>
Details
</button>

<button onclick=\"showDelete('{$row['ticket_number']}')\"
class='bg-red-600 px-3 py-1 text-white rounded hover:bg-red-700'>
Delete
</button>

</td>";

echo "</tr>";

}

} else {

echo "<tr>
<td colspan='5' class='p-3 text-red-600 font-semibold'>
No tickets found.
</td>
</tr>";

}
?>

</tbody>
</table>

</div>

</div>

<!-- Toast -->
<div id="toast"
class="fixed top-5 right-[-400px] bg-green-500 text-white px-6 py-3 rounded shadow-lg font-bold z-50 transition-all duration-500"></div>

<script>

window.addEventListener('DOMContentLoaded', ()=>{

<?php if(isset($_SESSION['toast'])): ?>

const toast = document.getElementById('toast');

toast.innerText = "<?php echo $_SESSION['toast']; ?>";

toast.style.right = '20px';

setTimeout(()=>{
toast.style.right='-400px';
},3000);

<?php unset($_SESSION['toast']); ?>

<?php endif; ?>

});

</script>

</body>
</html>