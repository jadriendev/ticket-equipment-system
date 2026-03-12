<?php
require_once "config.php";
include("session-checker.php");

if(isset($_GET['ticket_number']) && !empty(trim($_GET['ticket_number']))) {
    $ticket_number = $_GET['ticket_number'];

    $sql = "SELECT * FROM tbltickets WHERE ticket_number = ?";
    if($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $ticket_number);
        if(mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            $ticket = mysqli_fetch_array($result, MYSQLI_ASSOC);
            if(!$ticket) {
                $_SESSION['toast'] = "Ticket not found.";
                header("Location: ticket-management.php");
                exit();
            }
        } else {
            $_SESSION['toast'] = "Error loading ticket details.";
            header("Location: ticket-management.php");
            exit();
        }
    }
} else {
    header("Location: ticket-management.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Ticket Details - Technical Management System</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">

<main class="w-full max-w-lg px-6">
    <div class="bg-white rounded-2xl shadow-2xl p-8 flex flex-col gap-4">
        <div class="flex flex-col items-center gap-2">
            <img src="https://www.arellano.edu.ph/sites/default/files/inline-images/new-au-logo.png" class="w-32">
            <h1 class="text-2xl font-bold text-gray-800">Ticket Details</h1>
            <p class="text-gray-500 text-sm text-center">View all information about this ticket</p>
        </div>

        <div class="flex flex-col gap-3 mt-4">
            <div class="flex justify-between">
                <span class="font-semibold text-gray-700">Ticket Number:</span>
                <span class="text-gray-800"><?php echo $ticket['ticket_number']; ?></span>
            </div>
            <div class="flex justify-between">
                <span class="font-semibold text-gray-700">Problem:</span>
                <span class="text-gray-800"><?php echo $ticket['problem']; ?></span>
            </div>
            <div class="flex flex-col">
                <span class="font-semibold text-gray-700">Details:</span>
                <p class="text-gray-800 bg-gray-100 p-2 rounded-lg"><?php echo $ticket['details']; ?></p>
            </div>
            <div class="flex justify-between">
                <span class="font-semibold text-gray-700">Status:</span>
                <span class="text-gray-800"><?php echo $ticket['status']; ?></span>
            </div>
            <div class="flex justify-between">
                <span class="font-semibold text-gray-700">Created By:</span>
                <span class="text-gray-800"><?php echo $ticket['createdby']; ?></span>
            </div>
            <div class="flex justify-between">
                <span class="font-semibold text-gray-700">Date Created:</span>
                <span class="text-gray-800"><?php echo $ticket['datecreated']; ?></span>
            </div>
            <div class="flex justify-between">
                <span class="font-semibold text-gray-700">Assigned To:</span>
                <span class="text-gray-800"><?php echo $ticket['assignedto'] ?? '-'; ?></span>
            </div>
            <div class="flex justify-between">
                <span class="font-semibold text-gray-700">Date Assigned:</span>
                <span class="text-gray-800"><?php echo $ticket['dateassigned'] ?? '-'; ?></span>
            </div>
            <div class="flex justify-between">
                <span class="font-semibold text-gray-700">Date Completed:</span>
                <span class="text-gray-800"><?php echo $ticket['datecompleted'] ?? '-'; ?></span>
            </div>
            <div class="flex justify-between">
                <span class="font-semibold text-gray-700">Approved By:</span>
                <span class="text-gray-800"><?php echo $ticket['approvedby'] ?? '-'; ?></span>
            </div>
            <div class="flex justify-between">
                <span class="font-semibold text-gray-700">Date Approved:</span>
                <span class="text-gray-800"><?php echo $ticket['dateapproved'] ?? '-'; ?></span>
            </div>
        </div>

        <div class="mt-6">
            <a href="ticket-management.php" class="w-full text-center bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg transition block">Back to Tickets</a>
        </div>
    </div>
</main>

</body>
</html>
