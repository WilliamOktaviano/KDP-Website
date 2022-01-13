<?php

require_once '_global.php';

// klao perlu convert ke image dan token
// bisa lebih dr satu jenis image
if(!empty($IMAGE_SET)){ 
    
    //$folderPath =  $OBJ->uploadTempDoc;
    //if (!is_dir($folderPath))  mkdir($folderPath, 0755, true);
    
    $digits = 3;
     
    //$OBJ->setLog('POST VAR',true);
    //$OBJ->setLog($arrPostVars,true);
    
    // loop sebanyak data row
    for($i=0;$i<$totalPostVars;$i++){ 
			 
         foreach($IMAGE_SET as $imageSet){

            // upload ke _temp
            $uploadPath = (!empty($imageSet['uploadPath'])) ? $imageSet['uploadPath'] : $OBJ->uploadFolder;
            $paramName = $imageSet['paramName'];
            $paramImageList = $imageSet['paramImageList'];
            $paramToken = $imageSet['paramToken'];
                
            $imageURLs = (isset($arrPostVars[$i][$paramName])) ? $arrPostVars[$i][$paramName] : array(); 
			
            $token = str_pad(rand(0, pow(10, $digits)-1), $digits, '0', STR_PAD_LEFT).time();
            $arrImageList = array();
             
            $destPath = $OBJ->uploadTempDoc.$uploadPath.$token.'/';
			 
            if (!is_dir($destPath))  mkdir($destPath, 0755, true);
             
            foreach($imageURLs as $imageRow){
                
                // download ke folder temp  
                $url = $imageRow['url'];
                
                $arrPath = explode('/',$url);  
                $fileName = $arrPath[count($arrPath)-1]; 
                $OBJ->grabImage($url,$destPath.$fileName);
                 
                array_push($arrImageList, $fileName);
            }
            
            $arrPostVars[$i][$paramToken] = $token;
            $arrPostVars[$i][$paramImageList] = implode(',',$arrImageList);
             
            unset($arrPostVars[$i][$paramName]);
        }
        
    }
     
    
}

//default  
http_response_code(900);

switch($ACTION){
    case 'POST' :  
           
            $usecode = $OBJ->useAutoCode($OBJ->tableName);  
            $hasSuccessValue = false;
            $arrFailed = array();
            $ARR_RETURN_VALUE = array();

            for($i=0;$i<$totalPostVars;$i++){ 
                $OBJ = getNewObj(); // karena field yg gk ad di unset di normalize, jd harus create ulang objextnya

                $PARAM = array();    
                $errorMessage = array(); 
                $postVars = $arrPostVars[$i];
                                
                getDetailsAPIParam($ACTION,$PARAM,$postVars, $API_FIELDS, array(), $errorMessage);
 
                // kalo ad error jgn diproses  
                if(!empty($errorMessage)){  
                    addFailedRows($arrFailed, array(
                                    'code' => $PARAM['code'],
                                    'message' => $errorMessage, // error disini,
                            )); 
                }else{
                     
                    if($usecode) $PARAM['code'] = '[auto code]'; 
                    
                    // harus start trans lg
                    try{

                            if(!$OBJ->oDbCon->startTrans(true))
                                throw new Exception($OBJ->errorMsg[100]);
                         
                                $result = $OBJ->addData($PARAM); 
                                // harus bedain gagal karena duplikasi atau knp 
                                // blm tentu semua ada trdate...
                                if (isset($result[0]['response']['code']) && $result[0]['response']['code'] == '280'){
                                    $rs = $OBJ->getDataRowById($result[0]['response']['pkey']);
                                    if (isset($rs[0]['trdate']))
                                        $result[0]['message'] .=  '###'.$OBJ->formatDBDate($rs[0]['createdon'],'Y-m-d').'###'; // format tgl sesuai permintaaan logol
                                }

                              if(!$result[0]['valid'])
                                throw new Exception( $result[0]['message'] );
  
                              $OBJ->oDbCon->endTrans();
                        }catch(Exception $e){
                            $OBJ->oDbCon->rollback(); 
                        }	
                    
                    $response = getResponseValue($result,true);   

                    if(empty($response['errorMessage'])){

                        array_push($ARR_RETURN_VALUE,
                                 array(
                                        //'response_code' => 200,   
                                       'code' => $response['returnValue']['data']['code'], //$PARAM['code'], // overwrite kalo auto code 
                                       'key' => $response['returnValue']['data']['pkey'], //$PARAM['code'], // overwrite kalo auto code 
                                       'message' => $response['successMessage'],
                                    )
                                ); 

                        $hasSuccessValue = true;

                    }else{ 
                        addFailedRows($arrFailed, array(
                                'code' => $PARAM['code'],
                                'message' => $response['errorMessage'], 
                        ));

                    } 
                }
            }  
                
            $RETURN_VALUE['response_code'] = ($hasSuccessValue) ? 200 : 409;
            $RETURN_VALUE['success_rows'] = count($ARR_RETURN_VALUE);
            $RETURN_VALUE['success_data'] = $ARR_RETURN_VALUE;
            $RETURN_VALUE['failed_rows'] = count($arrFailed);
            $RETURN_VALUE['failed_data'] = $arrFailed;
           
            break;
      
    case 'PUT' :  
        
            $rsCol = updatingPkeyAndPostVars($OBJ, $arrPostVars, $API_FIELDS);
            $usecode = $OBJ->useAutoCode($OBJ->tableName);  
            $hasSuccessValue = false;
            $arrFailed = array();
            $ARR_RETURN_VALUE = array();
        
            for($i=0;$i<$totalPostVars;$i++){ 
                $OBJ = getNewObj(); // karena field yg gk ad di unset di normalize, jd harus create ulang objextnya
                
                $PARAM = array();    
                $errorMessage = array(); 
                $postVars = $arrPostVars[$i];

                getDetailsAPIParam($ACTION, $PARAM,$postVars, $API_FIELDS, array(), $errorMessage);
				
                // kalo ad error jgn diproses  
                if(!empty($errorMessage)){  
                    addFailedRows($arrFailed, array(
                                    'code' => $PARAM['code'],
                                    'message' => $errorMessage, // error disini,
                            )); 
                }else{
                     
                    //$rs = (isset($rsCol[$PARAM['code']])) ? $rsCol[$PARAM['code']] : array();  
                    $rs = (isset($rsCol[$PARAM['pkey']])) ? $rsCol[$PARAM['pkey']] : array();  
                    
                    //get ID nya dulu   
                    // cek kalo ad di add, kalo gk ad edit
                    // nanti perlu di optimized sekali query saja
                 
                    // harus di start trans lg
                    try{

                        if(!$OBJ->oDbCon->startTrans(true))
                            throw new Exception($OBJ->errorMsg[100]);

                         if (empty($rs)){
                            // add new data
                            // kalo tipe code nya auto, diisi nilai default saja
                            // ini nanti perlu dicek boleh atau gk, kalo di hilangin, masalah tdk dengan excel

                            if($usecode) $PARAM['code'] = '[auto code]';

                            $result = $OBJ->addData($PARAM);  
                        }else{
                            $PARAM['hidId'] = $rs['pkey']; 
                            $PARAM['hidModifiedOn'] = $rs['modifiedon'];  

                            //$OBJ->setLog($OBJ->oDbCon->transactionCounter,true); 
                            //$OBJ->setLog($PARAM,true);

                            $result = $OBJ->editData($PARAM);   
                        }
 
                      if(!$result[0]['valid'])
                        throw new Exception( $result[0]['message'] );

                      $OBJ->oDbCon->endTrans();
                        
                    }catch(Exception $e){
                        $OBJ->oDbCon->rollback(); 
                    }	
 
                    $response = getResponseValue($result);   
 
                    if(empty($response['errorMessage'])){ // isset buat handle kalo ad sql / php error

                        array_push($ARR_RETURN_VALUE,
                                 array(
                                        //'response_code' => 200,
                                        'code' => $PARAM['code'],
                                        'message' => $response['successMessage'],
                                    )
                                ); 

                        $hasSuccessValue = true;

                    }else{
 
                        addFailedRows($arrFailed, array(
                                'code' => $PARAM['code'],
                                'message' => $response['errorMessage'], 
                        ));

                    }
                    
                }

            }
        
      
        $RETURN_VALUE['response_code'] = ($hasSuccessValue) ? 200 : 409;
        $RETURN_VALUE['success_rows'] = count($ARR_RETURN_VALUE);
        $RETURN_VALUE['success_data'] = $ARR_RETURN_VALUE;
        $RETURN_VALUE['failed_rows'] = count($arrFailed);
        $RETURN_VALUE['failed_data'] = $arrFailed;
         
        break;
   
    case 'GET' :  
            /*if(!isset($_GET) || empty($_GET)){ 
                $RETURN_VALUE['response_code'] = 400;
                $RETURN_VALUE['message'] = $class->errorMsg[213];  
                break;
            } */
        
            $arrKeywords = array('order_by','order_type','keyword','date_from','date_to','offset','rows_per_page','_detail','show_detail');
        
            $criteria = array();
        
            $orderby = (!empty($_GET['order_by'])) ? $OBJ->oDbCon->paramOrder($_GET['order_by']) : $OBJ->tableName.'.pkey'; // order by harus dr kolom yg terdaftar saja
            $ordertype = (isset($_GET['order_type']) && !empty($_GET['order_type']) && $_GET['order_type'] != 1) ? 'asc' : 'desc'; 
            $order =' order by '.  $orderby  .' '. $ordertype;
        
            $quickSearchKey = (isset($_GET['keyword']) && !empty($_GET['keyword'])) ?  $_GET['keyword'] :  ''; 
        
            $quickSearchKey = trim($quickSearchKey);
        
            if(!empty($quickSearchKey)){ 
                // blm semua dipinhdakan ke class
                if (isset($OBJ->arrSearchColumn)){
                    $arrSearchColumn = $OBJ->arrSearchColumn;
                        
                    $quicksearchcriteria = array();
                    for($i=0;$i<count($arrSearchColumn);$i++){
                        array_push($quicksearchcriteria, $arrSearchColumn[$i][1] .' like ('.$OBJ->oDbCon->paramString( '%'.$quickSearchKey.'%' ).') ');	 
                    }
                    $quicksearchcriteria = '(' .implode(' OR ', $quicksearchcriteria).')'; 
                    array_push($criteria, $quicksearchcriteria);
                }
            }  

            //periode 
            if($OBJ->isTransaction){
                if(isset($_GET['date_from']) && !empty($_GET['date_from'])){
                     // nnati harus cek ada peride tanggal tdk
                     array_push($criteria, $OBJ->tableName.'.trdate >= '.$OBJ->oDbCon->paramString(date('Y-m-d',$_GET['date_from'])));
                }

                if(isset($_GET['date_to']) && !empty($_GET['date_to'])){
                     // nnati harus cek ada peride tanggal tdk
                     array_push($criteria, $OBJ->tableName.'.trdate <= '.$OBJ->oDbCon->paramString(date('Y-m-d',$_GET['date_to'])));
                } 
            }
           
             
            // ====> khusus statuskey sama code, sementara
            //statuskey
            if(isset($_GET['statuskey']) && !empty($_GET['statuskey'])){
                // harus explode dulu agar lebih aman
                $arrStatus = explode(',',$_GET['statuskey']); 
            }else if(isset($_GET['status_key']) && !empty($_GET['status_key'])){
                // harus explode dulu agar lebih aman
                $arrStatus = explode(',',$_GET['status_key']); 
            }else{
                // otomatis hilangkan yg statusnya batal
                $arrStatus = $OBJ->getAllStatus();
                $arrStatus = array_column($arrStatus,'pkey');
                array_pop($arrStatus);
            }  
            array_push($criteria, $OBJ->tableName.'.statuskey in ('.$OBJ->oDbCon->paramString($arrStatus,',').')' );
        
            // kalo parameter pasti kode
            if (isset($_GET['code']) && !empty($_GET['code'])) { 
                $code = explode(',',$_GET['code']);
                array_push($criteria, $OBJ->tableName.'.code in('.$OBJ->oDbCon->paramString($code,',').')'); 
            }
          
            
            // cari berdasarkan dataset
            $searchDataSet = array_column($API_FIELDS,null,'paramName');
            foreach($_GET as $key => $searchBy){
                // cari ke structure, fieldny ap.. 
                // kalo gk bisa searchable
                if(in_array($key, $arrKeywords) || !isset($searchDataSet[$key]['search']['field'])) continue;  
                 
                array_push($criteria, $searchDataSet[$key]['search']['field'] . ' like ' .$OBJ->oDbCon->paramString('%'.$searchBy.'%') );  
            }
        
            //$OBJ->setLog($criteria,true);
        
            $criteria  =  implode(' AND ', $criteria);
            if (!empty($criteria)) $criteria = ' AND ' . $criteria;
         
            // LIMIT   
            $rowsPerPage = isset($_GET['rows_per_page']) ? $_GET['rows_per_page']: $OBJ->loadSetting('adminTotalRowsPerPage');
            $offset = isset($_GET['offset']) ? $_GET['offset'] : 1; 
            if($offset <= 0) $offset = 1;
            $limitFrom = ($offset - 1) * $rowsPerPage;  
            $limit = ' limit '.$limitFrom.','.$rowsPerPage; 
            

            $OBJ->setCriteria($criteria);  
            $query = $OBJ->getQueryForList();
            if (empty($query))
                $query = $OBJ->getQuery();
          
            //$OBJ->setLog( $query . $order . $limit,true);
            $rs =  $OBJ->oDbCon->doQuery( $query . $order . $limit  );
        
            //ganti semua model refkey dengan refcode
            foreach($RETURN_IN_CODE as $key=>$row){
                for($i=0;$i<count($rs);$i++){ 
                    $rsTemp = $row['obj']->getDataRowById($rs[$i][$key]);
                    $rs[$i][$key] = (isset($rsTemp[0]['code']) && !empty($rsTemp[0]['code'])) ? $rsTemp[0]['code'] : USER_SYSTEM['code'];
                }
            }

        
            if  (!empty($RETURN_FIELDS))
                $API_FIELDS = array_merge($API_FIELDS,$RETURN_FIELDS); 
         
            // compability mode 
            if(isset($_GET['show_detail'])) 
                $showDetail = (!empty($_GET['show_detail'])) ? true : false;
            else 
                $showDetail = (isset($_GET['_detail']) && !empty($_GET['_detail'])) ? true : false;
          
        
            $rs = $OBJ->manipulateDataGetAPI($rs);
            $rs = $OBJ->compileAPIField($rs,$API_FIELDS,$showDetail);
         
            //$rs = array_column($rs,null,'code');

            // compile array for return 
            if(empty($rs)){
                $RETURN_VALUE['response_code'] = 400;
                $RETURN_VALUE['message'] = $class->errorMsg[213];
            }else{ 
                $rsCountedTotalRows = $OBJ->countTotalRows($criteria);
                $totalDataRows = $OBJ->getCountedTotalRows($rsCountedTotalRows); 
                $totalPages = ceil($totalDataRows/$rowsPerPage);
   
                $RETURN_VALUE['response_code'] =  200;
                $RETURN_VALUE['data'] = $rs;  
                $RETURN_VALUE['offset'] = $offset;  
                $RETURN_VALUE['rows_per_page'] = $rowsPerPage;  
                $RETURN_VALUE['total_pages'] = $totalPages;    
                $RETURN_VALUE['total_rows'] = $totalDataRows;  
                $RETURN_VALUE['message'] = '';  
            }
            break;
         
    case 'DELETE' :    
            if(!isset($postVars) || empty($postVars['code'])){ 
                $RETURN_VALUE['response_code'] = 400;
                $RETURN_VALUE['message'] = $class->errorMsg['code'][1];  
                break;
            } 

            $code = $postVars['code']; 
          
            $rs = $OBJ->searchData('','',true,' and '.$OBJ->tableName.'.code = '.$OBJ->oDbCon->paramString($code));  
            if(empty($rs)){ 
                $RETURN_VALUE['response_code'] = 400;
                $RETURN_VALUE['message'] = $class->errorMsg[213];  
                break;
            }
          
            $result = $OBJ->delete($rs[0]['pkey']);

            // compile array for return  
            $RETURN_VALUE = getResponseValue($result)['returnValue'];

            break;
         
     case 'CHANGESTATUS' :  
            
            $hasSuccessValue = false;
            $arrFailed = array();
            $ARR_RETURN_VALUE = array();

            $code = array_column($arrPostVars, 'code'); 
            $rsCol = array();
            if(!empty(($code))) {
             $rsCol = $OBJ->searchDataRow(array($OBJ->tableName.'.pkey',$OBJ->tableName.'.code',$OBJ->tableName.'.modifiedon'),
                                        ' and ' .$OBJ->tableName.'.code in ('.$OBJ->oDbCon->paramString($code,',').')'
                                      );    
             $rsCol = array_column($rsCol,'pkey','code');
            } 
         
            for($i=0;$i<$totalPostVars;$i++){  
                $OBJ = getNewObj(); 
                $OBJ->resetErrorLog();
                
                $result = array(); // inisialisasi ulang, karena kalo masuk catch karena nested throw, gk ke udpate resultnya
                
                $code = $arrPostVars[$i]['code'];
                
                if(empty($rsCol[$code])){
                      addFailedRows($arrFailed, array(
                                'code' => $code,
                                'message' => $OBJ->errorMsg[213], 
                        ));
                    
                }else{
                    
                    // change status gk selalu balikin nilai, bisa langsung throw
                    //$result = $OBJ->changeStatus($rsCol[$code],$arrPostVars[$i]['statuskey']);
                    $rs = $OBJ->searchDataRow(array($OBJ->tableName.'.pkey', $OBJ->tableName.'.statuskey'),
                                                       ' and ' .$OBJ->tableName.'.pkey = '.$OBJ->oDbCon->paramString($rsCol[$code])
                                                      );
                    $startStatus = $rs[0]['statuskey'] + 1;
                    $newStatus = $arrPostVars[$i]['statuskey'];
                    
                    if(empty($rs)){
                        
                        addFailedRows($arrFailed, array(
                                'code' => $code,
                                'message' => $OBJ->errorMsg[213], 
                        ));
                        
                    }elseif ($startStatus > $newStatus){
                        
                        addFailedRows($arrFailed, array(
                                'code' => $code,
                                'message' => $OBJ->errorMsg[201], 
                        ));
                        
                    }else{
                        
                        // harus start trans lg
                        try{ 
                            if(!$OBJ->oDbCon->startTrans(true))
                                throw new Exception($OBJ->errorMsg[100]);
   
                            else{
                                 // auto loop status
                                for($j=$startStatus;$j<=$newStatus;$j++){
                                    // reset ulang biar gk ad beberapa informasi sukses yg berulang 
                                    $OBJ->resetErrorLog();
                                    $result = $OBJ->changeStatus($rsCol[$code],$j);
                                     
                                    if(!$result[0]['valid'])
                                        throw new Exception( $result[0]['message'] ); 
                                }   
                            }
                           
                            $OBJ->oDbCon->endTrans();
                            
                        }catch(Exception $e){ 
                            $OBJ->oDbCon->rollback(); 
                        }	
 
                        $response = getResponseValue($result);

                        if(empty($response['errorMessage'])){

                            array_push($ARR_RETURN_VALUE,
                                     array(
                                            //'response_code' => 200,
                                            'code' => $code,
                                            'message' => $response['successMessage'],
                                        )
                                    ); 

                            $hasSuccessValue = true;

                        }else{ 
                            addFailedRows($arrFailed, array(
                                    'code' => $code,
                                    'message' => $response['errorMessage'], 
                            ));

                        }    
                        
                    }
 
                }
                
            }  
                
            $RETURN_VALUE['response_code'] = ($hasSuccessValue) ? 200 : 409;
            $RETURN_VALUE['success_rows'] = count($ARR_RETURN_VALUE);
            $RETURN_VALUE['success_data'] = $ARR_RETURN_VALUE;
            $RETURN_VALUE['failed_rows'] = count($arrFailed);
            $RETURN_VALUE['failed_data'] = $arrFailed;
           
            break;
        
     default : break;    
}


$apiLog = new APILog();  
try{  
	
	if(!$apiLog->oDbCon->startTrans(true))
		throw new Exception($apiLog->errorMsg[100]);

	// add to log
	$arrLog = array(); 
	$arrLog['ip'] = $_SERVER['REMOTE_ADDR'];
	$arrLog['payload'] = json_encode($arrPostVars);
	$arrLog['responseCode'] = $RETURN_VALUE['response_code'];
	$arrLog['action'] = $ACTION;
	$arrLog['endpoint'] =  $_SERVER['PHP_SELF'];
	$arrLog['responseMsg'] = (strtolower($ACTION) == 'get') ? '' : json_encode($RETURN_VALUE);
 
	$apiLog->addData($arrLog);
 	$apiLog->oDbCon->endTrans();

}catch(Exception $e){  
	$apiLog->oDbCon->rollback(); 
}	
	
http_response_code($RETURN_VALUE['response_code'] );
echo json_encode($RETURN_VALUE); 
  
?>