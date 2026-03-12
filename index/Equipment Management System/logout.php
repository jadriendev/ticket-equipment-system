<?php
session_start();
unset($_SESSION['username']);
unset($_SESSION['usertype']);
header("Location: login.php");
?>