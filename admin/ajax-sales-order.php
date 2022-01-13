<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';   

includeClass(array('SalesOrder.class.php'));
$salesOrder =  createObjAndAddToCol( new SalesOrder()); 

$obj = $salesOrder;    

if (isset($_GET) && !empty($_GET['action'])) {
			switch ( $_GET['action']){  
                case 'searchData' :   
                    
                    $order = 'order by '.$obj->tableName.'.code asc'; 

                    $arrCriteria = array(); 
                    
                    // bedakan parameter kosong atau tdk pernah dikirim
                    if (isset($_GET['pkey'])){ 
                         $_GET['pkey'] = (empty($_GET['pkey'])) ? 0 : $_GET['pkey'];
                         array_push ($arrCriteria, $obj->tableName.'.pkey = ' . $obj->oDbCon->paramString($_GET['pkey']) );  
                    }
                    
                    if (isset($_GET['isfulldeliver'])){ 
                        $_GET['isfulldeliver'] = (empty($_GET['isfulldeliver'])) ? 0 : $_GET['isfulldeliver'];
                        array_push ($arrCriteria, $obj->tableName.'.isfulldeliver = '. $obj->oDbCon->paramString($_GET['isfulldeliver']));  
                    }
                      
                    if ( isset($_GET['term']) && !empty($_GET['term']) ) 
                        array_push ($arrCriteria, '('.$obj->tableCustomer.'.name like '.$obj->oDbCon->paramString('%'.$_GET['term'].'%').' or '.$obj->tableName.'.code like '.$obj->oDbCon->paramString('%'.$_GET['term'].'%').')' );  
               
                    
                    if ( isset($_GET['statuskey']) && !empty($_GET['statuskey']) ) 
                        array_push ($arrCriteria, $obj->tableName.'.statuskey in ('.$_GET['statuskey'].')' );  
                    else
                        array_push ($arrCriteria, $obj->tableName.'.statuskey = 2' );  
                    
                    $criteria = implode(' and ', $arrCriteria);  
                    $criteria = (!empty($criteria)) ? ' and ' . $criteria : ''; 
              
                    $rs = $obj->searchDataForAutoComplete('','',false,$criteria,$order );
                    for($i=0;$i<count($rs);$i++){
                        $rs[$i]['value'] = htmlspecialchars_decode($rs[$i]['value']); 
                    }
 
                    echo json_encode($rs); 
                    break;
                    
                case 'getRelatedInformation' :
                    // TODO : perlu standarisasi
                    
                    if (!isset($_GET) ||  empty($_GET['pkey']))  
                        die;  
                    
                    $sokey = (empty($_GET['pkey'])) ? 0 : $_GET['pkey'];
                    
                    $arrCriteria = array(); 
                    array_push ($arrCriteria, 'qtyinbaseunit > deliveredqtyinbaseunit' );  
                    
                    $criteria = implode(' and ', $arrCriteria);  
                    $criteria = (!empty($criteria)) ? ' and ' . $criteria : '';
                    
                     $rs = $obj->getDetailWithRelatedInformation($sokey,$criteria); 
                     echo json_encode($rs);
                    
                    break;
                    
                case 'getDataRowById' :
                    
                    if (!isset($_GET['pkey'])) die;
                    
                    $pkey = $_GET['pkey'];
                    
                    $arrCriteria = array(); 
                    array_push ($arrCriteria, $obj->tableName.'.pkey = ' .  $obj->oDbCon->paramString($pkey));   
                    $criteria = implode(' and ', $arrCriteria);  
                    $criteria = (!empty($criteria)) ? ' and ' . $criteria : '';   

                    //pakai searchdata agar dapat narik informasi join table yg lain.
                    //jgn pake getDataRowById
                    $rsData = $obj->searchData('','',true,$criteria);
                    
                    echo json_encode($rsData); 
                    break; 
                    
            }
}
 
die;
  
?>