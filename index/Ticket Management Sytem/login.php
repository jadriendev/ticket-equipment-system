<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="font.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="shortcut icon" href="https://www.arellano.edu.ph/sites/default/files/inline-images/new-au-logo.png" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Login - Ticket Management System</title>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen font-sans">

    <main class="bg-white rounded-2xl shadow-2xl p-10 w-full max-w-md flex flex-col gap-6">
        <div class="flex flex-col items-center gap-2">
            <img src="https://www.arellano.edu.ph/sites/default/files/inline-images/new-au-logo.png" class="w-32">
            <h1 class="text-2xl font-bold text-gray-800">Ticket Management Login</h1>
            <p class="text-gray-500 text-sm text-center">Enter your credentials to access your account</p>
        </div>

        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" class="flex flex-col gap-4">
            <div class="flex flex-col">
                <label for="txtusername" class="font-semibold text-gray-700">Username</label>
                <div class="relative">
                    <i class="fa-solid fa-user absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" name="txtusername" required
                        class="pl-10 p-2 border border-gray-300 rounded-lg w-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>

            <div class="flex flex-col">
                <label for="txtpassword" class="font-semibold text-gray-700">Password</label>
                <div class="relative">
                    <i class="fa-solid fa-lock absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="password" id="password" name="txtpassword" required
                        class="pl-10 p-2 border border-gray-300 rounded-lg w-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <i class="fas fa-eye-slash absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 cursor-pointer" id="togglePassword"></i>
                </div>
            </div>

            <input type="submit" name="btnsubmit" value="Login"
                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded-lg transition duration-300 cursor-pointer">

            <?php
            if(isset($_POST['btnsubmit'])) {
                require_once "config.php";
                $sql = "SELECT * FROM tblaccounts WHERE username = ? AND password = ? AND status = 'ACTIVE'";
                if($stmt = mysqli_prepare($link, $sql)) {
                    mysqli_stmt_bind_param($stmt, "ss", $_POST['txtusername'], $_POST['txtpassword']);
                    if(mysqli_stmt_execute($stmt)) {
                        $result = mysqli_stmt_get_result($stmt);
                        if(mysqli_num_rows($result) > 0) {
                            $accounts = mysqli_fetch_array($result, MYSQLI_ASSOC);
                            session_start();
                            $_SESSION['username'] = $accounts['username'];
                            $_SESSION['usertype'] = $accounts['usertype'];
                            header("Location: ticket-management.php");
                        } else {
                            echo "<p class='text-red-600 text-sm mt-2'>Incorrect username or password.</p>";
                        }
                    } else {
                        echo "<p class='text-red-600 text-sm mt-2'>Error during login attempt.</p>";
                    }
                }
            }
            ?>
        </form>
    </main>

    <script>
        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('password');
        togglePassword.addEventListener('click', () => {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            togglePassword.classList.toggle('fa-eye');
            togglePassword.classList.toggle('fa-eye-slash');
        });
    </script>

</body>
</html>
