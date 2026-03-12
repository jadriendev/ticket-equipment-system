<?php
session_start();
require_once "config.php";

$error = "";

if(isset($_POST['btnsubmit']))
{
    $sql = "SELECT * FROM tblaccounts WHERE username = ? AND password = ? AND status = 'ACTIVE'";

    if($stmt = mysqli_prepare($link, $sql))
    {
        mysqli_stmt_bind_param($stmt, "ss", $_POST['txtusername'], $_POST['txtpassword']);

        if(mysqli_stmt_execute($stmt))
        {
            $result = mysqli_stmt_get_result($stmt);

            if(mysqli_num_rows($result) > 0)
            {
                $account = mysqli_fetch_array($result, MYSQLI_ASSOC);

                $_SESSION['username'] = $account['username'];
                $_SESSION['usertype'] = $account['usertype'];

                // ROLE BASED REDIRECT
                switch($account['usertype']) {
                    case "ADMINISTRATOR":
                        header("Location: adminIndex.php");
                        break;

                    case "TECHNICAL":
                        header("Location: techIndex.php");
                        break;

                    default:
                        header("Location: userIndex.php");
                }
                exit();
            }
            else
            {
                $error = "Incorrect username or password.";
            }
        }
        else
        {
            $error = "Error executing login statement.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="https://www.arellano.edu.ph/sites/default/files/inline-images/new-au-logo.png" type="image/x-icon">
    <title>Login | IT Support & Asset Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white shadow-2xl rounded-xl p-8 w-full max-w-md">
        <div class="text-center mb-6">
            <img src="https://www.arellano.edu.ph/sites/default/files/inline-images/new-au-logo.png" class="w-28 mx-auto mb-3">
            <h1 class="text-2xl font-bold text-gray-800">IT Support & Asset Management System</h1>
            <p class="text-gray-500 text-sm">Please login to continue</p>
        </div>

        <form method="POST" class="space-y-4">
            <div>
                <label class="font-semibold text-gray-700">Username</label>
                <div class="relative">
                    <i class="fa fa-user absolute left-3 top-3 text-gray-500"></i>
                    <input type="text" name="txtusername" required
                        class="w-full border rounded-lg pl-10 p-2 focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
            </div>

            <div>
                <label class="font-semibold text-gray-700">Password</label>
                <div class="relative">
                    <i class="fa fa-lock absolute left-3 top-3 text-gray-500"></i>
                    <input type="password" id="password" name="txtpassword" required
                        class="w-full border rounded-lg pl-10 pr-10 p-2 focus:ring-2 focus:ring-blue-500 outline-none">
                    <i id="togglePassword" class="fa fa-eye-slash absolute right-3 top-3 cursor-pointer text-gray-500"></i>
                </div>
            </div>

            <?php if($error): ?>
                <p class="text-red-600 text-sm text-center font-semibold"><?php echo $error; ?></p>
            <?php endif; ?>

            <button type="submit" name="btnsubmit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded-lg transition">
                Login
            </button>

        </form>
    </div>

    <script>
        const toggle = document.getElementById("togglePassword");
        const password = document.getElementById("password");

        toggle.addEventListener("click", () => {
            if(password.type === "password"){
                password.type = "text";
                toggle.classList.replace("fa-eye-slash", "fa-eye");
            } else {
                password.type = "password";
                toggle.classList.replace("fa-eye", "fa-eye-slash");
            }
        });
    </script>
</body>
</html>
