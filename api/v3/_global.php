<?php 

// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
//header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');

function endForInvalidLoginError($errMsg = ''){ 
    global $class;
    $RETURN_VALUE = array();
    $RETURN_VALUE['response_code']  = 401;  
    $RETURN_VALUE['message'] = $errMsg;
    http_response_code($RETURN_VALUE['response_code']); 
    echo json_encode($RETURN_VALUE); 
    die;   
}

function endForInvalidTokenError(){ 
    global $class;
    $RETURN_VALUE = array();
    $RETURN_VALUE['response_code']  = 401;  
    $RETURN_VALUE['message'] = $class->errorMsg[601];
    http_response_code($RETURN_VALUE['response_code']); 
    echo json_encode($RETURN_VALUE); 
    die;   
}

function endForDataNotFoundError(){ 
    global $class;
    $RETURN_VALUE = array();
    $RETURN_VALUE['response_code'] = 400;
    $RETURN_VALUE['message'] = $class->errorMsg[213];
    http_response_code($RETURN_VALUE['response_code']); 
    echo json_encode($RETURN_VALUE); 
    die;   
}

function endForRequestMethodError(){ 
    global $class;
    $RETURN_VALUE = array();
    $RETURN_VALUE['response_code'] = 400;
    $RETURN_VALUE['message'] = $class->errorMsg[213];
    http_response_code($RETURN_VALUE['response_code']); 
    echo json_encode($RETURN_VALUE); 
    die;   
}

function addFailedRows(&$arr,$row){
    array_push($arr,$row);
}

function getResponseValue($result,$showReturnData = false){
     
    $valid = true;  
    $errorMessage = array(); 
    $successMessage = array(); 
 
    foreach($result as $row){ 
        $row['message'] = strip_tags($row['message']);
        if(!$row['valid']){  
            $valid = false;
            array_push($errorMessage, $row['message']); 
        }else{
            array_push($successMessage, $row['message']); 
        }
    }

    $RETURN_VALUE = array();
    if(!empty($errorMessage)){ 
        $RETURN_VALUE['response_code'] = 409;
        $RETURN_VALUE['message'] = $errorMessage;
    }else{ 
        $RETURN_VALUE['response_code'] = 200;
        $RETURN_VALUE['message'] = $successMessage;
        if($showReturnData)
            $RETURN_VALUE['data'] = $result[0]['data'];
            
    } 

    return array(
                    'returnValue' => $RETURN_VALUE,
                    'successMessage' => $successMessage,
                    'errorMessage' => $errorMessage,
                );
}

function getAPIValue($postVars,$row, $dataset, &$errorMessage){ 
    global $class;

    $paramName = $row['paramName']; 
    $value =  $postVars[$paramName];
    
    // validasi field2 yg wajib diisi
    if ( isset($row['mandatory']) && $row['mandatory'] && empty($value)){  
        array_push($errorMessage, $paramName.'. ' . $class->errorMsg[603]);
        return '';
    }
 
    // kalo ada error return saja
    // if (!empty($errorMessage)) return '';
     
    //kalo value kosong langsung return saja
    if (empty($value)) return $value;
   
    $format = '';
      
    if(isset($dataset[1])){   
        if(!is_array($dataset[1])){
             $format = (isset($dataset[1])) ? $dataset[1] : ''; 
        }else{ 
             $format = (isset($dataset[1]['datatype'])) ? $dataset[1]['datatype'] : ''; 
        }
    } 
      
    $value = formatAPIValue($value, $format);
    
    if(isset($row['ref']) && !empty($row['ref'])){
         
        $tempName = $value;
        
        // kalo ad obj,
        if (isset($row['ref']['obj'])  && !empty($row['ref']['obj'])){
            $refObj = $row['ref']['obj']; 
            $refField = (isset($row['ref']['field']) && !empty($row['ref']['field'])) ?  $refObj->tableName.'.'.$row['ref']['field'] : $refObj->tableName.'.name';
               
            $rs = $refObj->searchDataRow( array( $refObj->tableName.'.pkey') , 
                                                '   and '.$refField .'='.$refObj->oDbCon->paramString($value)  
                                                ); 
            
            // kalo gk ketemu dan autoadd 
          
            if(empty($rs) && isset($row['ref']['autoAdd']) && $row['ref']['autoAdd']){ 
                
                 try{ 
                    if(!$refObj->oDbCon->startTrans())
                        throw new Exception($refObj->errorMsg[100]);
 
                        $defaultValue = $row['ref']['defaultValue'];

                        // sementara baru buat consignee
                        $newParam = array(); 
                        $newParam['name'] = $value;  
                        $newParam = array_merge($newParam, $defaultValue); 
                        $result = $refObj->addData($newParam);
  

                      if(!$result[0]['valid'])
                        throw new Exception( $result[0]['message'] );

                    $rs = array();
                    $rs[0] = $result[0]['data'];  

                      $refObj->oDbCon->endTrans();
                }catch(Exception $e){
                    $refObj->oDbCon->rollback(); 
                }	
 

            }
            
            //$refObj->setTimeLog("done",true);
            
        }else if (isset($row['ref']['tableName'])  && !empty($row['ref']['tableName'])){
            $tableName = $row['ref']['tableName']; 
            $refField = (isset($row['ref']['field']) && !empty($row['ref']['field'])) ? $tableName.'.'.$row['ref']['field'] : $tableName.'.name';
                
            $sql = 'select pkey from '.$tableName.' where '.$refField.' = ' . $class->oDbCon->paramString($value);
            $rs = $class->oDbCon->doQuery($sql); 
        }else if (isset($row['ref']['dataset'])  && !empty($row['ref']['dataset'])){
            $dataset = $row['ref']['dataset']; 
            $rs[0]['pkey'] = $dataset[strtolower($value)]; 
        }
        
        $value = (!empty($rs)) ? $rs[0]['pkey'] : '';    
         
        // validasi field2 yg wajib diisi, utk yg ref
        if ( isset($row['mandatory']) && $row['mandatory'] && empty($value)) 
            array_push($errorMessage, $tempName.'. ' . $class->errorMsg[213]); 
         
    } 
    
    return $value;
}

function getDetailsAPIParam($ACTION, &$PARAM,$postVars,$apiFields, $currAPIFieldsRow = array(), &$errorMessage){ 
    global $OBJ;
    $DELETE_KEYWORD = '_delete';
          
    if(!empty($currAPIFieldsRow['dataset'])){
        $willDeletedIndex = $DELETE_KEYWORD.'-'.$currAPIFieldsRow['dataset']['pkey'][0];  
        // Kalo _deleted, gk perlu proses lg
         if (isset($postVars[$DELETE_KEYWORD]) && $postVars[$DELETE_KEYWORD] == 1 ){
               if(!isset($PARAM[$willDeletedIndex])) $PARAM[$willDeletedIndex] = array(); 
                  array_push($PARAM[$willDeletedIndex], $postVars['key']);  

           return;   
         } 
    }
    
    // kalo edit, perlu disamakan jg struktur arraynya..

                     
    if(!isset($apiFields[$DELETE_KEYWORD])) $apiFields[$DELETE_KEYWORD] = array('paramName' => $DELETE_KEYWORD);
    
    // variable dibutuhkan di _global
    $arrExcludeField = array('createdby', 'client_id', 'timestamp');
    
    $PARAM['_mnv-api'] = 1; 
    
    
    foreach($apiFields as $key=>$row){
        $paramName = $row['paramName'];  
        
        //$OBJ->setLog($paramName . ' => '. $postVars[$paramName],true);
        
        $values  = (isset($postVars[$paramName]) && !empty($postVars[$paramName]))  ? $postVars[$paramName] : array(); 
        
        // khusus pkey, kalo PUT jgn dinolin. utk POST nanti review lg
        if($key == 'pkey' && !isset($postVars[$paramName]) && $ACTION == 'POST') 
            $postVars[$paramName] = 0;
              
        // kalo api mandatory tp kosong 
        // kalo dr PUT, gk perlu validasi
        if (!isset($postVars[$paramName]) && isset($row['mandatory']) && $row['mandatory'] && $ACTION != 'PUT')   
            array_push($errorMessage, $paramName.'. ' . $OBJ->errorMsg[603]);  
        
        // kalo hidSaveAndProceed 
        if($key == 'auto_proceed' && $values == 1){ 
            $PARAM['hidSaveAndProceed'] = 1;
            continue;
        }
            
        
        if(!isset($postVars[$paramName]) || ( isset($postVars['updatable']) && !$postVars['updatable'] ) ) continue; 
        
        if(in_array($key,$arrExcludeField)) continue;
        
         if (isset($row['dataset'])){  
            
            if(empty($currAPIFieldsRow))
                 $PARAM['hidTotalRows'][0][0] = count($postVars[$paramName]);
            else
                 $PARAM['hidDetailItemKeyTotalRows'][1][ count($PARAM['hidDetailItemKeyTotalRows'][1]) ] = count($postVars[$paramName]);
                
            foreach($values as $valueRow)
                getDetailsAPIParam($ACTION, $PARAM,$valueRow,$row['detail'], $row ,$errorMessage);
          
        }else{ 
             
            // untuk data header
            if(empty($currAPIFieldsRow)){ 
                $value = getAPIValue($postVars,$row, $OBJ->arrData[$key], $errorMessage);  
               
                // kalo mau kirim data, tp gk terdaftar di structure data class
                if(isset($row['forceParam']) && $row['forceParam'])
                    $PARAM[$key] = $value;
                else
                    $PARAM[$OBJ->arrData[$key][0]] = $value;
                
            }else{ 
                  
                $dataset = $currAPIFieldsRow['dataset']; 
                $elName =  isset($dataset[$key][0]) ? $dataset[$key][0] : ''; 
                $elName = (isset($row['forceParam']) && $row['forceParam']) ? $key : $elName;
                
                if (!isset($PARAM[$elName])) 
                   $PARAM[$elName] = array();
                
                // kalo POST baru di nolkan
                $value = ($key == 'pkey' && $ACTION == 'POST') ? 0 : getAPIValue($postVars, $row, $dataset[$key], $errorMessage);  
                
                array_push($PARAM[$elName], $value);
                   
            }
        }
        
    } 
 
}
 
function formatAPIValue($value, $format){
    global $class;
       
    $value = (is_array($value)) ? $value : trim($value);
    
    // jenis format diambil dr CLASS
    // kalo string dikirim
    switch(strtolower($format)){
        case 'date' : $value = date("d / m / Y",$value);
                        break;
        case 'datetime' : $value = date("d / m / Y H:i",$value);
                        break;
        case 'number' : $value = $class->unformatNumber($value);
                        break;
        default : break;    
    }
    
    return $value;
}

function updatingDetailKeyAndPostVars($OBJ,$postRow,$API_FIELDS){
    // isi semua pkey detail
     
    $tableName = (isset($API_FIELDS['tableName'])) ? $API_FIELDS['tableName'] :  $OBJ->tableNameDetail;
    
    //$OBJ->setLog($API_LEVEL_INDEX,true);
    //$OBJ->setLog($postRow,true);
    
    foreach($postRow as $key=>$row){
        if(isset($row['request_id'])){  
            
            // ini perlu tambahin validasi refkey nya agar gk nyasar ke transaksi lain
            // tp gk perlu, karena pas edit kalo pkeyny agk ketemu gagal pasti
            $sql = 'select pkey from '.$tableName.' where requestid = ' . $OBJ->oDbCon->paramString($row['request_id']);
            //$OBJ->setLog($sql,true);
            $rs =  $OBJ->oDbCon->doQuery($sql);
            //$OBJ->setLog($rs[0]['pkey'],true);
            $postRow[$key]['key'] = (!empty($rs)) ? $rs[0]['pkey'] : 0;
            //unset($postRow[$key]['request_id']); // gk boleh unset, karena kalo add new row perlu
        }
        
    }
     
    return $postRow;
}
 
function updatingPkeyAndPostVars($OBJ,&$arrPostVars,$API_FIELDS){
    
    // update key header
    
    $rsCol = array();
    
    //split dulu mana yg by code, mana yg by pkey, mana yg by request id
    // isi ulang pkeynya utk setiap request
    $arrSelectField = array($OBJ->tableName.'.pkey',$OBJ->tableName.'.code',$OBJ->tableName.'.requestid',$OBJ->tableName.'.modifiedon');


    // PKEY
    $pkey = array_column($arrPostVars, 'key'); 
    if(!empty(($pkey))) {
     $rs = $OBJ->searchDataRow($arrSelectField,  ' and ' .$OBJ->tableName.'.pkey in ('.$OBJ->oDbCon->paramString($pkey,',').')' );  
         
     $rsByRequestKey = array_column($rs,null,'pkey');
     foreach($arrPostVars as $key => $row) {
       //$arrPostVars[$key]['key'] = $rsByRequestKey[$row['key']]['pkey']; 
       $arrPostVars[$key]['code'] = $rsByRequestKey[$row['key']]['code'];   
     } 
        
     $rsCol += array_column($rs,null,'pkey');
    } 

    // REQUEST ID
    $requestid = array_column($arrPostVars, 'request_id'); 
    if(!empty(($requestid))) {
     $rs = $OBJ->searchDataRow($arrSelectField,  ' and ' .$OBJ->tableName.'.requestid in ('.$OBJ->oDbCon->paramString($requestid,',').')' );   

     $rsByRequestId = array_column($rs,null,'requestid');
     foreach($arrPostVars as $key => $row) {
       $arrPostVars[$key]['key'] = $rsByRequestId[$row['request_id']]['pkey']; 
       $arrPostVars[$key]['code'] = $rsByRequestId[$row['request_id']]['code'];   
     } 

     $rsCol += array_column($rs,null,'pkey');
    } 

    // CODE
    $code = array_column($arrPostVars, 'code'); 
    if(!empty(($code))) {
     $rs = $OBJ->searchDataRow($arrSelectField, ' and ' .$OBJ->tableName.'.code in ('.$OBJ->oDbCon->paramString($code,',').')'  );   

     $rsByCode = array_column($rs,null,'code');
     foreach($arrPostVars as $key => $row) {
       $arrPostVars[$key]['key'] = $rsByCode[$row['code']]['pkey']; 
       //$arrPostVars[$key]['code'] = $rsByCode[$row['code']]['code'];   
     } 

     $rsCol += array_column($rs,null,'pkey');
    }
    
    // update pkey setiap detail
   /* foreach($API_FIELDS as $fieldKey=>$fieldRow){ 
        if (isset($fieldRow['dataset'])) { 
           foreach($arrPostVars as $postVarKey => $row){
                if(isset($row[$fieldKey])) 
                    $arrPostVars[$postVarKey][$fieldKey] = updatingDetailKeyAndPostVars($OBJ,$row[$fieldKey],$fieldRow['detail']); 
           }
              
        }
        
    }*/
    
    // dari postvar, karena kalo dr dataset susah nentuin indexnya
    foreach($arrPostVars as $postRowKey=>$postRow){ 
        foreach($postRow as $key => $row)
            if (is_array($row)){  
                // cari informasi dr struktur api, kalo gk ketemu skip saja 
                $arrPostVars[$postRowKey][$key] = updatingDetailKeyAndPostVars($OBJ,$row,$API_FIELDS[$key]); 
            }
          
    }
    
    //$OBJ->setLog('result',true);
    //$OBJ->setLog($arrPostVars,true);
    return $rsCol;
}

$ACTION = strtoupper($_SERVER['REQUEST_METHOD']);  
$RETURN_VALUE = array();

   
// POST / PUT 
$fileContent = file_get_contents("php://input");

if($security->isJSON($fileContent))
    $postVars = json_decode($fileContent,true);
else
    parse_str($fileContent,$postVars);

// cek 1 dimensi atau bkn, paksa jd 2 dimensi
$arrPostVars = (!isset($postVars[0])) ? array($postVars) : $postVars;


// set default variable 
define('WAREHOUSE_ID', 'WRHS00001');
 
//ada 3 tipe akses
// 1. APPS 
// 2. server API
// 3. session login (import excel) , ini samakan saja dengan point no 2, pake Auth langsung, generate Auth di sisi file import

// kalo dari API server lain, harus generate Authnya    
//$userkey = (isset($arrPostVars['_userkey']) && !empty($arrPostVars['_userkey'])) ? $arrPostVars['_userkey'] : 0;

//if(empty($_SERVER['HTTP_AUTHORIZATION'])){ 
	// utk point no 1
 	// kalo dr APPS, coba cek ad ID Unique gk 
//	endForInvalidLoginError($result['message']); 
//	$result = $security->adminLogin('apiuser','074f1feWintera');// sementara utk kenari dulu, nanti diganti jgn hardcode
//	if ( $result['valid']  == false) { 
//		endForInvalidLoginError($result['message']); 
//	}else{ 
//		$userkey = $result['data']['pkey']; 
//
//		// alter header auth
//		$usertoken = $security->getUserOTP($userkey);    
//		$_SERVER['HTTP_AUTHORIZATION'] = $_SERVER['HTTP_AUTHORIZATION'].'.'.$usertoken;
//	}
//}else{
//	$arrAuth= explode('.',$_SERVER['HTTP_AUTHORIZATION']);
//	$userkey = $arrAuth[1];
//}

// test kenari
//$result = $security->adminLogin('apiuser','074f1feWintera#');// sementara utk kenari dulu, nanti diganti jgn hardcode
//if ( $result['valid']  == false) { 
//	endForInvalidLoginError($result['message']); 
//}else{ 
//	$userkey = $result['data']['pkey']; 
//
//	// alter header auth
//	$usertoken = $security->getUserOTP($userkey);    
//	$_SERVER['HTTP_AUTHORIZATION'] = $_SERVER['HTTP_AUTHORIZATION'].'.'.$userkey.'.'.$usertoken;
//}

// normalnya
$arrAuth= explode('.',$_SERVER['HTTP_AUTHORIZATION']);
$userkey = $arrAuth[1];

// utk cancel trans
// kalo utk ubah status, kalo diletakan dibawah, bentrok pas inject warehousekey
$changeStatusKey = 0;
if(isset($_GET['_mnv-cancel']) && $_GET['_mnv-cancel'] == 1){
    // cari status cancel utk setiap object
    $rsStatus = $OBJ->getAllStatus();
    $_GET['_mnv-change-status'] = $rsStatus[count($rsStatus)-1]['pkey'];
}
 
if(isset($_GET['_mnv-change-status']) && !empty($_GET['_mnv-change-status'])){
    if($ACTION <> 'POST')  endForRequestMethodError();  
    // perlu cek hak akses
    $ACTION = 'CHANGESTATUS';
    
    $changeStatusKey = $_GET['_mnv-change-status']; 
    $newPostVar = array();
    for($i=0;$i<count($arrPostVars);$i++) { 
        $newPostVar[$i]['code'] = $arrPostVars[$i];
        $newPostVar[$i]['statuskey'] = $changeStatusKey;
    }
    
    $arrPostVars = $newPostVar;
}

// inject warehousekey
$totalPostVars = count($arrPostVars);
for($i=0;$i<$totalPostVars;$i++){ 
    if(!isset($arrPostVars[$i]['warehouse_id']) || empty($arrPostVars[$i]['warehouse_id']))
        $arrPostVars[$i]['warehouse_id'] = WAREHOUSE_ID;
    
   // unset($arrPostVars[$i]['_userkey']);  // kalo ada, non aktifin dulu liat kepake dimana nanti
}

// khusus LOGOL dimatikan saja
// kemungkinan userkey sudah ad dr POST di _include.php
// nanti dicke perlu dioverwrite atau gk
/*if(!isset($userkey) || empty($userkey))
    $userkey = (isset($_GET['_userkey']) && !empty($_GET['_userkey'])) ? $_GET['_userkey'] : '';*/


$actionkey = 10;
switch($ACTION){ 
    case 'POST' :  
    case 'PUT' :  $actionkey = 11; break;
    case 'DELETE' : $actionkey = 12; break;   
    case 'CHANGESTATUS' : $actionkey = $changeStatusKey; break;   
};
 
// pake $class karena $OBJ blm terbentuk
$gatePassResponse = $security->APIGatePassV3($userkey, '', $actionkey,true);
 
?>