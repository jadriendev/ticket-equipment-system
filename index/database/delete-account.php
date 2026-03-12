<?php
require_once "config.php";
include("session-checker.php");

if(isset($_GET['username'])) {
    $usernameToDelete = trim($_GET['username']);

    $sql = "DELETE FROM tblaccounts WHERE username = ?";
    if($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $usernameToDelete);
        if(mysqli_stmt_execute($stmt)) {

            $sql_log = "INSERT INTO tbllogs(datelog, timelog, action, module, performedby, performedto) VALUES (?, ?, ?, ?, ?, ?)";
            if($stmt_log = mysqli_prepare($link, $sql_log)) {
                $date = date("d/m/Y");
                $time = date("h:i:sa");
                $action = "Delete account.";
                $module = "Accounts Management";
                mysqli_stmt_bind_param($stmt_log, "ssssss", $date, $time, $action, $module, $_SESSION['username'], $usernameToDelete);
                mysqli_stmt_execute($stmt_log);
            }

            session_start();
            $_SESSION['toast'] = "User account \"$usernameToDelete\" deleted successfully.";

            header("Location: accounts-management.php");
            exit();

        } else {
            session_start();
            $_SESSION['toast'] = "Error deleting account \"$usernameToDelete\".";
            header("Location: accounts-management.php");
            exit();
        }
    }
} else {
    header("Location: accounts-management.php");
    exit();
}
?>
