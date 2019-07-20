<?php 

include './helper.php';
include './cors.php';

require_once __DIR__ . '/vendor/autoload.php';

use Auth0\SDK\JWTVerifier;
use Auth0\SDK\Exception\InvalidTokenException;
use Auth0\SDK\Exception\CoreException;

$config = parse_ini_file("./config.ini");

$server     = $config['server'];
$username   = $config['db_user'];
$password   = $config['db_password'];
$database   = $config['database'];

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
    
    if ($tokenInfo)
    {
        response(200, $tokenInfo);
        exit();
    }
}
 catch (InvalidTokenException $e) {
    echo 'Caught: InvalidTokenException - '.$e->getMessage();
    exit();
} catch (CoreException $e) {
    echo 'Caught: CoreException - '.$e->getMessage();
    exit();
} catch (\Exception $e) {
    echo 'Caught: Exception - '.$e->getMessage();
    exit();
}
  

error(500, 'Oops, something went');
exit();

/*
$conn = new PDO("mysql:host=$servername; dbname=$database", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$subID = $_POST['sub_id'];
$sql = "SELECT user.id FROM users WHERE sub_id =  $subID";

$query = $conn->exec($sql);
*/