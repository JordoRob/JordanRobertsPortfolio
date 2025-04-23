<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/labour/src/global_generators.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/labour/src/session_security.php";


function importEmployeeCSV($csvFile)
{


    $data = [];
    try {
        if (($file = fopen($csvFile, "r")) !== FALSE) {
            for ($i = 0; $i < 2; $i++) {
                $row = fgetcsv($file, ",");
            }
            // Read the employee data
            while (($row = fgetcsv($file, ",")) !== FALSE) {
                $name = $row[0];
                $nameParts = explode(', ', $name);
                if (count($nameParts) == 2) {
                    $firstName = $nameParts[1];
                    $lastName = $nameParts[0];
                    $name = $firstName . ' ' . $lastName;
                }
                // make hireDate go from DD-MM-YYYY to YYYY-MM-DD
                $hireDate = $row[1] !== '' ? date("Y-m-d", strtotime($row[1])) : null;
                $phoneNum1 = $row[2];
                $phoneNum2 = $row[3];
                $email = $row[4];

                $employeeData = [
                    'name' => $name,
                    'hireDate' => $hireDate,
                    'phoneNum1' => $phoneNum1,
                    'phoneNum2' => $phoneNum2,
                    'email' => $email
                ];

                $data[] = $employeeData;
            }

            fclose($file);
        } else {
            echo 'Unable to open CSV';
        }
    } catch (Exception $e) {
        echo $e->getMessage();
    }

    return $data;
}

function displayEmployeeTable($data)
{

    require_once $_SERVER['DOCUMENT_ROOT'] . "/src/global_data_table.php";

    echo "<br>
        <h1 style='font-size: 24px;' >Employees to be imported</h1>
        <br>
        <form method='post' action='insertEmployeeCSV.php'>

          <table>
            <tr>
              <th>Name</th>
              <th>Role</th>
              <th>Active</th>
              <th>Birthday</th>
              <th>Phone Number</th>
              <th>Secondary Phone Number</th>
              <th>Email</th>
              <th>Hire Date</th>
            </tr>";

    foreach ($data as $index => $row) {
        echo "<tr>";
        echo "<td><input type='text' name='data[$index][name]' value='" . $row['name'] . "'></td>";
        echo "<td>
        <select name='data[$index][role]'>";
        foreach ($display_role as $roleIndex => $roleName) {
            $selected = ($row['role'] == $roleIndex) ? ' selected' : '';
            echo "<option value='$roleIndex' $selected>$roleName</option>";
        }
        echo "</select>
        </td>";
        echo "<td>
            <select name='data[$index][active]'>";
        foreach ($active_status as $statusIndex => $statusName) {
            $selected = ($row['active'] == $statusIndex) ? ' selected' : '';
            echo "<option value='$statusIndex' $selected>$statusName</option>";
        }
        echo "</select>
        </td>";
        echo "<td><input type='date' name='data[$index][birthday]' value='" . $row['birthday'] . "'></td>";
        echo "<td><input type='text' name='data[$index][phoneNum1]' value='" . $row['phoneNum1'] . "'></td>";
        echo "<td><input type='text' name='data[$index][phoneNum2]' value='" . $row['phoneNum2'] . "'></td>";
        echo "<td><input type='text' name='data[$index][email]' value='" . $row['email'] . "'></td>";
        echo "<td><input type='date' name='data[$index][hireDate]' value='" . $row['hireDate'] . "'></td>";
        // echo "<td>
        //                 <select name='data[$index][redseal]'>
        //                   <option value='1'" . ($row['redseal'] == 1 ? ' selected' : '') . ">Yes</option>
        //                   <option value='0'" . ($row['redseal'] == 0 ? ' selected' : '') . ">No</option>
        //                 </select>
        //               </td>";

        echo "</tr>";
    }

    echo "</table>
    <br>
    <input type='hidden' name='submit' value='Hi'>
    <input type='submit' name='go' value='Submit'>
    </form>";
}

?>

<html>

<head>
    <link rel="stylesheet" href="/src/global_styles/navbar.css">
    <link rel="stylesheet" href="/src/global_styles/styles.css">
</head>

<body>
    <?php
    echo generate_nav_bar(2, 2);

    if (isset($_FILES['employeeCSVFile']) && $_FILES['employeeCSVFile']['type'] == 'text/csv') {
        check_is_admin();
        $data = importEmployeeCSV($_FILES['employeeCSVFile']['tmp_name']);
        displayEmployeeTable($data);
    } else {
        echo "Error: Invalid form submission.";
    }
    ?>
</body>

</html>