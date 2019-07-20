<?php

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