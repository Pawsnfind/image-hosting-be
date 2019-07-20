 <?php

include './helper.php';

include './cors.php';

$config = parse_ini_file("../config.ini");

$server     = $config['server'];
$username   = $config['db_user'];
$password   = $config['db_password'];
$database   = $config['database'];

$user_id = NULL;

if (!is_dir("images"))
    mkdir("images");

if (!$_POST['api_key'])
{
    echo "No token";
    error(400, "No token");
    return;
}
 
if (ValidToken($_POST['api_key'])) 
{ 
    $image_dir = "images/";

    if (!$_POST['shelter_id'])
    {
        echo "No shelter_id";
        error(400, "No shelter_id");
        return;
    }

    if (!$_POST['animal_id'])
    {
        echo "No animal_id";
        error(400, "No animal_id");
        return;
    }

    if (!is_dir($image_dir . $_POST['shelter_id'])) 
    {
        mkdir($image_dir . $_POST['shelter_id']);
    }

    if (!is_dir($image_dir . $_POST['shelter_id'] . '/'  . $_POST['animal_id'])) 
    {
        mkdir($image_dir . $_POST['shelter_id'] . '/' . $_POST['animal_id']);
    }

    $shelterID = $_POST['shelter_id'];
    $animalID = $_POST['animal_id'];

    $date = new DateTime();

    $filename = strval($date->getTimeStamp()) . strtolower(basename($_FILES["image"]["name"]));
    $fileExt  = pathinfo($filename, PATHINFO_EXTENSION);

    $target_file = $image_dir . $shelterID . '/' . $animalID . '/' . hash("sha256", $_POST['token'] . $filename) . '.' . $fileExt;

    $imagePath = $target_file;
 
    if (file_exists($target_file)) 
    {
        error(400, 'Image already exists.  Try changing the image name');
        return;
    }
 
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) 
    {
        $imagePath = addslashes($imagePath);

        $message = array
        (
            'url' =>  $imagePath
        );

        http_response_code(200);
        echo json_encode($message);
        return;
    } 

    error(500,  "Something went wrong");
}

function ValidToken($token)
{
    $api_key == $config['api_key'];  // Will come from post later

    if ($api_key) 
    {
        $conn = new PDO("mysql:host=$servername; dbname=$database", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = 'SELECT user.id FROM users WHERE api_key =  $api_key';

        $query = $conn->exec($sql);

        if ($query)
        {
            $user_id = $query[0];
            
            return true;
        }
        else
            return false;    
    } 
    else 
    {
        return false;
    }
}