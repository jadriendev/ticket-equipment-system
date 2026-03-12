<?php
session_start();

if(!isset($_SESSION['username']) || $_SESSION['usertype'] != "USER"){
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" href="https://www.arellano.edu.ph/sites/default/files/inline-images/new-au-logo.png" type="image/x-icon">
    <title>User | IT Support & Asset Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
    <header class="bg-blue-900 text-white p-4 flex justify-between items-center">
        <div class="flex items-center gap-3">
            <img src="https://www.arellano.edu.ph/sites/default/files/inline-images/new-au-logo.png" class="w-12">
            <h1 class="text-xl font-bold">IT Support & Asset Management System</h1>
        </div>

        <div class="flex items-center gap-5">
            <p>Welcome, <b><?php echo $_SESSION['username']; ?></b></p>
            <p class="text-sm">Role: <?php echo $_SESSION['usertype']; ?></p>
            <a href="logout.php" class="bg-red-500 px-3 py-1 rounded">
                Logout
            </a>
        </div>
    </header>

    <main class="flex-grow flex items-center justify-center">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <a href="Equipment Management System/equipment-view.php" class="bg-white p-8 rounded-xl shadow-lg text-center hover:scale-105 hover:bg-blue-50 transition duration-300">
                <i class="fa-solid fa-laptop text-5xl text-green-600 mb-4"></i>
                <h2 class="text-lg font-bold">Equipment Management (View Only)</h2>
            </a>

            <a href="Ticket Management Sytem/ticket-management.php" class="bg-white p-8 rounded-xl shadow-lg text-center hover:scale-105 hover:bg-blue-50 transition duration-300">
                <i class="fa-solid fa-ticket text-5xl text-purple-600 mb-4"></i>
                <h2 class="text-lg font-bold">Ticket Management</h2>
            </a>
        </div>
    </main>

    <footer class="bg-blue-900 flex items-center justify-center gap-3 text-white p-5">
        <img src="https://www.arellano.edu.ph/sites/default/files/inline-images/new-au-logo.png" alt="" width="32px">
        Copyright 2026, Jadrien Roi Aguilar
    </footer>
</body>
</html>
