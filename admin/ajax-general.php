<?php 

// harusnya gk perlu lg, karena sudah selalu di include di file ajax
//require_once '../_config.php'; 
//require_once '../_include-v2.php';  

if (!isset($obj)) die; 

$_GET['term'] = (isset($_GET['term'])) ? $_GET['term'] : '' ;  

// priority field value
// 1 : GET, 2 : Variable dr ajax, 3 : default value  
if (isset($_GET['searchField']) && !empty($_GET['searchField']))
    $fieldValue = explode(',',$_GET['searchField']);
else if (!isset($fieldValue))
    $fieldValue = $obj->tableName.'.name';
 
 
$returnFieldValue = (isset($_GET['returnField']) && !empty($_GET['returnField'])) ? explode(',',$_GET['returnField']) : $fieldValue; // utk jaga compability


$orderfield = (is_array($fieldValue)) ? $obj->tableName.'.'.$fieldValue[0] : $fieldValue;
$order = (isset($order)) ? $order : 'order by '. $obj->oDbCon->paramOrder($orderfield).' asc' ;  
   
// tetep set diatas
// setidaknya buat pastiin statuskey include utk semua nya, kecuali ad action yg gk mau ad statuskey, 
// mungkin tinggal reset ulang criterianya di case nya
if(!isset($arrCriteria)) $arrCriteria = array();     
$criteria = implode(' and ', $arrCriteria);   
$criteria = (!empty($criteria)) ? ' and ' . $criteria : '';

if (isset($_GET) && !empty($_GET['action'])) {
			switch ( $_GET['action']){  
                case 'searchData' :  
                    // for general "like" search  
                    $returnField = array('key' => $obj->tableName.'.pkey','value' => $returnFieldValue) ; // harusnya fieldValue dipisah dr get
                    
                    //overwrite field yg di search 
                    $searchFieldValue = (isset($_GET['searchField']) && !empty($_GET['searchField'])) ? explode(',',$_GET['searchField']) : $fieldValue;
                    $searchOptions = array('field' => $searchFieldValue,  'key' => $_GET['term']) ; // TODO: <---- cek ini juga utk sql injection
  
                    // get semua GET
                    // exclude 'action'
                    $arrCriteria = array();     
                    $getKeys = array_keys($_GET);
                    for($i=0;$i<count($getKeys);$i++){ 
                        
                        if ($getKeys[$i] == 'action' || $getKeys[$i] == 'term' ||  $getKeys[$i] == 'limit' || $getKeys[$i] == 'searchField' || $getKeys[$i] == 'returnField' || $getKeys[$i] == 'excludekey')
                            continue; 
                        
                        // ini gk boleh continue, karena isinya bisa 0
                        /*if (empty($_GET[$getKeys[$i]]) || empty($getKeys[$i]))
                            continue;*/
                        
                        if ($_GET[$getKeys[$i]] == '' || empty($getKeys[$i]))
                            continue;
   
                        $concat = '=';
                        $value = $obj->oDbCon->paramString($_GET[$getKeys[$i]]);
                         
                        //  /^\(.*\)\Z/
                        if ($getKeys[$i] == 'statuskey' && preg_match('/^\([\d,]*\)\Z/', $_GET[$getKeys[$i]]) ){ // statuskey harus format angka dan koma, untuk menghindari sql injection
                            $concat = ' in ';
                            $value = $_GET[$getKeys[$i]];
                        }
                        
                        array_push ($arrCriteria, $getKeys[$i].$concat.$value);  
                    }

                    $tempcriteria = implode(' and ', $arrCriteria);   
                    $criteria .= (!empty($tempcriteria)) ? ' and ' . $tempcriteria : '';  

					// klao exlcude pkey
					 // gk bisa. joinnya rusak
//					if(isset($_GET['excludekey']) && !empty($_GET['excludekey'])){ 
//						$excludeKey = json_decode($_GET['excludekey'],true);
//						$obj->setLog($excludeKey,true);
//						if(!empty($excludeKey))
//							$criteria .= ' or '.$obj->tableName.'.pkey in ('.$obj->oDbCon->paramString($excludeKey,',').')';
//					}
//					
//					$obj->setLog($criteria,true);
//					
                    $searchOptions['criteria'] = $criteria; 
                    
                    if(isset($_GET['limit']) && !empty($_GET['limit']) && is_numeric($_GET['limit']))
                        $order .= ' limit ' . $_GET['limit'];
                        
                    $rsData = $obj->searchDataForAutoComplete($returnField,$searchOptions,$order);
                    
                    echo json_encode($rsData); 
                    break; 
                    
                case 'getDataRowById' : 
                    if (!isset($_GET['pkey'])) die;
                    
                    $pkey = $_GET['pkey'];
                    
                    array_push ($arrCriteria, $obj->tableName.'.pkey = ' .  $obj->oDbCon->paramString($pkey));   
                    $criteria = implode(' and ', $arrCriteria);  
                    $criteria = (!empty($criteria)) ? ' and ' . $criteria : '';   

                    //pakai searchdata agar dapat narik informasi join table yg lain.
                    //jgn pake getDataRowById 
                    $rsData = $obj->searchData('','',true,$criteria); 
                    
                    echo json_encode($rsData); 
                    break; 
                    
                case 'getDetailById' : 
                    if (!isset($_GET['pkey'])) die;
                    
                    $pkey = $_GET['pkey'];
                        
                    $criteria = implode(' and ', $arrCriteria);  
                    $criteria = (!empty($criteria)) ? ' and ' . $criteria : '';   

                    //pakai searchdata agar dapat narik informasi join table yg lain. 
                    $rsData = $obj->getDetailWithRelatedInformation($pkey,$criteria);
                    
                    echo json_encode($rsData); 
                    break; 
                    
            }
}
  
  
?>