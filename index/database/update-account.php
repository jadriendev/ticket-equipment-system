<?php
    require_once "config.php";
    include("session-checker.php");

    if (isset($_POST['btnsubmit'])) 
    {
        $sql = "UPDATE tblaccounts SET password = ?, usertype = ?, status = ? WHERE username = ?";
        if ($stmt = mysqli_prepare($link, $sql)) 
        {
            mysqli_stmt_bind_param($stmt, "ssss", $_POST['txtpassword'], $_POST['cmbtype'], $_POST['rbstatus'], $_GET['username']);
            if (mysqli_stmt_execute($stmt)) 
            {
                $sql = "INSERT INTO tbllogs(datelog, timelog, action, module, performedby, performedto) VALUES (?, ?, ?, ?, ?, ?)";

                if ($stmt = mysqli_prepare($link, $sql)) 
                {
                    $date = date("d/m/Y");
                    $time = date("h:i:sa");
                    $action = "Update account.";
                    $module = "Accounts Management";

                    mysqli_stmt_bind_param($stmt, "ssssss", $date, $time, $action, $module, $_SESSION['username'], $_GET['username']);
                    mysqli_stmt_execute($stmt);
                }

                $_SESSION['toast'] = "User account '{$_GET['username']}' updated successfully.";
                header("Location: accounts-management.php");
                exit();
            }
            else 
            {
                $_SESSION['toast'] = "Error updating account details.";
                header("Location: accounts-management.php");
                exit();
            }
        }
    }
    else 
    {
        if (isset($_GET['username']) && !empty(trim($_GET['username']))) 
        {
            $sql = "SELECT * FROM tblaccounts WHERE username = ?";
            if ($stmt = mysqli_prepare($link, $sql)) 
            {
                mysqli_stmt_bind_param($stmt, "s", $_GET['username']);
                if (mysqli_stmt_execute($stmt)) 
                {
                    $result = mysqli_stmt_get_result($stmt);
                    $account = mysqli_fetch_array($result, MYSQLI_ASSOC);
                }
                else 
                {
                    $_SESSION['toast'] = "Error loading account details.";
                    header("Location: accounts-management.php");
                    exit();
                }
            }
        }
        else 
        {
            header("Location: accounts-management.php");
            exit();
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
    <title>Update Account Page - Technical Management System</title>
</head>
<body class="flex flex-col items-center justify-center bg-gray-100">
    <main class="flex flex-col items-center justify-center min-h-screen max-w-md w-full">
        <form class="bg-white flex flex-col px-7 py-10 gap-2 rounded-lg shadow-lg w-full" action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method = "POST">
            <div class="flex items-center justify-center">
                <img src="https://www.arellano.edu.ph/sites/default/files/inline-images/new-au-logo.png" width="200px">
            </div>
            <hr class="border-t-1 border-gray-300 w-full my-3">
            <p class="font-bold text-lg tracking-wide text-center">Change the value on this form and submit to update the account.</p>
            
            <label class="font-semibold text-md tracking-wide" for="txtusername">Username:</label>
            <div class="relative w-full">
                <i class="fa-solid fa-user text-md absolute left-3 top-1/2 -translate-y-1/2"></i>
                <input class="border border-2 border-black rounded-lg p-2 pl-10 rounded w-full text-sm w-full" value="<?php echo $account['username']; ?>" type="text" name="txtusername" required>
            </div>
            
            <label class="font-semibold text-md tracking-wide" for="txtpassword">Password:</label>
            <div class="relative w-full mb-3">
                <i class="fa-solid fa-lock text-md absolute left-3 top-1/2 -translate-y-1/2"></i>
                <input class="border border-2 border-black rounded-lg p-2 pl-10 rounded w-full text-sm w-full" value="<?php echo $account['password']; ?>" id="password" type="password" name="txtpassword" required>
                <i class="fas fa-eye-slash absolute right-3 top-3 text-gray-500 cursor-pointer" id="togglePassword"></i>
            </div>

            <label class="font-semibold text-md tracking-wide">Current Account Type:</label>
            <div class="relative w-full mb-3">
                <i class="fa-solid fa-user text-md absolute left-3 top-1/2 -translate-y-1/2"></i>
                <input class="border border-2 border-black rounded-lg p-2 pl-10 rounded w-full text-sm w-full" value="<?php echo $account['usertype']; ?>" type="text">
            </div>
            
            <label class="font-semibold text-md tracking-wide" for="cmbtype">Change Account Type:</label>
            <select class="border border-2 border-black rounded-lg p-2 rounded w-full text-sm w-full" name="cmbtype" id="cmbtype" required>
                <option value="">--Select Account Type--</option>
                <option value="ADMINISTRATOR">Administrator</option>
                <option value="TECHNICAL">Technical</option>
                <option value="USER">User</option>
            </select>

            <label class="font-semibold text-md tracking-wide block mb-2">Status:</label>
                <?php
                    $status = $account['status'];
                    if ($status == 'ACTIVE')  
                    {
                ?>
                        <label class="flex items-center gap-2 cursor-pointer mb-1">
                            <input type="radio" name="rbstatus" value="ACTIVE" checked class="accent-purple-600 w-4 h-4">
                            <span class="text-sm">Active</span>
                        </label>

                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="rbstatus" value="INACTIVE" class="accent-purple-600 w-4 h-4">
                            <span class="text-sm">Inactive</span>
                        </label>
                    <?php
                    }
                    else {
                    ?>
                        <label class="flex items-center gap-2 cursor-pointer mb-1">
                            <input type="radio" name="rbstatus" value="ACTIVE" checked class="accent-purple-600 w-4 h-4">
                            <span class="text-sm">Active</span>
                        </label>

                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="rbstatus" value="INACTIVE" class="accent-purple-600 w-4 h-4">
                            <span class="text-sm">Inactive</span>
                        </label>
                        <?php
                    }
                        ?>
            <br>
            <div class="flex items-center justify-center gap-3 w-full">
                <a class="w-1/2 bg-blue-600 p-2 text-white rounded-lg transition transform-transition duration-200 hover:bg-blue-700 cursor-pointer text-center" href="accounts-management.php">Cancel</a>
                <input class="w-1/2 bg-blue-600 p-2 text-white rounded-lg transition transform-transition duration-200 hover:bg-blue-700 cursor-pointer text-center" type="submit" name="btnsubmit">
            </div>
        </form>
    </main>
    <script src="script.js"></script>
</body>
</html>