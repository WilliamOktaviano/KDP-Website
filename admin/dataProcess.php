<?php
  
	foreach ($_POST as $k => $v) {
		
		if (!is_array($v))
			 $v = trim($v);  
		
		$arr[$k] = $v;     
         
	}  
	 
	
	$arrReturn = array(); 
	  
	$arr['createdBy'] =  base64_decode($_SESSION[$obj->loginAdminSession]['id']);
	$arr['modifiedBy'] =  base64_decode($_SESSION[$obj->loginAdminSession]['id']);   	
	
    
// validasi security
// sehingga di BASECLASS tidak perlu ad validasi security sehingga bisa bypass jika ada kepentingan
// ex. User boleh delete (yg otomatis ubah status ke pembatalan, tp user tidak memiliki hak akses utk pembatalan)
 
	switch ($_POST['action']) {
  		case 'add': if(!$security->isAdminLogin($securityObject,11,false)) die;
						
						$arrReturn = $obj->addData($arr);
						break;
					
		case 'edit': if(!$security->isAdminLogin($securityObject,11,false)) die;
						$arrReturn = $obj->editData($arr);
						break;
				
		case 'resendEmail': if(!$security->isAdminLogin($securityObject,11,false)) die;
						$arrReturn = $obj->sendInvoice($arr['hidId']);
						break;
									
					
  		case 'delete': if(!$security->isAdminLogin($securityObject,12,false)) die;
                        
                        $description = (isset($arr['description'])) ? $arr['description'] : '';
						$arrPkey = $arr['selectedPkey']; 
						
						$arrReturn  = array(); 
						for ($i=0;$i<count($arrPkey);$i++){
                            
                            $obj->startNewErrorLogSession(); 
							$arrTemp = $obj->delete($arrPkey[$i],false,$description);
							
							for($j=0;$j<count($arrTemp);$j++)
								array_push($arrReturn, $arrTemp[$j]);
						 
						}
					
 	    				break;
		 
 				
  		case 'duplicate': if(!$security->isAdminLogin($securityObject,11,false)) die;
  					
						$arrPkey = $arr['selectedPkey']; 
						
						$arrReturn  = array(); 
						for ($i=0;$i<count($arrPkey);$i++){
                            
                            $obj->startNewErrorLogSession(); 
							$arrTemp = $obj->duplicateData($arrPkey[$i],false,array('reuseCode' => false));
                            $arrTemp = $arrTemp['errorLog'];
							
							for($j=0;$j<count($arrTemp);$j++)
								array_push($arrReturn, $arrTemp[$j]);
						 
						}
					
 	    				break;
		 
  		case 'changestatus':   if(!$security->isAdminLogin($securityObject,$arr['newStatus'],false))die;  // sementara karena masi ad class yg overwrite change status yg blm diupdate
            
                                $arrPkey = $arr['selectedPkey']; 
                                if (empty($arrPkey)) break;
            
								$arrReturn  = array(); 
            
                                $description = (isset($arr['description'])) ? $arr['description'] : '';
             					for ($i=0;$i<count($arrPkey);$i++){
                                    
                                    $obj->startNewErrorLogSession();   
									$arrTemp = $obj->changeStatus($arrPkey[$i],$arr['newStatus'],$description,$arr['copyData']);
                                    
                                    for($j=0;$j<count($arrTemp);$j++)
										array_push($arrReturn, $arrTemp[$j]);
								  
								} 
            
 	    					break;

   		case 'changetag': if(!$security->isAdminLogin($securityObject,11,false)) die;
							 
								$arrPkey = $arr['selectedPkey']; 
								
								$arrReturn  = array(); 
								for ($i=0;$i<count($arrPkey);$i++){
									
									$arrTemp = $obj->changeTag($arrPkey[$i],$arr['newTag']);
									
									for($j=0;$j<count($arrTemp);$j++)
										array_push($arrReturn, $arrTemp[$j]);
								 
								}
							
							break;
            
        case 'requestpickup' :  // hak akses harus di cek lg
                                $arrPkey = $arr['selectedPkey']; 
                                for ($i=0;$i<count($arrPkey);$i++) 
									 $obj->requestPickup($arrPkey[$i]);   
                                break;
            
        case 'resyncmarketplace' :  $arrPkey = $arr['selectedPkey']; 

                                    $syncCriteria = array(); 
                                    $syncCriteria['attr'] = array('name','brand', 'qoh', 'price','measurement', 'shortDescription','image', 'others'); // karena kalo stok awal 0, pas brg masuk, harga harus update ulang
                                    $syncCriteria['type'] = 2;  
                                    $syncCriteria['itemkey'] = $arrPkey; 
             
                                    $marketplace = createObjAndAddToCol(new Marketplace());
                                    $item->reupdateMarketplaceAttribute($arrPkey);
                                    $marketplace->syncProductsInAllMarketplace($syncCriteria);   
                                    break;
            
        case 'generaterow' :    $arrPkey = $arr['selectedPkey']; 
                                $rs = $obj->searchData('','',true, ' and '.$obj->tableName.'.pkey in ('.$obj->oDbCon->paramString($arrPkey,',').')'); 
                                //$rs = $obj->getReadStatus($rs);
                                $rsStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','textcolor'); 
            
                                // isi tag
                                $obj->getAllTag();
                
                                $arrReturn = array();
                                foreach($rs as $row){
                                    array_push($arrReturn, array(
                                                                'pkey' => $row['pkey'],
                                                                'html' => generateDataRow($obj,$row,$arrColumn,$rsStatus, false) // $arrColumn sudah diupdate dr dataList.php
                                                                ));
                                }
                                break;
            
        case 'updatecolumnheader' : $obj->updateColumnHeader($_POST['fileName'],json_decode($_POST['param'],true));
                                break;
	}	
	    

    // boleh gk disini close mysql, 
    // terus update marketplace queue,
    // send request ke marketplace jgn pake database lg
    

	echo json_encode($arrReturn);  
	die; 
	
?>