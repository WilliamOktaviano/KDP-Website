<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';  

includeClass(array('ItemCategory.class.php')); 
$itemCategory = new ItemCategory(); 


$rsItemCategory= $itemCategory->searchData($itemCategory->tableName.'.statuskey',1,true,' and '.$itemCategory->tableName.'.isshow = 1'); 
for($j=0;$j<count($rsItemCategory);$j++){   
    if( !empty($rsItemCategory[$j]['file'])) 
        $rsItemCategory[$j]['phpThumbHash'] = getPHPThumbHash($rsItemCategory[$j]['file']);	  
}
$arrTwigVar['itemCategory']  = $rsItemCategory;


echo $twig->render('product-categories.html', $arrTwigVar);
?>