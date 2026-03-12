<?php
include("config.php");

if(isset($_GET['username'])) {

    $username = $_GET['username'];

    $sql = "DELETE FROM tblaccounts WHERE username = ?";
    $stmt = mysqli_prepare($link, $sql);

    mysqli_stmt_bind_param($stmt, "s", $username);

    if(mysqli_stmt_execute($stmt)) 
    {
        echo "Account Deleted";
        header("Location: accounts-management.php");
        exit();
    } 
    mysqli_stmt_close($stmt);
    mysqli_close($link);
} 
else 
{
    echo "No username provided.";
}
?>
