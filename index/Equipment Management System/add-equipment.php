<?php
require_once "config.php";
include ("session-checker.php");

function validateYear() {
    if(empty($_POST['txtyear'])) {
        echo "<script>alert('Year should not be empty.');</script>";
        return 1;
    }
    elseif(!is_numeric($_POST['txtyear'])) {
        echo "<script>alert('Year must be a number.');</script>";
        return 1;
    }
    elseif(strlen(trim($_POST['txtyear'])) != 4) {
        echo "<script>alert('Year must be 4 digits.');</script>";
        return 1;
    }
    return 0;
}

function validateUniqueness($link) {
    $errorUnique = 0;
    $sql = "SELECT asset_number, serial_number FROM tblequipment WHERE asset_number = ? OR serial_number = ?";
    if($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "ss", $_POST['txtasset'], $_POST['txtserial']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            if($row['asset_number'] == $_POST['txtasset']) {
                echo "<script>alert('Asset number is already in use.');</script>";
                $errorUnique++;
            }
            if($row['serial_number'] == $_POST['txtserial']) {
                echo "<script>alert('Serial number is already in use.');</script>";
                $errorUnique++;
            }
        }
    }
    return $errorUnique;
}

if(isset($_POST['btnsubmit']))
{
    $errors = 0;
    $errors += validateYear();
    $errors += validateUniqueness($link);

    if($errors == 0)
    {
        $sql = "SELECT * FROM tblequipment WHERE asset_number = ?";
        if($stmt = mysqli_prepare($link, $sql))
        {
            mysqli_stmt_bind_param($stmt, "s", $_POST['txtasset']);
            if(mysqli_stmt_execute($stmt))
            {
                $result = mysqli_stmt_get_result($stmt);
                if(mysqli_num_rows($result) == 0)
                {
                    $sql = "INSERT INTO tblequipment (asset_number, serial_number, type, manufacturer, year_model, description, branch, department, status, createdby, datecreated) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    if($stmt = mysqli_prepare($link, $sql))
                    {
                        $status = "WORKING";
                        $date = date("d/m/Y");
                        mysqli_stmt_bind_param($stmt, "sssssssssss", $_POST['txtasset'], $_POST['txtserial'], $_POST['cmbtype'], $_POST['txtmanu'], $_POST['txtyear'], $_POST['txtdesc'], $_POST['cmbbranches'], $_POST['cmbdepartment'], $status, $_SESSION['username'], $date);
                        if(mysqli_stmt_execute($stmt))
                        {
                            $sqlLog = "INSERT INTO tbllogs(datelog, timelog, action, module, performedby, performedto) VALUES (?, ?, ?, ?, ?, ?)";
                            if($stmtLog = mysqli_prepare($link, $sqlLog)) {
                                $date = date("d/m/Y");
                                $time = date("h:i:sa");
                                $action = "Add Equipment"; // or "Update Equipment" if this is update
                                $module = "Equipment Management"; // define your module name
                                $performedby = $_SESSION['username'];
                                $performedto = $_POST['txtasset'];

                                mysqli_stmt_bind_param($stmtLog, "ssssss", $date, $time, $action, $module, $performedby, $performedto);
                                mysqli_stmt_execute($stmtLog); // execute the log insertion
                            }
                            $_SESSION['toast'] = "Equipment '{$_POST['cmbtype']}' created successfully.";
                            header("Location: equipment-management.php");
                            exit();
                        }
                        else
                        {
                            $_SESSION['toast'] = "Error adding equipment.";
                            header("Location: equipment-management.php");
                            exit();
                        }
                    }
                }
                else
                {
                    $_SESSION['toast'] = "Equipment '{$_POST['cmbtype']}' already added.";
                    header("Location: equipment-management.php");
                    exit();
                }
            }
            else
            {
                $_SESSION['toast'] = "Error validating equipment.";
                header("Location: equipment-management.php");
                exit();
            }
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
    <title>Add New Equipment Page - Equipment Management System</title>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-6">
    <main class="w-full max-w-4xl">
        <form class="bg-white rounded-xl shadow-lg p-8 space-y-6" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
            <div class="flex flex-col items-center gap-2">
                <img src="https://www.arellano.edu.ph/sites/default/files/inline-images/new-au-logo.png" class="w-36">
                <p class="font-bold text-lg tracking-wide text-center">Add New Equipment</p>
                <p class="text-sm text-gray-600 text-center">Change the values and submit to add the equipment.</p>
            </div>

            <hr class="border-gray-300">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="font-semibold text-sm">Asset Number</label>
                    <div class="relative w-full">
                        <i class="fa-solid fa-user text-md absolute left-3 top-1/2 -translate-y-1/2"></i>
                        <input class="w-full border rounded-lg p-2 pl-10 text-sm" type="text" name="txtasset" required>
                    </div>
                </div>
                <div>
                    <label class="font-semibold text-sm">Serial Number</label>
                    <div class="relative w-full mb-3">
                        <i class="fa-solid fa-lock text-md absolute left-3 top-1/2 -translate-y-1/2"></i>
                        <input class="w-full border rounded-lg p-2 pl-10 text-sm" type="text" name="txtserial" required>
                    </div>
                </div>
                <div>
                    <label class="font-semibold text-md tracking-wide" for="cmbtype">Equipment Type:</label>
                        <select class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-black focus:border-black" name="cmbtype" id="cmbtype" required>
                            <option value="">--Select Equipment Type--</option>
                            <option value="MONITOR">Monitor</option>
                            <option value="CPU">CPU</option>
                            <option value="KEYBOARD">Keyboard</option>
                            <option value="MOUSE">Mouse</option>
                            <option value="AVR">AVR</option>
                            <option value="MAC">Mac</option>
                            <option value="PRINTER">Printer</option>
                            <option value="PROJECTOR">Projector</option>
                        </select>
                </div>
                <div>
                    <label class="font-semibold text-sm">Manufacturer</label>
                    <div class="relative w-full">
                        <input class="w-full border rounded-lg p-2 text-sm" type="text" name="txtmanu" required>
                    </div>
                </div>
                <div>
                    <label class="font-semibold text-sm">Year Model</label>
                    <div class="relative w-full">
                        <input class="w-full border rounded-lg p-2 text-sm" type="text" name="txtyear" required>
                    </div>
                </div>
                <div class="md:col-span-2">
                    <label class="font-semibold text-sm">Description</label>
                    <textarea class="w-full border rounded-lg p-2 text-sm mt-1" rows="3" name="txtdesc"></textarea>
                </div>
                <div>
                    <label class="font-semibold text-md tracking-wide" for="cmbbranches">Branches:</label>
                        <select class="w-full border rounded-lg p-2 text-sm" name="cmbbranches" id="cmbbranches" required>
                            <option value="">--Select Branches--</option>
                            <option value="JUAN SUMULONG CAMPUS">Juan Sumulong Campus</option>
                            <option value="JOSE ABAD SANTOS CAMPUS">Jose Abad Santos Campus</option>
                            <option value="APOLINARIO MABINI CAMPUS">Apolinario Mabini Campus</option>
                            <option value="ANDRES BONIFACIO CAMPUS">Andres Bonifacio Campus</option>
                            <option value="ELISA ESGUERRA CAMPUS">Elisa Esguerra Campus</option>
                            <option value="JOSE RIZAL CAMPUS">Jose Rizal Campus</option>
                            <option value="PLARIDEL CAMPUS">Plaridel Campus</option>
                        </select>
                </div>
                <div class="md:col-span-2">
                    <label class="font-semibold text-md tracking-wide" for="cmbdepartment">Department:</label>
                        <select class="w-full border rounded-lg p-2 text-sm mt-1" name="cmbdepartment" required>
                            <option value="">--Select Department--</option>
                            <option value="COLLEGE OF ARTS AND SCIENCE">College of Arts and Science</option>
                            <option value="SCHOOL OF BUSINESS AND ADMINISTRATION">School of Business and Administration</option>
                            <option value="SCHOOL OF BUSINESS AND COMMERCE">School of Business and Commerce</option>
                            <option value="SCHOOL OF EDUCATION">School of Education</option>
                            <option value="SCHOOL OF COMPUTER SCIENCE">School of Computer Science</option>
                            <option value="SCHOOL OF HOSPITALITY AND TOURISM MANAGEMENT">School of Hospitality and Tourism Management</option>
                            <option value="COLLEGE OF CRIMINAL JUSTICE EDUCATION">College of Criminal Justice Education</option>
                            <option value="INSTITUTE OF ACCOUNTANCY">Institute of Accountancy</option>
                            <option value="ALLIED MEDICAL/HEALTH SCIENCES PROGRAMS">Allied Medical/Health Science Programs</option>
                            <option value="ARELLANO UNIVERSITY SCHOOL OF LAW">Arellano University School of LAW</option>
                        </select>
                </div>
            <div class="md:col-span-2 flex justify-end gap-3 pt-6 border-t border-gray-200">
                <a href="equipment-management.php" class="px-5 py-2 rounded-lg border border-gray-400 text-gray-700 hover:bg-gray-100">Cancel</a>
                <input type="submit" name="btnsubmit" class="px-5 py-2 rounded-lg bg-[#004ea8] text-white hover:bg-blue-700 cursor-pointer">
            </div>
        </form>
    </main>
</body>
</html>