<html>
<head>
<title>Dog Stories</title>
<link href="global.css" rel="stylesheet" type="text/css">
<style>
.navButton a{
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
<h1>Dog Stories!</h1>
<p>Come up with a fun story and name for the dog pictured below!</p>
<?php
require_once './API/curl_helper.php';
$imgUrl=sendRequest('https://dog.ceo/api/breeds/image/random', 'GET');

$imgUrl=json_decode(json_decode($imgUrl, true)['response'], true)['message'];
echo "<img class='dogImage' src='$imgUrl' alt='Random dog image'>";

?>

<form action="submit.php" method="post" id="dogForm">
<input type="text" name="username" placeholder="Enter your username" required><br>
<input type="text" name="dog_name" placeholder="Name the dog" required><br>
<textarea name="response" placeholder="Tell us your dog story" required></textarea><br>
<input type="hidden" name="image_link" value="<?php echo $imgUrl; ?>">
<input type="submit" value="Submit">
</form>

<p>Dog Stories is a simple web app that utilizes a 'Random Dog Image' API aswell as a REST API built by myself in PHP with a GET
and POST endpoint for storing and retrieving stories.</p>
</b>
</div>
<div id='bottomButtons'>
<span class="navButton">
<a id="storyLink" href="stories.php">See Other Stories
</a>
</span>
<span class="navButton">
<a id="storyLink" href="../index.html">Return Home
</a>
</span>
</div>
</html>

