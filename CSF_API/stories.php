<html>
<head>
<title>Dog Stories</title>
<link href="global.css" rel="stylesheet" type="text/css">
<style>
#table-container{
max-height: 75vh;
  overflow: scroll;
}.navButton a{
    color:white;
    text-decoration:none;
}.navButton:hover{
background-color: #0076a5;
}#bottomButtons{
display:flex;
flex-direction:column;
gap:10px;
    position:absolute;
    bottom:10px;
    left:1%;
}.navButton{
text-align:center;
    background-color: #00abee;
    padding:10px;
    border-radius: 10px;
}

</style>
</head>
<body>
<div class='flex-body'>
<h1>Previous Stories</h1>
<form method="get" action='submit.php' id="dogForm">
<input type="text" name="id" placeholder="Enter the ID of the survey you want to see" required><br>
<input type="submit" value="Submit">
</form>
<div id="table-container">
<table>
<tr>
<th>Survey ID</th>
<th>Username</th>
<th>Dog Photo</th>
<th>Dog Name</th>
<th>Story</th>
</tr>



<?php
// Include the common cURL file
require_once './API/curl_helper.php';
$restAPIBaseURL = 'http://localhost/CSF_API/API';
try {
    // Get all surveys
    if($_GET['id'] == null){ #if no ID is requested, show all surveys
        $allSurveys = sendRequest($restAPIBaseURL.'/api.php/surveys', 'GET');
        $response=json_decode($allSurveys, true)['response'];
        $allSurveys = json_decode(json_decode($allSurveys, true)['response'],true);
        if($response == '[]'){
            echo "<tr>";
            echo "<td colspan='5'>No surveys found</td>";
            echo "</tr>";
        }
        else{
        foreach($allSurveys as $survey){
            echo "<tr>";
            echo "<td>".$survey['id']."</td>";
            echo "<td>".$survey['username']."</td>";
            echo "<td><img class='table-image' src='".$survey['image_link']."' alt='Dog photo'></td>";
            echo "<td>".$survey['DOG_NAME']."</td>";
            echo "<td>".$survey['response']."</td>";
            echo "</tr>";}}


   }    
    else{   #if an ID is requested, show the survey with that ID
        $id = $_GET['id'];
        $employee = sendRequest($restAPIBaseURL."/api.php/surveys/$id", 'GET');
        $survey = json_decode(json_decode($employee, true)['response'],true);
        echo "<tr>";
        echo "<td>".$survey['id']."</td>";
        echo "<td>".$survey['username']."</td>";
        echo "<td><img class='table-image' src='".$survey['image_link']."' alt='Dog photo'></td>";
        echo "<td>".$survey['dog_name']."</td>";
        echo "<td>".$survey['response']."</td>";
        echo "</tr>";
    }
    
} catch (Exception $e) {
    echo 'Caught exception: ', $e->getMessage(), "\n";
};

?>
</table>
</div>
</div>
<div id='bottomButtons'>
<span class="navButton">
<a id="storyLink" href="index.php">New Story
</a>
</span>
<span class="navButton">
<a id="storyLink" href="../index.html">Return Home
</a>
</span>
</html>