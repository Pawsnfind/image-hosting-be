<?php

include './helper.php';
include './cors.php';

require_once __DIR__ . '/vendor/autoload.php';

use Auth0\SDK\JWTVerifier;
use Auth0\SDK\Exception\InvalidTokenException;
use Auth0\SDK\Exception\CoreException;

$valid_audience = $config['AUTH0_CLIENT_ID'];
$authorized_iss = $config['AUTH0_DOMAIN'];
 
$token = NULL;
$tokenInfo = NULL;

$authHeaders = getallheaders();
 
if (empty($authHeaders))
{
    error(401, 'No token');
    exit();
}

try 
{
    $config = [
        'supported_algs' => [ 'RS256' ],
        'valid_audiences' => [$valid_audience],
        'authorized_iss' => [$authorized_iss]
    ];

    $verifier = new JWTVerifier($config);
    $authorizationHeader = str_replace('bearer ', '', $authHeaders['Authorization']);
    $token =  str_replace('Bearer ', '', $authorizationHeader);
    $tokenInfo = $verifier->verifyAndDecode($token);
}
catch (Exception $e) 
{
    error(500, 'Oops, something went');
    exit();
}

if (!$tokenInfo)
    error(401, 'Unauthorized');
