<?php
require_once "config.php";
include ("session-checker.php");

if(isset($_POST['btnsubmit']))
{
    $sql = "SELECT * FROM tblaccounts WHERE username = ?";
    if($stmt = mysqli_prepare($link, $sql))
    {
        mysqli_stmt_bind_param($stmt, "s", $_POST['txtusername']);
        if(mysqli_stmt_execute($stmt))
        {
            $result = mysqli_stmt_get_result($stmt);
            if(mysqli_num_rows($result) == 0)
            {
                $sql = "INSERT INTO tblaccounts (username, password, usertype, status, createdby, datecreate) VALUES (?, ?, ?, ?, ?, ?)";
                if($stmt = mysqli_prepare($link, $sql))
                {
                    $status = "ACTIVE";
                    $date = date("d/m/Y");
                    mysqli_stmt_bind_param($stmt, "ssssss", $_POST['txtusername'], $_POST['txtpassword'], $_POST['cmbtype'], $status, $_SESSION['username'], $date);
                    if(mysqli_stmt_execute($stmt))
                    {
                        $_SESSION['toast'] = "User account '{$_POST['txtusername']}' created successfully.";
                        header("Location: accounts-management.php");
                        exit();
                    }
                    else
                    {
                        $_SESSION['toast'] = "Error creating account.";
                        header("Location: accounts-management.php");
                        exit();
                    }
                }
            }
            else
            {
                $_SESSION['toast'] = "Username '{$_POST['txtusername']}' already in use.";
                header("Location: accounts-management.php");
                exit();
            }
        }
        else
        {
            $_SESSION['toast'] = "Error validating username.";
            header("Location: accounts-management.php");
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
    <link rel="stylesheet" href="font.css">
    <link rel="shortcut icon" href="https://www.arellano.edu.ph/sites/default/files/inline-images/new-au-logo.png" type="image/x-icon">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Create New Account Page - Technical Management System</title>
</head>
<body class="flex flex-col items-center justify-center bg-gray-100">
    <main class="flex flex-col items-center justify-center min-h-screen max-w-md w-full">
        <form class="bg-white flex flex-col px-7 py-10 gap-2 rounded-lg shadow-lg w-full" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
            <div class="flex items-center justify-center">
                <img src="https://www.arellano.edu.ph/sites/default/files/inline-images/new-au-logo.png" width="200px">
            </div>
            <hr class="border-t-1 border-gray-300 w-full my-3">
            <p class="font-bold text-lg tracking-wide text-center">Fill up this form and submit to create a new account.</p>
            
            <label class="font-semibold text-md tracking-wide" for="txtusername">Username:</label>
            <div class="relative w-full">
                <i class="fa-solid fa-user text-md absolute left-3 top-1/2 -translate-y-1/2"></i>
                <input class="border border-2 border-black rounded-lg p-2 pl-10 rounded w-full text-sm w-full" type="text" name="txtusername" required>
            </div>
            
            <label class="font-semibold text-md tracking-wide" for="txtpassword">Password:</label>
            <div class="relative w-full mb-3">
                <i class="fa-solid fa-lock text-md absolute left-3 top-1/2 -translate-y-1/2"></i>
                <input class="border border-2 border-black rounded-lg p-2 pl-10 rounded w-full text-sm w-full" id="password" type="password" name="txtpassword"  required>
                <i class="fas fa-eye-slash absolute right-3 top-3 text-gray-500 cursor-pointer" id="togglePassword"></i>
            </div>
            
            <label class="font-semibold text-md tracking-wide" for="cmbtype">Account Type:</label>
            <select class="border border-2 border-black rounded-lg p-2 rounded w-full text-sm w-full" name="cmbtype" id="cmbtype" required>
                <option value="">--Select Account Type--</option>
                <option value="ADMINISTRATOR">Administrator</option>
                <option value="TECHNICAL">Technical</option>
                <option value="USER">User</option>
            </select>
            <br>
            <div class="flex items-center justify-center gap-3 w-full">
                <a class="w-1/2 bg-blue-600 p-2 text-white rounded-lg transition transform-transition duration-200 hover:bg-blue-700 cursor-pointer text-center" href="accounts-management.php">Cancel</a>
                <input class="w-1/2 bg-blue-600 p-2 text-white rounded-lg transition transform-transition duration-200 hover:bg-blue-700 cursor-pointer text-center" type="submit" name="btnsubmit" value="Submit">
            </div>
        </form>
    </main>
</body>
<script src="script.js"></script>
</html>