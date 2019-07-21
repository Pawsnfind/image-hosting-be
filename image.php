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
    $image_dir = "images/";

    if (!is_dir($image_dir . md5($acc_id))) 
    {
        mkdir($image_dir . md5($acc_id));
    }

    $date = new DateTime();
 
    $filename = md5(strval($date->getTimeStamp()) . strtolower(basename($_FILES["image"]["name"])));
    $fileExt  = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
  
    $target_file = $image_dir . md5($acc_id) . '/' . hash("sha256", $_POST['api_key'] . $filename) . '.' . $fileExt;

    if(!exif_imagetype($_FILES["image"]["tmp_name"])) {
      error(400, 'File uploaded is not an image');
      exit();
    }

    if (file_exists($target_file)) 
    {
        error(400, 'Image already exists.  Try changing the image name');
        return;
    }
 
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) 
    {
        $conn = new PDO("mysql:host=$servername; dbname=$database", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      
        $url = 'https://' . $_SERVER['SERVER_NAME'] . '/' . $target_file;
        $sql = "INSERT INTO images (image_url, image_path, user_id) VALUES ('$url', '$target_file', '$user_id') ";
        $results = $conn->exec($sql);
    

        $image_id = $conn->lastInsertId();
        $conn = NULL;
        $message = array
        (
            'url' =>  $url,
            'image_id' => $image_id
        );

        http_response_code(200);
        echo json_encode($message, JSON_UNESCAPED_SLASHES);
        return;
    } 

    error(500,  "Something went wrong");
    exit();
}
else{
    error(400,  "Invalid API key");
    exit();
}
  