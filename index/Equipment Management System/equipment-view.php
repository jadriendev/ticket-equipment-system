<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="font.css">
    <link rel="shortcut icon" href="https://www.arellano.edu.ph/sites/default/files/inline-images/new-au-logo.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@7.1.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Equipment Management Page - Equipment Management System</title>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
    <nav class="bg-[#004ea8] flex items-center justify-between py-2 px-10 shadow-lg">
        <div class="flex gap-3 items-center">
            <img src="https://www.arellano.edu.ph/sites/default/files/inline-images/new-au-logo.png" width="70px">
            <h1 class="font-semibold text-white text-2xl tracking-wide">Equipment Management System</h1>
        </div>

        <div class="flex items-center gap-10">
            <?php
                session_start();
                if(isset($_SESSION['username']))
                {
                    echo "<h4 class='font-semibold text-lg text-white tracking-wide'>Account Type: " . $_SESSION['usertype'] . "</h4>";
                    echo "<h1 class='font-semibold text-xl text-white tracking-wide'>Welcome, " . $_SESSION['username'] . "</h1>";    
                }
                else
                {
                    header("Location: login.php");
                }
            ?>
        </div>
    </nav>

    <main class="p-10 flex-grow">
        <form class="flex items-center mb-3 justify-center" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
            <div class="bg-white flex items-center justify-center py-6 px-7 gap-2 rounded-lg shadow-lg mb-3">
                <label class="font-semibold">Search:</label>
                <input class="p-1 border-2 border-black rounded-lg" type="text" name="txtsearch" placeholder="Search...">
                <a class="bg-[#004ea8] py-2 px-5 text-white rounded-lg hover:bg-[#005FD4] cursor-pointer" href="../userIndex.php"><i class="fa-solid fa-arrow-left"></i> Back</a>
                <input class="bg-[#004ea8] text-white py-2 px-5 rounded-lg hover:bg-[#005FD4] cursor-pointer" type="submit" name="btnsearch" value="Search">
            </div>
        </form>

        <?php

        function buildtable($result)
        {
            if(mysqli_num_rows($result) > 0)
            {
                echo "<div class='shadow-lg'>";
                echo "<table class='bg-white w-full text-center' cellpadding='20'>";

                echo "<tr class='border-b bg-[#004ea8]'>";
                echo "<th class='rounded-tl-xl text-white'>Asset Number</th>
                    <th class='text-white'>Serial Number</th>
                    <th class='text-white'>Type</th>
                    <th class='text-white'>Branch</th>
                    <th class='text-white'>Status</th>
                    <th class='rounded-tr-xl text-white'>Created By</th>";
                echo "</tr>";

                while($row = mysqli_fetch_array($result))
                {
                    echo "<tr class='odd:bg-white even:bg-gray-50'>";
                    echo "<td class='border-b'>" . $row['asset_number'] . "</td>";
                    echo "<td class='border-b'>" . $row['serial_number'] . "</td>";
                    echo "<td class='border-b'>" . $row['type'] . "</td>";
                    echo "<td class='border-b'>" . $row['branch'] . "</td>";
                    echo "<td class='border-b'>" . $row['status'] . "</td>";
                    echo "<td class='border-b'>" . $row['createdby'] . "</td>";
                    echo "</tr>";
                }

                echo "</table>";
                echo "</div>";
            }
            else
            {
                echo "<h2 class='text-center font-semibold text-red-600'>No record/s found.</h2>";
            }
        }

        require_once "config.php";

        if(isset($_POST['btnsearch']))
        {
            $sql = "SELECT * FROM tblequipment 
                    WHERE asset_number LIKE ? 
                    OR serial_number LIKE ? 
                    OR type LIKE ? 
                    OR branch LIKE ? 
                    OR status LIKE ?
                    ORDER BY asset_number";

            $stmt = mysqli_prepare($link, $sql);

            $search = '%' . $_POST['txtsearch'] . '%';
            mysqli_stmt_bind_param($stmt, "sssss", $search, $search, $search, $search, $search);

            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            buildtable($result);
        }
        else
        {
            $sql = "SELECT * FROM tblequipment ORDER BY asset_number";
            $stmt = mysqli_prepare($link, $sql);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            buildtable($result);
        }
        ?>
    </main>

    <footer class="bg-[#004ea8] shadow-lg">
        <div class="flex items-center justify-between py-5 px-10">
            <div class="flex items-center">
                <img width="90px" class="mr-3" src="https://www.arellano.edu.ph/sites/default/files/inline-images/new-au-logo.png" alt="Arellano University">
                <h1 class="text-2xl text-white font-semibold tracking-wide">Equipment Management System</h1>
            </div>
            <div class="flex items-center gap-2">
                <a href="https://www.facebook.com/jadrienwebdev" target="_blank"><i class="fa-brands fa-facebook text-white text-4xl transform transition duration-300 hover:scale-110"></i></a>
                <a href="https://github.com/jadriendev" target="_blank"><i class="fa-brands fa-github text-white text-4xl transform transition duration-300 hover:scale-110"></i></a>
                <a href="https://www.linkedin.com/in/jadrien-roi-aguilar-42b993362/" target="_blank"><i class="fa-brands fa-linkedin text-white text-4xl transform transition duration-300 hover:scale-110"></i></a>
            </div>
        </div>
        <hr class="border-gray-300">
        <div class="flex flex-col items-center justify-center py-4 text-gray-400">
            <div class="flex items-center gap-2">
                <span class="text-md tracking-wide text-gray-200">© 2026 • Jadrien Roi Aguilar |</span>
                <span class="text-sm tracking-wide text-gray-200">BSIT-2A</span>
            </div>
            <div class="text-sm tracking-wide mt-1 text-gray-200">
                ITC127 Advance Database System
            </div>
        </div>
    </footer>
</body>
</html>