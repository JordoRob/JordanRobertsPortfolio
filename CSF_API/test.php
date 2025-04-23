<?php
// Include the common cURL file
require_once './API/curl_helper.php';
$restAPIBaseURL = 'http://localhost/CSF_API/API';
try {
    // Add a new survey

$data = [
        'response' => 'I love dogs',
        'username' => 'doglover',
        'dog_name' => 'Fido',
        'image_link' => 'https://www.example.com/fido.jpg'
    ];
    $result = sendRequest($restAPIBaseURL.'/api.php/surveys', 'POST', $data);
    echo $result;

    $result = sendRequest('https://dog.ceo/api/breeds/image/random', 'GET');
    echo $result;

} catch (Exception $e) {
    echo 'Caught exception: ', $e->getMessage(), "\n";
};