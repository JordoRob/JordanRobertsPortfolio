<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/labour/src/global_generators.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/labour/src/session_security.php";

function importJobsCSV($csvFile, $pdo)
{
    $data = [];
    try {
        if (($file = fopen($csvFile, "r")) !== FALSE) {
            //skip the header row
            fgetcsv($file);

            //read file
            $projectManager = null;
            while (($row = fgetcsv($file, ",")) !== FALSE) {
                if ($row[0] == null) {
                    continue;
                    //skip row if empty
                }
                //if the first row is the PM's name, store it in the $projectManager variable.
                else if (strpos($row[0], 'PROJECT') !== false) {
                    if (strpos($row[0], "'S") !== false) {
                        $projectManager = str_replace("'S PROJECTS", '', $row[0]);
                    } else {
                        $projectManager = str_replace(" PROJECTS", '', $row[0]);
                    }
                } else {
                    //exclude foreman phone number, keep the name only
                    preg_match('/^([^(]+)/', $row[2], $matches);
                    $foremanName = trim($matches[1]);
                    $jobData = [
                        'title' => $row[0],
                        'address' => $row[1],
                        'foreman' => $foremanName,
                        'manager' => $projectManager,
                        'startDate' => '',
                        'endDate' => ''
                    ];
                    //insert the record into $data[]
                    $data[] = $jobData;
                }
            }
            //close the file
            fclose($file);
        } else {
            echo 'Unable to open CSV';
        }
    } catch (Exception $e) {
        echo $e->getMessage();
    }
    return $data;
}



function displayEditForm($data)
{
    echo "<br>
    <h1 style='font-size: 24px;'>Jobs to be imported</h1>
    <h2 style='font-size: 14px;'>Note: please use chrome or edge, otherwise start/end date won't work</h1>
    <br>
            <form method='post' action='insertJobsCSV.php'>
              <table>
                <tr>
                  <th>Title</th>
                  <th>Address</th>
                  <th>Foreman</th>
                  <th>Manager</th>
                  <th>Start Date</th>
                  <th>End Date</th>
                </tr>";

    foreach ($data as $index => $row) {
        echo "<tr>";
        echo "<td><input type='text' name='data[$index][title]' value='" . $row['title'] . "'></td>";
        echo "<td><input type='text' name='data[$index][address]' value='" . $row['address'] . "'></td>";
        echo "<td><input type='text' name='data[$index][foreman]' value='" . $row['foreman'] . "'></td>";
        echo "<td><input type='text' name='data[$index][manager]' value='" . $row['manager'] . "'></td>";
        echo "<td><input type='month' name='data[$index][startDate]' value='" . $row['startDate'] . " 00:00:00" . "'></td>";
        echo "<td><input type='month' name='data[$index][endDate]' value='" . $row['endDate'] . " 00:00:00" . "'></td>";
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
    <link rel="stylesheet" href="/labour/src/global_styles/styles.css">
    <link rel="stylesheet" href="/labour/src/global_styles/navbar.css">
    <!-- <link rel="stylesheet" href="/labour/src/admin-view/admin_styles.css"> -->
</head>

<body>
    <?php
    echo generate_nav_bar(2, 2);

    if (isset($_FILES['jobsCSVFile']) && $_FILES['jobsCSVFile']['type'] == 'text/csv') {
        check_is_admin();
        $data = importJobsCSV($_FILES['jobsCSVFile']['tmp_name'], $pdo);
        displayEditForm($data);
    } else {
        echo "Error: Invalid form submission.";
    }
    ?>
</body>

</html>