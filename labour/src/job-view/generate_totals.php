<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/labour/src/session_security.php";
security_check_and_connect();
error_reporting(E_ALL);

$generated_html = "<h1>Totals</h1>";
try {
    $currentQuery = "SELECT COUNT(distinct employee_id) as current FROM worksOn"; //Get current number of worksOn entires, no duplicates
    $currentResult = $pdo->query($currentQuery);
    $currentRow = $currentResult->fetch();

    $generated_html .= "
        <span class='total' id='totalsCurrent'>
            <p>Current</p>
            <div>" . $currentRow['current'] . "</div>
        </span>
    ";

    //sum of every outlook month
    $outlookQuery = "
        SELECT DATE_FORMAT(date,'%M %Y') AS dateFormatted, 
        SUM(count) as 'out' 
        FROM outlook JOIN job
        ON outlook.job_id=job.id
        WHERE 
            date > CURDATE() 
            AND date <= DATE_ADD(CURDATE(), INTERVAL 12 MONTH)
            AND archived IS NULL
        GROUP BY date 
        ORDER BY date
    ";
    $outlookStmt = $pdo->prepare($outlookQuery);
    $outlookStmt->execute();

    // Loop through each outlook entry if any were found
    $currDate = date('Y-m-d H:i:s', strtotime("now")); //get the current date to compare other ones against
    $currDate = new DateTime($currDate);

    // refactored this whole section since the old one would write all the outlooks from the database first, and then write the rest of the entries
    //// Loop through each month, if there's an outlook entry for the outlook, generate it, otherwise write a 0 (aka a fake entry)
    $outlookRow = $outlookStmt->fetch(PDO::FETCH_ASSOC); //get first row
    for ($available_forecast = 0; $available_forecast < 12; $available_forecast++) {

        // increment the currDate incrementer
        date_add($currDate, date_interval_create_from_date_string("1 month"));

        // The month shown above each entry
        $visualDate = $currDate->format("M");

        // real outlook date is retrieved from database, fake is the incrementor
        $realoutlookDate = $outlookRow ? strtotime($outlookRow['dateFormatted']) : NULL;
        $fakeoutlookDate = strtotime(($currDate->format("F Y")));

        // if the database outlook date is after the iterator (or we're out of outlook rows from the database), then write a 0 and go to next iteration
        if ($outlookRow == NULL || $realoutlookDate > $fakeoutlookDate) {
            $outlookNum = 0;
        } else { // else write the actual outlook entry and fetch the next row (to be used in next iteration)
            // retrieve the projection number
            $outlookNum = $outlookRow['out'];
            $outlookRow = $outlookStmt->fetch(PDO::FETCH_ASSOC);
        }
        $generated_html .= "
            <span class='total' id='totals[" . date_format($currDate, "m-Y") . "]'>
                <p>$visualDate</p>
                <div>$outlookNum</div>
            </span>
        ";
        // Create the rest of the months as 0 if they don't exist
    }

    // Return the html for the totals
    terminate($generated_html);
} catch (PDOException $e) {
    terminate($e->getMessage(), 500);
}
?>