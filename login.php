<?php 

include './helper.php';

use Auth0\SDK\JWTVerifier;

$config = parse_ini_file("../config.ini");

$server     = $config['server'];
$username   = $config['db_user'];
$password   = $config['db_password'];
$database   = $config['database'];

$auth0_domain = $config['AUTH0_DOMAIN'];
$jwks_uri = $config['JWKS_URI'];

if (empty($_POST['token']))
    error(400, 'No token');

try 
{
    $verifier = new JWTVerifier([
        'supported_algs' => ['RS256'],
        'valid_audiences' => ['YOUR_API_IDENTIFIER'],
        'authorized_iss' => $auth0_domain
    ]);
  
    $this->token = $token;
    $this->tokenInfo = $verifier->verifyAndDecode($_POST['token']);
}
catch(\Auth0\SDK\Exception\CoreException $e) 
{
    error(400, $e);
}
  


$conn = new PDO("mysql:host=$servername; dbname=$database", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$subID = $_POST['sub_id'];
$sql = "SELECT user.id FROM users WHERE sub_id =  $subID";

$query = $conn->exec($sql);