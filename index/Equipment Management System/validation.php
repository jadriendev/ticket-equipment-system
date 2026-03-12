<?php
    require_once "config.php";
    include("session-checker.php");

    if (isset($_GET['asset_number']) && !empty(trim($_GET['asset_number']))) {
        $sql = "SELECT * FROM tblequipment WHERE asset_number = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $_GET['asset_number']);
            if (mysqli_stmt_execute($stmt)) {
                $result = mysqli_stmt_get_result($stmt);
                $account = mysqli_fetch_array($result, MYSQLI_ASSOC);
            }
        }
    }

    if (!$account) {
        header("Location: equipment-management.php");
        exit();
    }

    function validateYear(){
        $errorYear = 0;
        if(empty($_POST['txtyear'])) {
            echo "<script>
                    alert('Year should not be empty.');  
                  </script>";
            $errorYear++;
        }
        elseif (!is_numeric($_POST['txtyear'])) {
            echo "<script>
                    alert('Year must be a number.');  
                  </script>";
            $errorYear++;
        }
        elseif (strlen(trim($_POST['txtyear'])) != 4) {
            echo "<script>
                    alert('Year must be 4 digits.');  
                  </script>";
            $errorYear++;
        }
        return $errorYear;
    }

    function validateUniqueness($link, $currentAsset){
        $errorUnique = 0;
        $sql = "SELECT asset_number, serial_number FROM tblequipment WHERE (asset_number = ? OR serial_number = ?) AND asset_number != ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "sss", $_POST['txtasset'], $_POST['txtserial'], $currentAsset);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                if ($row['asset_number'] == $_POST['txtasset']) {
                    echo "<script>
                            alert('Assets number is already use.');  
                          </script>";
                    $errorUnique++;
                }
                if ($row['serial_number'] == $_POST['txtserial']) {
                    echo "<script>
                            alert('Serial number is already use.');  
                          </script>";
                    $errorUnique++;
                }
            }
        }
        return $errorUnique;
    }

    if (isset($_POST['btnsubmit'])) 
    {
        $errors = 0;
        $errors += validateYear();
        $errors += validateUniqueness($link, $_GET['asset_number']);

        if($errors == 0)
        {
            $sql = "UPDATE tblequipment SET serial_number = ?, type = ?, manufacturer = ?, year_model = ?, description = ?, branch = ?, department = ?, status = ?, asset_number = ? WHERE asset_number = ?";
            if ($stmt = mysqli_prepare($link, $sql)) 
            {
                mysqli_stmt_bind_param($stmt, "ssssssssss", $_POST['txtserial'], $_POST['cmbtype'], $_POST['txtmanu'], $_POST['txtyear'], $_POST['txtdesc'], $_POST['cmbbranches'], $_POST['cmbdepartment'], $_POST['rbstatus'], $_POST['txtasset'], $_GET['asset_number']);
                
                if (mysqli_stmt_execute($stmt)) 
                {
                    $sql = "INSERT INTO tbllogs(datelog, timelog, action, module, performedby, performedto) VALUES (?, ?, ?, ?, ?, ?)";
                    if ($stmt = mysqli_prepare($link, $sql)) {
                        $date = date("d/m/Y");
                        $time = date("h:i:sa");
                        $action = "Update Equipment";
                        $performedby = $_SESSION['username'];
                        $performedto = $_POST['txtasset'];
                        mysqli_stmt_bind_param($stmt, "ssssss", $date, $time, $action, $module, $performedby, $performedto);
                    }

                    $_SESSION['toast'] = "Equipment updated successfully.";
                    header("Location: equipment-management.php");
                    exit();
                }
            }
        }
        else {
            $account['asset_number'] = $_POST['txtasset'];
            $account['serial_number'] = $_POST['txtserial'];
            $account['type'] = $_POST['cmbtype'];
            $account['manufacturer'] = $_POST['txtmanu'];
            $account['year_model'] = $_POST['txtyear'];
            $account['description'] = $_POST['txtdesc'];
            $account['branch'] = $_POST['cmbbranches'];
            $account['department'] = $_POST['cmbdepartment'];
            $account['status'] = $_POST['rbstatus'];
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
    <title>Update Equipment Page - Equipment Management System</title>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-6">
    <main class="w-full max-w-4xl">
        <form class="bg-white rounded-xl shadow-lg p-8 space-y-6"
              action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>"
              method="POST">

            <!-- Header -->
            <div class="flex flex-col items-center gap-2">
                <img src="https://www.arellano.edu.ph/sites/default/files/inline-images/new-au-logo.png"
                     class="w-36">
                <p class="font-bold text-lg tracking-wide text-center">
                    Update Equipment
                </p>
                <p class="text-sm text-gray-600 text-center">
                    Change the values and submit to update the equipment.
                </p>
            </div>

            <hr class="border-gray-300">

            <!-- Form Content -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                <!-- Asset Number -->
                <div>
                    <label class="font-semibold text-sm">Asset Number</label>
                    <div class="relative mt-1">
                        <i class="fa-solid fa-hashtag absolute left-3 top-1/2 -translate-y-1/2 text-gray-500"></i>
                        <input class="w-full border rounded-lg p-2 pl-10 text-sm"
                               type="text" name="txtasset"
                               value="<?php echo $account['asset_number']; ?>" required>
                    </div>
                </div>

                <!-- Serial Number -->
                <div>
                    <label class="font-semibold text-sm">Serial Number</label>
                    <div class="relative mt-1">
                        <i class="fa-solid fa-hashtag absolute left-3 top-1/2 -translate-y-1/2 text-gray-500"></i>
                        <input class="w-full border rounded-lg p-2 pl-10 text-sm"
                               type="text" name="txtserial"
                               value="<?php echo $account['serial_number']; ?>" required>
                    </div>
                </div>

                <!-- Equipment Type -->
                <div>
                    <label class="font-semibold text-sm">Equipment Type</label>
                    <select class="w-full border rounded-lg p-2 text-sm mt-1"
                            name="cmbtype" required>
                        <!-- options untouched -->
                    </select>
                </div>

                <!-- Manufacturer -->
                <div>
                    <label class="font-semibold text-sm">Manufacturer</label>
                    <input class="w-full border rounded-lg p-2 text-sm mt-1"
                           type="text" name="txtmanu"
                           value="<?php echo $account['manufacturer']; ?>" required>
                </div>

                <!-- Year -->
                <div>
                    <label class="font-semibold text-sm">Year Model</label>
                    <input class="w-full border rounded-lg p-2 text-sm mt-1"
                           type="text" name="txtyear"
                           value="<?php echo $account['year_model']; ?>" required>
                </div>

                <!-- Branch -->
                <div>
                    <label class="font-semibold text-sm">Branch</label>
                    <select class="w-full border rounded-lg p-2 text-sm mt-1"
                            name="cmbbranches" required>

                        <option value="">--Select Branches--</option>
                        <option value="JUAN SUMULONG CAMPUS" <?php if($account['branch'] == "JUAN SUMULONG CAMPUS") echo "selected"; ?>>Juan Sumulong Campus</option>
                        <option value="JOSE ABAD SANTOS CAMPUS" <?php if($account['branch'] == "JOSE ABAD SANTOS CAMPUS") echo "selected"; ?>>Jose Abad Santos Campus</option>
                        <option value="APOLINARIO MABINI CAMPUS" <?php if($account['branch'] == "APOLINARIO MABINI CAMPUS") echo "selected"; ?>>Apolinario Mabini Campus</option>
                        <option value="ANDRES BONIFACIO CAMPUS" <?php if($account['branch'] == "ANDRES BONIFACIO CAMPUS") echo "selected"; ?>>Andres Bonifacio Campus</option>
                        <option value="ELISA ESGUERRA CAMPUS" <?php if($account['branch'] == "ELISA ESGUERRA CAMPUS") echo "selected"; ?>>Elisa Esguerra Campus</option>
                        <option value="JOSE RIZAL CAMPUS" <?php if($account['branch'] == "JOSE RIZAL CAMPUS") echo "selected"; ?>>Jose Rizal Campus</option>
                        <option value="PLARIDEL CAMPUS" <?php if($account['branch'] == "PLARIDEL CAMPUS") echo "selected"; ?>>Plaridel Campus</option>

                    </select>
                </div>


                <!-- Department -->
                <div class="md:col-span-2">
                    <label class="font-semibold text-sm">Department</label>
                    <select class="w-full border rounded-lg p-2 text-sm mt-1"
                            name="cmbdepartment" required>

                        <option value="">--Select Department--</option>
                        <option value="COLLEGE OF ARTS AND SCIENCE" <?php if($account['department'] == "COLLEGE OF ARTS AND SCIENCE") echo "selected"; ?>>College of Arts and Science</option>
                        <option value="SCHOOL OF BUSINESS AND ADMINISTRATION" <?php if($account['department'] == "SCHOOL OF BUSINESS AND ADMINISTRATION") echo "selected"; ?>>School of Business and Administration</option>
                        <option value="SCHOOL OF BUSINESS AND COMMERCE" <?php if($account['department'] == "SCHOOL OF BUSINESS AND COMMERCE") echo "selected"; ?>>School of Business and Commerce</option>
                        <option value="SCHOOL OF EDUCATION" <?php if($account['department'] == "SCHOOL OF EDUCATION") echo "selected"; ?>>School of Education</option>
                        <option value="SCHOOL OF COMPUTER SCIENCE" <?php if($account['department'] == "SCHOOL OF COMPUTER SCIENCE") echo "selected"; ?>>School of Computer Science</option>
                        <option value="SCHOOL OF HOSPITALITY AND TOURISM MANAGEMENT" <?php if($account['department'] == "SCHOOL OF HOSPITALITY AND TOURISM MANAGEMENT") echo "selected"; ?>>School of Hospitality and Tourism Management</option>
                        <option value="COLLEGE OF CRIMINAL JUSTICE EDUCATION" <?php if($account['department'] == "COLLEGE OF CRIMINAL JUSTICE EDUCATION") echo "selected"; ?>>College of Criminal Justice Education</option>
                        <option value="INSTITUTE OF ACCOUNTANCY" <?php if($account['department'] == "INSTITUTE OF ACCOUNTANCY") echo "selected"; ?>>Institute of Accountancy</option>
                        <option value="ALLIED MEDICAL/HEALTH SCIENCES PROGRAMS" <?php if($account['department'] == "ALLIED MEDICAL/HEALTH SCIENCES PROGRAMS") echo "selected"; ?>>Allied Medical/Health Science Programs</option>
                        <option value="ARELLANO UNIVERSITY SCHOOL OF LAW" <?php if($account['department'] == "ARELLANO UNIVERSITY SCHOOL OF LAW") echo "selected"; ?>>Arellano University School of LAW</option>

                    </select>
                </div>


                <!-- Description -->
                <div class="md:col-span-2">
                    <label class="font-semibold text-sm">Description</label>
                    <textarea class="w-full border rounded-lg p-2 text-sm mt-1"
                              rows="3"
                              name="txtdesc"><?php echo $account['description']; ?></textarea>
                </div>

                <!-- Status -->
                <div class="md:col-span-2">
                    <label class="font-semibold text-sm block mb-2">Status</label>

                    <?php
                        $status = $account['status'];
                        if ($status == 'WORKING')  
                        {
                    ?>
                        <div class="flex gap-6">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="rbstatus" value="WORKING" checked class="accent-purple-600 w-4 h-4">
                                <span class="text-sm">Working</span>
                            </label>

                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="rbstatus" value="ON-REPAIR" class="accent-purple-600 w-4 h-4">
                                <span class="text-sm">On-Repair</span>
                            </label>

                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="rbstatus" value="RETIRED" class="accent-purple-600 w-4 h-4">
                                <span class="text-sm">Retired</span>
                            </label>
                        </div>
                    <?php
                        }
                        else {
                    ?>
                        <div class="flex gap-6">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="rbstatus" value="WORKING" checked class="accent-purple-600 w-4 h-4">
                                <span class="text-sm">Working</span>
                            </label>

                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="rbstatus" value="ON-REPAIR" class="accent-purple-600 w-4 h-4">
                                <span class="text-sm">On-Repair</span>
                            </label>

                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="rbstatus" value="RETIRED" class="accent-purple-600 w-4 h-4">
                                <span class="text-sm">Retired</span>
                            </label>
                        </div>
                    <?php
                        }
                    ?>
                </div>


            <!-- Buttons -->
            <div class="flex justify-end gap-3 pt-4">
                <a href="equipment-management.php"
                   class="px-5 py-2 rounded-lg border border-gray-400 text-gray-700 hover:bg-gray-100">
                    Cancel
                </a>
                <input type="submit" name="btnsubmit"
                       class="px-5 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 cursor-pointer">
            </div>

        </form>
    </main>
</body>

</html>




<body class="flex flex-col items-center justify-center bg-gray-100">
    <main class="flex flex-col items-center justify-center min-h-screen max-w-md w-full">
        <form class="bg-white flex flex-col px-7 py-10 gap-2 rounded-lg shadow-lg w-full" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
            <div class="flex items-center justify-center">
                <img src="https://www.arellano.edu.ph/sites/default/files/inline-images/new-au-logo.png" width="200px">
            </div>
            <hr class="border-t-1 border-gray-300 w-full my-3">
            <p class="font-bold text-lg tracking-wide text-center">Fill up this form and submit to add a new equipment.</p>
            
            <label class="font-semibold text-md tracking-wide" for="txtasset">Asset Number:</label>
            <div class="relative w-full">
                <i class="fa-solid fa-user text-md absolute left-3 top-1/2 -translate-y-1/2"></i>
                <input class="border border-2 border-black rounded-lg p-2 pl-10 rounded w-full text-sm w-full" type="text" name="txtasset" required>
            </div>
            
            <label class="font-semibold text-md tracking-wide" for="txtserial">Serial Number:</label>
            <div class="relative w-full mb-3">
                <i class="fa-solid fa-lock text-md absolute left-3 top-1/2 -translate-y-1/2"></i>
                <input class="border border-2 border-black rounded-lg p-2 pl-10 rounded w-full text-sm w-full" type="text" name="txtserial" required>
            </div>
            
            <label class="font-semibold text-md tracking-wide" for="cmbtype">Equipment Type:</label>
            <select class="border border-2 border-black rounded-lg p-2 rounded w-full text-sm w-full" name="cmbtype" id="cmbtype" required>
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

            <label class="font-semibold text-md tracking-wide" for="txtmanu">Manufacturer:</label>
            <div class="relative w-full">
                <input class="border border-2 border-black rounded-lg p-2 pl-2 rounded w-full text-sm w-full" type="text" name="txtmanu" required>
            </div>

            <label class="font-semibold text-md tracking-wide" for="txtyear">Year:</label>
                <div class="relative w-full">
                    <input class="border border-2 border-black rounded-lg p-2 pl-2 rounded w-full text-sm w-full" type="text" name="txtyear" required>
                </div>

            <label class="font-semibold text-md tracking-wide" for="txtdesc">Description:</label>
                <div class="relative w-full">
                    <textarea class="border border-2 border-black rounded-lg p-2 pl-2 rounded w-full text-sm w-full" name="txtdesc" required></textarea>
                </div>

            <label class="font-semibold text-md tracking-wide" for="cmbbranches">Branches:</label>
            <select class="border border-2 border-black rounded-lg p-2 rounded w-full text-sm w-full" name="cmbbranches" id="cmbbranches" required>
                <option value="">--Select Branches--</option>
                <option value="JUAN SUMULONG CAMPUS">Juan Sumulong Campus</option>
                <option value="JOSE ABAD SANTOS CAMPUS">Jose Abad Santos Campus</option>
                <option value="APOLINARIO MABINI CAMPUS">Apolinario Mabini Campus</option>
                <option value="ANDRES BONIFACIO CAMPUS">Andres Bonifacio Campus</option>
                <option value="ELISA ESGUERRA CAMPUS">Elisa Esguerra Campus</option>
                <option value="JOSE RIZAL CAMPUS">Jose Rizal Campus</option>
                <option value="PLARIDEL CAMPUS">Plaridel Campus</option>
            </select>
            
            <label class="font-semibold text-md tracking-wide" for="cmbdepartment">Department:</label>
            <select class="border border-2 border-black rounded-lg p-2 rounded w-full text-sm w-full" name="cmbdepartment" required>
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

            <br>
            <div class="flex items-center justify-center gap-3 w-full">
                <a class="w-1/2 bg-blue-600 p-2 text-white rounded-lg transition transform-transition duration-200 hover:bg-blue-700 cursor-pointer text-center" href="equipment-management.php">Cancel</a>
                <input class="w-1/2 bg-blue-600 p-2 text-white rounded-lg transition transform-transition duration-200 hover:bg-blue-700 cursor-pointer text-center" type="submit" name="btnsubmit" value="Submit">
            </div>
        </form>
    </main>
</body>