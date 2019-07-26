<?php

$config = parse_ini_file("../config.ini");

$server     = $config['server'];
$username   = $config['db_user'];
$password   = $config['db_password'];
$database   = $config['database'];

$conn = NULL;

try 
{
    $conn = new PDO("mysql:host=$server", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "CREATE DATABASE $database";

    $conn->exec($sql);
}
catch (PDOException $e)
{
    echo 'Error creating database.' . '<br>' . $e->getMessage();
}

try
{
    $conn = new PDO("mysql:host=$servername; dbname=$database", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "CREATE TABLE users (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(128) NOT NULL,
        sub_id VARCHAR(256) NOT NULL,
        api_key VARCHAR(256) NOT NULL
    )";

    $conn->exec($sql);
}
catch (PDOException $e)
{
    echo 'Error creating users table.' . '<br>' . $e->getMessage();
}

try
{
    $sql = "CREATE TABLE images (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        image_url VARCHAR(256) NOT NULL,
        image_path VARCHAR(256) NOT NULL,
        user_id INT(6) UNSIGNED NOT NULL,
        FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)
    )";

    $conn->exec($sql);
}
catch (PDOException $e)
{
    echo 'Error creating images table.' . '<br>' . $e->getMessage();
}

$conn = NULL;

echo 'Database created';
 