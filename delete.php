<?php

include './helper.php';
include './cors.php';


if (empty($_POST['api_key']))
{
    error(400, 'No api key');
    exit();
}

if (empty($_POST['image_id']))
{
    error(400, "No image ID");
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
$image_id = $_POST['image_id'];

$conn = NULL;

if (count($results) > 0) 
{ 
    $conn = new PDO("mysql:host=$servername; dbname=$database", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $api_key = $_POST['api_key'];

    $sql = "SELECT * FROM images WHERE user_id = '$user_id' AND id = '$image_id' LIMIT 1";
    
    $query = $conn->prepare($sql);
    $query->execute();
    $results = $query->fetchAll();
    $conn = NULL;

    if (count($results) === 0)
    {
        error(404, 'Image not found');
        exit();
    } 

    $imagePath = $results[0][2];
    
    if (file_exists($imagePath)) 
    {
        try
        {
            unlink($imagePath);
        }
        catch(Exception $e)
        {
            error(500, 'There was an error deleting the image');
            exit();
        }

        $conn = new PDO("mysql:host=$servername; dbname=$database", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $api_key = $_POST['api_key'];
    
        $sql = "DELETE FROM images WHERE user_id = '$user_id' AND id = '$image_id'";
        
        $query = $conn->prepare($sql);
        $query->execute();
        $conn = NULL;

        response(200, 'Image deleted');
        exit();
    }
    else
    {
        error(404, 'Image not found on server');
        exit();
    }

    error(500,  "Something went wrong");
    exit();
}
else{
    error(400,  "Invalid API key");
    exit();
}
  