<?php
require_once "config.php";
include("session-checker.php");

if(isset($_POST['btnsubmit'])) {
    $ticket_number = $_GET['ticket_number'];
    $problem = $_POST['cmbproblem'];
    $details = $_POST['txtdetails'];

    $sql = "UPDATE tbltickets SET problem = ?, details = ? WHERE ticket_number = ?";
    if($stmt = mysqli_prepare($link, $sql)) {
        $sql_log = "INSERT INTO tbllogs(datelog, timelog, action, module, performedby, performedto) 
                VALUES (?, ?, ?, ?, ?, ?)";
    if($stmt_log = mysqli_prepare($link, $sql_log)) {
        $datelog = date("Y-m-d");
        $timelog = date("H:i:s");
        $action = "Updated ticket";
        $module = "Ticket Management";
        $performedby = $_SESSION['username'];
        $performedto = $ticket_number;

        mysqli_stmt_bind_param($stmt_log, "ssssss", $datelog, $timelog, $action, $module, $performedby, $performedto);
        mysqli_stmt_execute($stmt_log);
    }
        mysqli_stmt_bind_param($stmt, "sss", $problem, $details, $ticket_number);
        if(mysqli_stmt_execute($stmt)) {
            $_SESSION['toast'] = "Ticket $ticket_number updated successfully.";
            header("Location: ticket-management.php");
            exit();
        } else {
            $_SESSION['toast'] = "Error updating ticket.";
            header("Location: ticket-management.php");
            exit();
        }
    }
} else {
    if(isset($_GET['ticket_number']) && !empty(trim($_GET['ticket_number']))) {
        $sql = "SELECT * FROM tbltickets WHERE ticket_number = ?";
        if($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $_GET['ticket_number']);
            if(mysqli_stmt_execute($stmt)) {
                $result = mysqli_stmt_get_result($stmt);
                $ticket = mysqli_fetch_array($result, MYSQLI_ASSOC);
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
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Ticket - Ticket Management System</title>
    <link rel="shortcut icon" href="https://www.arellano.edu.ph/sites/default/files/inline-images/new-au-logo.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
<main class="w-full max-w-lg px-6">
    <div class="bg-white rounded-2xl shadow-2xl p-8 flex flex-col gap-6">
        <div class="flex flex-col items-center gap-2">
            <img src="https://www.arellano.edu.ph/sites/default/files/inline-images/new-au-logo.png" class="w-32">
            <h1 class="text-2xl font-bold text-gray-800">Update Ticket</h1>
            <p class="text-gray-500 text-sm text-center">Edit the ticket details and submit to save changes</p>
        </div>

        <form action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>" method="POST" class="flex flex-col gap-4">
            <div class="flex flex-col">
                <label class="font-semibold text-gray-700">Ticket Number</label>
                <input type="text" value="<?php echo $ticket['ticket_number']; ?>" readonly class="bg-gray-100 border border-gray-300 rounded-lg p-2 text-gray-700 font-semibold">
            </div>

            <div class="flex flex-col">
                <label class="font-semibold text-gray-700">Problem</label>
                <select name="cmbproblem" required class="border border-gray-300 rounded-lg p-2 text-gray-700">
                    <option value="Hardware" <?php if($ticket['problem']=="Hardware") echo "selected"; ?>>Hardware</option>
                    <option value="Software" <?php if($ticket['problem']=="Software") echo "selected"; ?>>Software</option>
                    <option value="Connection" <?php if($ticket['problem']=="Connection") echo "selected"; ?>>Connection</option>
                </select>
            </div>

            <div class="flex flex-col">
                <label class="font-semibold text-gray-700">Details</label>
                <textarea name="txtdetails" rows="4" required class="border border-gray-300 rounded-lg p-2 text-gray-700 resize-none"><?php echo $ticket['details']; ?></textarea>
            </div>

            <div class="flex gap-4 mt-4">
                <a href="ticket-management.php" class="flex-1 text-center bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg transition">Cancel</a>
                <input type="submit" name="btnsubmit" value="Save" class="flex-1 bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg cursor-pointer transition">
            </div>

        </form>
    </div>
</main>

</body>
</html>
