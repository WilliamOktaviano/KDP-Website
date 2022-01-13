<?php
require_once '../../_config.php';  
require_once '../../_include-fe-v2.php';
require_once '../../_global.php';  // perlu utk obj $twig utk kirim email

includeClass("SalesOrder.class.php");
$salesOrder = new SalesOrder();

$fileContent = file_get_contents("php://input");

$salesOrder->updateMidtransResponse($fileContent);

?>