<?php

$config = parse_ini_file("config.ini");

$servername = $config['server'];
$username   = $config['db_user'];
$password   = $config['db_password'];
$database   = $config['database'];

function error($code, $message)
{
    http_response_code($code);

    $error = array
    (
        'error' =>  $message
    );

    echo json_encode($error);
}

function response($code, $message)
{
    http_response_code($code);

    $res = array
    (
        'response' =>  $message
    );

    echo json_encode($res);
}

