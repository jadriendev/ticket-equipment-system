<?php
require_once "config.php";
include("session-checker.php");

$ticket_number = date("YmdHis"); 
$datecreated = date("Y-m-d H:i:s");

if(isset($_POST['btnsubmit'])) {
    $ticket_number = $_POST['txtticket'];
    $problem = $_POST['cmbproblem'];
    $details = $_POST['txtdetails'];
    $status = "PENDING";
    $createdby = $_SESSION['username'];

    $sql = "INSERT INTO tbltickets 
        (ticket_number, problem, details, status, createdby, datecreated, assignedto, dateassigned, datecompleted, approvedby, dateapproved)
        VALUES (?, ?, ?, ?, ?, ?, NULL, NULL, NULL, NULL, NULL)";

    if($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "ssssss", $ticket_number, $problem, $details, $status, $createdby, $datecreated);
        if(mysqli_stmt_execute($stmt)) {
            // Log
            $sql_log = "INSERT INTO tbllogs(datelog, timelog, action, module, performedby, performedto) 
                        VALUES (?, ?, ?, ?, ?, ?)";
            if($stmt_log = mysqli_prepare($link, $sql_log)) {
                $datelog = date("Y-m-d");
                $timelog = date("H:i:s");
                $action = "Created ticket";
                $module = "Ticket Management";
                $performedby = $_SESSION['username'];
                $performedto = $ticket_number;
                mysqli_stmt_bind_param($stmt_log, "ssssss", $datelog, $timelog, $action, $module, $performedby, $performedto);
                mysqli_stmt_execute($stmt_log);
            }

            $_SESSION['toast'] = "Ticket $ticket_number created successfully!";
            header("Location: ticket-management.php");
            exit();
        } else {
            $_SESSION['toast'] = "Error creating ticket!";
            header("Location: ticket-management.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Create Ticket - Ticket Management System</title>
<link rel="shortcut icon" href="https://www.arellano.edu.ph/sites/default/files/inline-images/new-au-logo.png" type="image/x-icon">
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>
/* Toast styles */
#toast {
    position: fixed;
    top: 20px;
    right: -400px;
    background-color: #22c55e; /* green for success */
    color: white;
    padding: 15px 25px;
    border-radius: 8px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    font-weight: bold;
    transition: right 0.5s ease;
    z-index: 1000;
}
#toast.show {
    right: 20px;
}
#toast.error {
    background-color: #ef4444; /* red for error */
}
</style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">

<main class="w-full max-w-lg px-6">
    <div class="bg-white rounded-2xl shadow-2xl p-8 flex flex-col gap-6">
        <div class="flex flex-col items-center gap-2">
            <img src="https://www.arellano.edu.ph/sites/default/files/inline-images/new-au-logo.png" class="w-32">
            <h1 class="text-2xl font-bold text-gray-800">Create New Ticket</h1>
            <p class="text-gray-500 text-sm text-center">Fill out the form below</p>
        </div>

        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" class="flex flex-col gap-4">
            <div class="flex flex-col">
                <label class="font-semibold text-gray-700">Ticket Number</label>
                <input type="text" name="txtticket" value="<?php echo $ticket_number; ?>" readonly
                    class="bg-gray-100 border border-gray-300 rounded-lg p-2 text-gray-700 font-semibold">
            </div>

            <div class="flex flex-col">
                <label class="font-semibold text-gray-700">Problem</label>
                <select name="cmbproblem" required class="border border-gray-300 rounded-lg p-2 text-gray-700">
                    <option value="">-- Select Problem --</option>
                    <option value="Hardware">Hardware</option>
                    <option value="Software">Software</option>
                    <option value="Connection">Connection</option>
                </select>
            </div>

            <div class="flex flex-col">
                <label class="font-semibold text-gray-700">Details</label>
                <textarea name="txtdetails" rows="4" placeholder="Describe your issue..." required
                    class="border border-gray-300 rounded-lg p-2 text-gray-700 resize-none"></textarea>
            </div>

            <div class="flex gap-4 mt-4">
                <a href="ticket-management.php" class="flex-1 text-center bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg transition">Cancel</a>
                <input type="submit" name="btnsubmit" value="Save" class="flex-1 bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg cursor-pointer transition">
            </div>
        </form>
    </div>
</main>

<!-- Toast Notification -->
<div id="toast"></div>

<script>
window.addEventListener('DOMContentLoaded', (event) => {
    <?php if(isset($_SESSION['toast'])): ?>
        let toast = document.getElementById('toast');
        toast.innerText = "<?php echo $_SESSION['toast']; ?>";
        toast.classList.add('show');
        <?php if(strpos($_SESSION['toast'], 'Error') !== false): ?>
            toast.classList.add('error');
        <?php endif; ?>
        setTimeout(() => { toast.classList.remove('show'); }, 3000);
        <?php unset($_SESSION['toast']); ?>
    <?php endif; ?>
});
</script>

</body>
</html>