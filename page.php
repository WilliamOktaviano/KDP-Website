<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php'; 

$pagename = (isset($_GET) && !empty($_GET['page'])) ? $_GET['page'] : ''; 
 
$rsPage = $page->searchData($page->tableName.'.pagename',$pagename,true);
$rsPage = $page->updateContentLang($rsPage);  

if(!empty($rsPage)){
    $arrTwigVar ['title'] =  $rsPage[0]['title']; 
    $arrTwigVar ['content'] =  $rsPage[0]['detail']; 
    $arrTwigVar ['pageKey'] =  $rsPage[0]['pkey']; 
    $arrTwigVar ['pageImage'] =  $rsPage[0]['file'];   
    $arrTwigVar ['ACTIVE_MENU'] = array($rsPage[0]['pagename']);  
    $arrTwigVar ['PAGE_ID'] =  $rsPage[0]['pagename'];   
}else{
    $arrTwigVar ['title'] = ''; 
    $arrTwigVar ['content'] = ''; 
    $arrTwigVar ['pageKey'] =  ''; 
    $arrTwigVar ['pageImage'] =  ''; 
    $arrTwigVar ['ACTIVE_MENU'] = ''; 
    $arrTwigVar ['PAGE_ID'] =  ''; 
}

echo $twig->render('page.html', $arrTwigVar);  
?>
