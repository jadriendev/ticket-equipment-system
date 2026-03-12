<?php
session_start();
require_once "config.php";

if(!isset($_SESSION['username']) || $_SESSION['usertype'] !== 'USER'){
    header("Location: login.php");
    exit();
}

if(isset($_GET['ticket_number'])){
    $ticket = $_GET['ticket_number'];
    $user = $_SESSION['username'];

    // Delete ticket
    $stmt = mysqli_prepare($link, "DELETE FROM tbltickets WHERE ticket_number=? AND createdby=?");
    mysqli_stmt_bind_param($stmt, "ss", $ticket, $user);

    if(mysqli_stmt_execute($stmt)){
        // Log the deletion
        $stmt_log = mysqli_prepare($link, "INSERT INTO tbllogs(datelog, timelog, action, module, performedby, performedto) VALUES(?, ?, ?, ?, ?, ?)");
        $datelog = date("Y-m-d");
        $timelog = date("H:i:s");
        $action = "Deleted ticket";
        $module = "Ticket Management";
        $performedby = $user;
        $performedto = $ticket;
        mysqli_stmt_bind_param($stmt_log, "ssssss", $datelog, $timelog, $action, $module, $performedby, $performedto);
        mysqli_stmt_execute($stmt_log);

        // Set toast for deletion success
        $_SESSION['toast'] = "Ticket $ticket deleted successfully!";
    } else {
        $_SESSION['toast'] = "Error deleting ticket $ticket!";
    }
}

// Redirect back to ticket dashboard
header("Location: ticket-management.php");
exit();
?>