<?php 
require_once '../_config.php'; 
require_once '../_include.php'; 

if(!$security->isAdminLogin('diskUsage',10,true));
 
if(!isset($_POST) || empty($_POST['action']))  die;
if(empty($_POST['month']) || empty($_POST['year'])) die;

//start deleting....
$arrTransactionTable = array();
array_push($arrTransactionTable, $truckingServiceOrder);
array_push($arrTransactionTable, $truckingCostCashOut);
array_push($arrTransactionTable, $itemOutDepot);
array_push($arrTransactionTable, $itemInDepot);


switch ($_POST['action']){  
    case 'delete' :     // untuk transaction table
                        $arrFilePerPeriod = array();
        
                        foreach($arrTransactionTable as $table){ 
                    
                            $sql = 'select 
                                        pkey 
                                    from 
                                        '.$table->tableName.'
                                    where 
                                        month(trdate) = '.$class->oDbCon->paramString($_POST['month']).' and 
                                        year(trdate) = '.$class->oDbCon->paramString($_POST['year']);
                            
                            $rs = $class->oDbCon->doQuery($sql);
                            
                            foreach($rs as $row){
                                // compability check
                                if(isset($table->fileType)){
                                    foreach($table->fileType as $fileType){ 
                                        $urlpath  = $class->defaultDocUploadPath.$fileType['uploadFileFolder'].$row['pkey']; 
                                        if(is_dir($urlpath))
                                            array_push($arrFilePerPeriod,$urlpath);  
                                    }
                                }else{   
                                    $urlpath  = $class->defaultDocUploadPath.$table->uploadFileFolder.$row['pkey']; 
                                    if(is_dir($urlpath))
                                        array_push($arrFilePerPeriod,$urlpath);  
                                }
                            }  

                            //start deleting
                            foreach($arrFilePerPeriod as $folderPath){  
                               $class->deleteAll($folderPath);  
                            }


                        }
                    
                    break;
}

die;
  
?>