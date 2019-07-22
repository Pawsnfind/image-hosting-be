<?php

include './helper.php';
include './cors.php';
 
if (!is_dir("images"))
    mkdir("images");
 
if (empty($_POST['api_key']))
{
    error(400, 'No api key');
    exit();
}

$conn = new PDO("mysql:host=$servername; dbname=$database", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$api_key = $_POST['api_key'];
$sql = "SELECT * FROM users WHERE api_key = '$api_key' LIMIT 1";

$query = $conn->prepare($sql);
$query->execute();
$results = $query->fetchAll();
$user_id = $results[0][0];
$acc_id  = $results[0][2];
$conn = NULL;

if (count($results) > 0) 
{ 
    $conn = new PDO("mysql:host=$servername; dbname=$database", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $api_key = $_POST['api_key'];
    $sql = "SELECT * FROM images WHERE user_id = '$user_id'";
     
    $query = $conn->prepare($sql);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($results) > 0)
    {
        http_response_code($code);

        $res = array
        (
            'images' =>  $results
        );

        echo json_encode($res, JSON_UNESCAPED_SLASHES);
        exit();
    }
    else
    {
        error(404, "No images found");
        exit();
    }
}
else
{
    error(400,  "Invalid API key");
    exit();
}
  
error(500,  "Something went wrong");
exit();