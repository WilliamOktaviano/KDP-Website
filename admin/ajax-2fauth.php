<?php   
require_once '../_config.php'; 
require_once '../_include-v2.php';  
require_once '../assets/vendor/autoload.php';  
$g = new \Google\Authenticator\GoogleAuthenticator();

if (isset($_GET) && !empty($_GET['action'])) {
    switch ( $_GET['action']){  
        case 'verify' :     
            if (!isset($_SESSION) || empty($_SESSION['2fAuth'])) die;

            $secret = $_SESSION['2fAuth'];
            $check_this_code = $_GET['code'];

            $rsResult = array();
            if ($g->checkCode($secret, $check_this_code)) {
                $rsResult['status'] = true;   
            } else {
                $rsResult['status'] = false;   
            }

            echo json_encode($rsResult); 
            break; 
    }
}
die; 
?>