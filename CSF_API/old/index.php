<html>
<head>
<title>Dog Stories</title>
</head>
<body>
<h1>Dog Stories!</h1>
<?php
require_once './API/curl_helper.php';
$imgUrl=sendRequest('https://dog.ceo/api/breeds/image/random', 'GET');

$imgUrl=json_decode(json_decode($imgUrl, true)['response'], true)['message'];
echo "<img class='dogImage' src='$imgUrl' alt='Random dog image'>";

?>
<form action="submit.php" method="post">
<input type="text" name="username" placeholder="Enter your username" required><br>
<input type="text" name="dog_name" placeholder="Name the dog" required><br>
<textarea name="response" placeholder="Tell us your dog story" required></textarea><br>
<input type="hidden" name="image_link" value="<?php echo $imgUrl; ?>">
<input type="submit" value="Submit">
</form>

<a id="seeStories" href="stories.php">See Stories
</a>

</html>

