<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="font.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="shortcut icon" href="https://www.arellano.edu.ph/sites/default/files/inline-images/new-au-logo.png" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Login Page - Technical Management System</title>
</head>
<body class="flex flex-col items-center justify-center bg-gray-200">
    <main class="flex flex-col items-center justify-center min-h-screen max-w-md w-full">
        <form class="bg-white flex flex-col px-7 py-10 gap-2 rounded-lg shadow-lg w-full" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
            <div class="flex items-center justify-center">
                <img src="https://www.arellano.edu.ph/sites/default/files/inline-images/new-au-logo.png" width="200px">
            </div>
            <hr class="border-t-1 border-gray-300 w-full my-3">
            <label class="font-semibold text-md tracking-wide" for="txtusername">Username:</label>
            <div class="relative w-full">
                <i class="fa-solid fa-user text-md absolute left-3 top-1/2 -translate-y-1/2"></i>
                <input class="border border-2 border-black rounded-lg p-2 pl-10 rounded w-full text-sm w-full" type="text" name="txtusername">
            </div>
            
            <label class="font-semibold text-md tracking-wide" for="txtpassword">Password:</label>
            <div class="relative w-full mb-3">
                <i class="fa-solid fa-lock text-md absolute left-3 top-1/2 -translate-y-1/2"></i>
                <input class="border border-2 border-black rounded-lg p-2 pl-10 rounded w-full text-sm w-full" id="password" type="password" name="txtpassword">
                <i class="fas fa-eye-slash absolute right-3 top-3 text-gray-500 cursor-pointer" id="togglePassword"></i>
            </div>

            <input class="bg-blue-600 text-white p-2 w-full rounded-lg transform-transition transition duration-300 hover:bg-blue-700 cursor-pointer" type="submit" name="btnsubmit" value="Login">
        </form>
        <br>
            <?php
                if(isset($_POST['btnsubmit']))
                {
                    require_once "config.php";
                    $sql = "SELECT * FROM tblaccounts WHERE username = ? AND password = ? AND status = 'ACTIVE'";
                    if($stmt = mysqli_prepare($link, $sql))
                    {
                        mysqli_stmt_bind_param($stmt, "ss", $_POST['txtusername'], $_POST['txtpassword']);
                        if(mysqli_stmt_execute($stmt))
                        {
                            $result = mysqli_stmt_get_result($stmt);
                            if(mysqli_num_rows($result) > 0)
                            {
                                $accounts = mysqli_fetch_array($result, MYSQLI_ASSOC);
                                session_start();
                                $_SESSION['username'] = $accounts['username'];
                                $_SESSION['usertype'] = $accounts['usertype'];
                                header("Location: accounts-management.php");
                            }
                            else
                            {
                                echo "<font color = 'red'>Incorrect username or password.</font>";
                            }
                        }
                        else
                        {
                            echo "<font color = 'red'>ERROR on login statement.</font>";
                        }
                    }
                }
            ?>
    </main>
</body>
<script src="script.js"></script>
</html>
