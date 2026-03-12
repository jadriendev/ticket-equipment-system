<?php
require_once "config.php";
include("session-checker.php");

if(isset($_GET['asset_number'])) {
    $equipmentToDelete = trim($_GET['asset_number']);

    $sql = "DELETE FROM tblequipment WHERE asset_number = ?";
    if($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $equipmentToDelete);
        if(mysqli_stmt_execute($stmt)) {

            $sql_log = "INSERT INTO tbllogs(datelog, timelog, action, module, performedby, performedto) VALUES (?, ?, ?, ?, ?, ?)";
            if($stmt_log = mysqli_prepare($link, $sql_log)) {
                $date = date("d/m/Y");
                $time = date("h:i:sa");
                $action = "Delete Equipment.";
                $module = "Equipment Management System";
                mysqli_stmt_bind_param($stmt_log, "ssssss", $date, $time, $action, $module, $_SESSION['username'], $equipmentToDelete);
                mysqli_stmt_execute($stmt_log);
            }

            session_start();
            $_SESSION['toast'] = "Asset Number \"$equipmentToDelete\" deleted successfully.";

            header("Location: equipment-management.php");
            exit();

        } else {
            session_start();
            $_SESSION['toast'] = "Error deleting equipment \"$equipmentToDelete\".";
            header("Location: equipment-management.php");
            exit();
        }
    }
} else {
    header("Location: equipment-management.php");
    exit();
}
?>
