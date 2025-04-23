<?php

if (isset($_POST['data'])) {
    include_once $_SERVER['DOCUMENT_ROOT'] . "/labour/src/global_generators.php";
    require_once $_SERVER['DOCUMENT_ROOT'] . "/labour/src/session_security.php";

        security_check_and_connect("POST", ["data"]);
        check_is_admin();
        $data = $_POST['data'];

        echo generate_nav_bar(2, 2);
        insertJobs($data, $pdo);
        $pdo = null;
    
}

function insertJobs($data, $pdo)
{
    foreach ($data as $row) {

        $title = $row['title'];
        $address = $row['address'];
        $foremanName = $row['foreman'];
        $managerName = $row['manager'];
        // taken from addJob.php
        //if date is just year-month, add on the day (since mysql doesn't do just year-month) (this option should be chose for edge and chrome, since they use the month input type)
        //if date is year-month-day, leave as is (this option chosen for safari and firefox, since they don't support the month input type and need to use date input type)
        $startDate = $row['startDate'];
        if (strlen($row['startDate']) == 7) {
            $startDate = $row['startDate'] . "-01";
        } else if (strlen($row['startDate']) == 0) { //if no start date is chosen
            $startDate = null;
        }
        $endDate = $row['endDate'];
        if (strlen($row['endDate']) == 7) {
            $endDate = $row['endDate'] . "-01";
        } else if (strlen($row['endDate']) == 0) { //if no end date is chosen
            $endDate = null;
        }
        $today = date('Y-m-d');
        $foreman_id = null;
        $job_id = null;
        $archived = false;

        // get foreman's id based on name
        $foreman_id = getForemanId($pdo, $foremanName);


        // query to check whether there's a job with the same title
        $checkStmt = $pdo->prepare("SELECT id FROM job WHERE title = :title");
        $checkStmt->bindParam(':title', $title);
        $checkStmt->execute();
        $existingJob = $checkStmt->fetch(PDO::FETCH_ASSOC);

        // if a job with the same title already exists, skip the insertion and print a warning message
        if ($existingJob) {
            echo "A job with the same title: " . $title . " already exists, please double-check and manually add this job if needed";
            echo "<br>";
            continue; // skip the rest, start a new iteration
        } else {
            // Insert a job and store its id in $job_id

            $archived = null;

            // If title is empty, don't add that job
            if (!empty($title)) {
                if (empty($endDate) && empty($startDate)) {
                    $jobAdded = addJobC($title, $address, $archived, $managerName, NULL, NULL, $pdo);
                } else if (empty($startDate)) {
                    $jobAdded = addJobC($title, $address, $archived, $managerName, NULL, $endDate, $pdo);
                } else if (empty($endDate)) {
                    $jobAdded = addJobC($title, $address, $archived, $managerName, $startDate, NULL, $pdo);
                } else {
                    $jobAdded = addJobC($title, $address, $archived, $managerName, $startDate, $endDate, $pdo);
                }
                if ($jobAdded) {
                    $job_id = getJobIdByTitle($pdo, $title);
                    echo "Job added successfully: " . $title;
                    echo "<br>";
                }
            } else {
                $jobAdded = false;
            }

        }
    }
    echo "";

}

function getForemanId($pdo, $foremanName)
{
    try {
        $stmt = $pdo->prepare("SELECT id FROM employee WHERE name = :name");
        $stmt->bindParam(':name', $foremanName);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            return $result['id'];
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    return null;
}

function addJobC($title, $address, $archived, $manager_name, $start_date, $end_date, $pdo)
{
    try {

        // Insert a new job
        $sql = "INSERT INTO job (title, address, archived, manager_name, start_date, end_date) VALUES (:title, :address, :archived, :manager_name, :start_date, :end_date)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':title', $title);
        $stmt->bindValue(':address', $address);
        $stmt->bindValue(':archived', $archived);
        $stmt->bindValue(':manager_name', $manager_name);
        $stmt->bindValue(':start_date', $start_date);
        $stmt->bindValue(':end_date', $end_date);
        $result = $stmt->execute();

        if ($result) {
            // Insertion was successful
            return true;
        } else {
            // Error occurred during insertion
            echo "An error occurred during insertion.";
            echo "<br>";
            echo "PDO Error Info: " . json_encode($stmt->errorInfo());
            return false;
        }

    } catch (Exception $e) {
        echo $e->getMessage();
        return false;
    }
}

function getJobIdByTitle($pdo, $title)
{
    $stmt = $pdo->prepare("SELECT * FROM job WHERE title = :title");
    $stmt->bindValue(':title', $title);
    $stmt->execute();
    $job = $stmt->fetch(PDO::FETCH_ASSOC);
    return $job['id'];
}

?>

<html>

<head>
    <link rel="stylesheet" href="/labour/src/global_styles/navbar.css">
    <link rel="stylesheet" href="/labour/src/global_styles/styles.css">
</head>

<body>
    <?php

    ?>
</body>

</html>