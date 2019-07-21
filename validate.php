<?php 

include './auth.php';

$conn = new PDO("mysql:host=$servername; dbname=$database", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sql = "SELECT * FROM users WHERE sub_id = '$tokenInfo->sub' AND email = '$tokenInfo->email' LIMIT 1";

$query = $conn->prepare($sql);
$query->execute();
$results = $query->fetchAll();

$conn = NULL;

$api_key = NULL;

if (count($results) == 0)
{
    $conn = new PDO("mysql:host=$servername; dbname=$database", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $api_key = md5($tokenInfo->email . $tokenInfo->sub . $tokenInfo->nickname);

    $sql = "INSERT INTO users (api_key, email, sub_id) VALUES ('$api_key', '$tokenInfo->email', '$tokenInfo->sub') ";
    $results = $conn->exec($sql);
    $conn = NULL;

    $conn = new PDO("mysql:host=$servername; dbname=$database", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "SELECT * FROM users WHERE sub_id = '$tokenInfo->sub' AND email = '$tokenInfo->email' LIMIT 1";

    $query = $conn->prepare($sql);
    $query->execute();
    $results = $query->fetchAll();

    $conn = NULL;
}

response(200, $results[0][3]);