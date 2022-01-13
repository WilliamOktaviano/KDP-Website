<?php
require_once '../../_config.php';  
require_once '../../_include-fe-v2.php';
require_once '../../_global.php';  // perlu utk obj $twig utk kirim email

$class->setLog('======= PAYMENT FINISH =============================',true);
$class->setLog('GET',true);
$class->setLog($_GET,true);
$class->setLog('POST',true);
$class->setLog($_POST,true);

?>