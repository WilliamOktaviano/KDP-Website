<?php
  
class ItemIn extends BaseClass{ 
 
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'item_in_header';
		$this->tableNameDetail = 'item_in_detail';
		$this->tableItemUnit = 'item_unit';
        $this->tableItem = 'item';
        //$this->tableSupplier = 'supplier';
        $this->tableSerial = 'item_in_detail_sn';
		$this->tableWarehouse = 'warehouse';
		$this->tableStatus = 'transaction_status';
        $this->tableVendorPartNumber = 'item_vendor_part_number';
		$this->tableItemVendorPartNumber = 'item_vendor_part_number' ; 
		$this->tableType = 'item_in_type';
        $this->isTransaction = true; 
		  
		$this->tableNeedToBeCopyOnCancel = array($this->tableNameDetail);
       
		$this->securityObject = 'ItemIn';  
           
        $this->arrDataDetail = array();  
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey','ref');
        $this->arrDataDetail['itemkey'] = array('hidItemKey'); 
        $this->arrDataDetail['qty'] = array('qty','number');
        $this->arrDataDetail['unitkey'] = array('selUnit');
        $this->arrDataDetail['unitconvmultiplier'] = array('unitConvMultiplier','number'); 
        $this->arrDataDetail['qtyinbaseunit'] = array('qtyInBaseUnit','number');
        $this->arrDataDetail['costinbaseunit'] = array('COGS','number'); 
        $this->arrDataDetail['vendorpartnumberkey'] = array('hidVendorPartNumberKey','number'); 
       
        $this->arrData = array();  
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => array('dataset' => $this->arrDataDetail))); 
        $this->arrData['code'] = array('code');
        $this->arrData['trdate'] = array('trDate','date');
        $this->arrData['trInvoiceDate'] = array('trInvoiceDate','date');
        $this->arrData['warehousekey'] = array('selWarehouseKey');
        $this->arrData['trdesc'] = array('trDesc');  
        $this->arrData['refkey'] = array('refkey');
        $this->arrData['reftabletype'] = array('reftabletype');
        $this->arrData['statuskey'] = array('selStatus'); 
        $this->arrData['isfullreceive'] = array('chkIsFullReceive');
        //$this->arrData['supplierkey'] = array('hidSupplierKey');
        $this->arrData['refcode'] = array('refCode');
        $this->arrData['employeekey'] = array('hidEmployeeKey');
		$this->arrData['coarevenuekey'] = array('hidCOARevenueKey');
		$this->arrData['typekey'] = array('selTransactionType');
         
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'refcode','title' => 'refCode','dbfield' => 'refcode', 'width' => 100));
        //array_push($this->arrDataListAvailableColumn, array('code' => 'supplier','title' => 'supplier','dbfield' => 'suppliername','default'=>true, 'width' => 250));
        array_push($this->arrDataListAvailableColumn, array('code' => 'description','title' => 'note','dbfield' => 'trdesc',  'default'=>true,'width' => 250));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
 

        $this->importUrl = 'import/itemIn';
       
        $this->printMenu = array();  
        array_push($this->printMenu,array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/itemIn'));
         
        $this->includeClassDependencies(array(

              'COALink.class.php',
              'GeneralJournal.class.php',
              'Item.class.php',
              'ItemUnit.class.php',
              'ItemInReceive.class.php',
              //'Supplier.class.php',
              'WarrantyPeriod.class.php',
              'Marketplace.class.php', 
              'ItemMovement.class.php', 
              'SalesOrderRentalWorkOrder.class.php',
              'Customer.class.php'

        ));
       
        $this->overwriteConfig();
       
   }
   
   function getQuery(){
	   
	   $sql = '
			SELECT '.$this->tableName.'.* ,
			   '.$this->tableWarehouse.'.code as warehousecode,
			   '.$this->tableWarehouse.'.name as warehousename,
			   '.$this->tableStatus.'.status as statusname 
			FROM '.$this->tableName.',  
         		 '.$this->tableStatus.',  
            	 '.$this->tableWarehouse.'  
			WHERE '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and  '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey
 	  ' .$this->criteria ; 
		 
        $sql .=  $this->getWarehouseCriteria() ;
        $sql .=  $this->getCompanyCriteria() ;
        
        return $sql;
    }
	 
   
   
   /* function afterUpdateData($arrParam, $action){  
        $this->updateDetail($arrParam); 
    }*/
  	
    function afterStatusChanged($rsHeader){ 
                
         // retrieve latest status
        $rsHeader = $this->getDataRowById($rsHeader[0]['pkey']);
        
        if ($rsHeader[0]['isfullreceive'] == 1 && $rsHeader[0]['statuskey'] == 2){  
            $sql = 'update '.$this->tableNameDetail.' set receivedqtyinbaseunit = qtyinbaseunit where refkey  = '.$this->oDbCon->paramString($rsHeader[0]['pkey']);
            $this->oDbCon->execute($sql); 
             
            $this->changeStatus($rsHeader[0]['pkey'],3); 
        }
        
        if($this->isActiveModule('marketplace')){
			if ($rsHeader[0]['isfullreceive'] == 1 && ($rsHeader[0]['statuskey'] == 2 || $rsHeader[0]['statuskey'] == 4)){  
				$marketplace = new Marketplace();
				$rsDetail = $this->getDetailById($rsHeader[0]['pkey']);
				$arrItemKey = array_column($rsDetail,'itemkey'); 
				$marketplace->updateProductsQOHInAllMarketplace($arrItemKey); 
			 
			}
		}
        
    }
      
    function getSerialNumber($refkey){
        $sql = 'select * from '.$this->tableSerial.' where refkey = ' . $this->oDbCon->paramString($refkey); 
        return $this->oDbCon->doQuery($sql);
    }
        
        
    function validateForm($arr,$pkey = ''){
        $showVendorPartNumber = $this->loadSetting('showVendorPartNumber');
		$item = new Item();   
		//$supplier= new Supplier();   
		  
		$arrayToJs = parent::validateForm($arr,$pkey); 
         
		//$arrSupplierkey = $arr['hidSupplierKey']; 
		$arrItemkey = $arr['hidItemKey']; 
		$arrSelUnit = $arr['selUnit']; 
		$arrQty = $arr['qty']; 
		$warehousekey = $arr['selWarehouseKey']; 
	    //$arrSerialNumber = $arr['snList'];
         
        if (empty($warehousekey))  
            $this->addErrorList($arrayToJs,false,$this->errorMsg['warehouse'][1]); 
       
        if($showVendorPartNumber)
            $arrVendorPartNumberKey = $arr['hidVendorPartNumberKey'];
		
		//validasi kalo status gk menunggu gk bisa edit 
		if (!empty($pkey)){
			$rs = $this->getDataRowById($pkey);
			if ($rs[0]['statuskey'] <> 1){
				$this->addErrorList($arrayToJs,false,$this->errorMsg[212]);
			}
		} 
      /*   
        $rsSupplier = $supplier->getDataRowById($arrSupplierkey);
        if(empty($rsSupplier))
            $this->addErrorList($arrayToJs,false,$this->errorMsg['supplier'][1]);*/
	  		
        $arrDetailKeys = array(); 
         
		for($i=0;$i<count($arrItemkey);$i++) {
            $rsItem = $item->getDataRowById($arrItemkey[$i]);
            
		 	if (empty($arrItemkey[$i]) ){ 
				$this->addErrorList($arrayToJs,false, $this->errorMsg['item'][1]); 	
			} else{
                
                if ($this->unFormatNumber($arrQty[$i]) <= 0){
                    $this->addErrorList($arrayToJs,false,$rsItem[0]['name']. '. ' . $this->errorMsg[500]); 
                } 

                // cek punya konversi unit utk satuan yg dipilih gk  
                $conv = $item->getConvMultiplier($arrItemkey[$i],$arrSelUnit[$i]);
                if (empty($conv)){
                    $this->addErrorList($arrayToJs,false,$rsItem[0]['name']. '. ' . $this->errorMsg['itemUnitConversion'][3]); 
                }  

                if($showVendorPartNumber){
                    if (in_array( array($arrItemkey[$i], $arrVendorPartNumberKey[$i]),$arrDetailKeys)){  
                        $this->addErrorList($arrayToJs,false, $rsItem[0]['name'].'. '.$this->errorMsg[215]); 	 
                    }else{ 
                        array_push($arrDetailKeys, array($arrItemkey[$i], $arrVendorPartNumberKey[$i]));
                    }
                    
                    if(!empty($arrVendorPartNumberKey[$i])){ 
                        $rsVendor = $item->getVendorPartNumber($arrItemkey[$i], ' and '.$this->tableVendorPartNumber.'.pkey ='.$this->oDbCon->paramString($arrVendorPartNumberKey[$i]));
                        if(empty($rsVendor))
                            $this->addErrorList($arrayToJs,false,$rsItem[0]['name']. '. ' . $this->errorMsg['vendorPartNumber'][3]); 
                    }else{
                            $this->addErrorList($arrayToJs,false,$rsItem[0]['name']. '. ' . $this->errorMsg['vendorPartNumber'][1]); 
                    }
                }else{
                    if (in_array($arrItemkey[$i],$arrDetailKeys)){  
                        $this->addErrorList($arrayToJs,false, $rsItem[0]['name'].'. '.$this->errorMsg[215]); 	 
                    }else{ 
                        array_push($arrDetailKeys, $arrItemkey[$i]);
                    }   
                }  
            }    		   
           
        }
  
		return $arrayToJs;
	 }
	  
    function validateCancel($rsHeader,$autoChangeStatus=false){ 
		$id = $rsHeader[0]['pkey'];
  
		//cek apakah sudah ad penerimaan
		if($this->isActiveModule('itemInReceive')){
			if (!$rsHeader[0]['isfullreceive']) {
				$itemInReceive = new ItemInReceive();
				$rsItemInReceive = $itemInReceive->searchData('','',true,' and '.$itemInReceive->tableName.'.refkey = '.$this->oDbCon->paramString($id).' and ('.$itemInReceive->tableName.'.statuskey in (2,3))');

				if (!empty($rsItemInReceive))
					 $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' ' .$this->errorMsg['itemIn'][2]);
			} 
		}
	 }
    

	function validateConfirm($rsHeader){
          
        $warehouse = new Warehouse();  
        $coaLink = new COALink();
        $item = new Item();
        
		$id = $rsHeader[0]['pkey'];
        $rsDetail = $this->getDetailById($id);
        
//        $showVendorPartNumber = $this->loadSetting('showVendorPartNumber');
//        if($showVendorPartNumber){ 
//            for($i=0;$i<count($rsDetail);$i++){
//              $rsVendor = $item->getVendorPartNumber($rsDetail[$i]['itemkey'], ' and '.$this->tableVendorPartNumber.'.pkey ='.$this->oDbCon->paramString($rsDetail[$i]['vendorpartnumberkey']));
//              if(empty($rsVendor)){  
//                  $rsItem = $item->getDataRowById($rsDetail[$i]['itemkey']);
//                  $this->addErrorLog(false,$rsItem[0]['name']. '. ' . $this->errorMsg['vendorPartNumber'][3]); 
//              }
//            } 
//        }
  
        
        // hati2 kalo aktifin, karena bermasalah di API
        /*if(USE_SN && $rsHeader[0]['isfullreceive'] == 1){
            for($i=0;$i<count($rsDetail);$i++){
                $rsItem = $item->getDataRowById($rsDetail[$i]['itemkey']);
                if(!$rsItem[0]['needsn'])
                    continue;

                $rsSN = $this->getSerialNumber($rsDetail[$i]['pkey']);

                if($rsDetail[$i]['qtyinbaseunit'] <> count($rsSN))
                    $this->addErrorLog(false, $this->errorMsg['serialnumber'][2]); 

            }	 
        } */
        
         if (USE_GL){ 
            $arrCOA = array();
            array_push($arrCOA, 'hpp' , 'inventory' ); 
            for ($i=0;$i<count($arrCOA);$i++){
                $rsCOA = $coaLink->getCOALink ($arrCOA[$i], $warehouse->tableName,$rsHeader[0]['warehousekey'], 0); 
                if (empty($rsCOA))	
                    $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['coa'][3]);
            } 
         } 
		  
	 }		

	function confirmTrans($rsHeader){
        
        $id = $rsHeader[0]['pkey'];
        
		$itemMovement = new ItemMovement();  
        //$warrantyPeriod = new WarrantyPeriod(); 
        $item = new Item();
        
        
        $arrNote = array();
        array_push($arrNote, $rsHeader[0]['code']);
        array_push($arrNote, $this->ucFirst($this->lang['itemIn']));
        
        $salesOrderRentalWorkOrder = new SalesOrderRentalWorkOrder();
        $tableRentalWorkOrderKey = $this->getTableKeyAndObj($salesOrderRentalWorkOrder->tableName,array('key'));  
        $tableRentalWorkOrderKey = $tableRentalWorkOrderKey['key'];  
        switch($rsHeader[0]['reftabletype']){
            case  $tableRentalWorkOrderKey :  $rsWO = $salesOrderRentalWorkOrder->getDataRowById($rsHeader[0]['refkey']);  
                                              $customer = new Customer();
                                              $rsCustomer = $customer->getDataRowById($rsWO[0]['customerkey']);
                                              array_push($arrNote, ucfirst($this->lang['itemReturn']). ' ' .$rsCustomer[0]['name']); // kemungkinan nanti harus ambil nama customer
                                       
                   break;
            default :  $noteRental = '';
        }
        
         
        $note = implode('. ',$arrNote).'.'; 
	 	
		$rsDetail = $this->getDetailById($rsHeader[0]['pkey']);  
        $arrItemkey = array_column($rsDetail,'itemkey');
        
       	$this->updateConvMultiplier($id);
         
        $rsTableKey = $this->getTableKeyAndObj($this->tableName,array('key')); 
        // lock vendor part number 
        
//        for($i=0;$i<count($rsDetail); $i++){	
//            $sql = 'update '.$this->tableItemVendorPartNumber.' set islock = 1 where pkey = ' . $this->oDbCon->paramString($rsDetail[$i]['vendorpartnumberkey']); 
//            $this->oDbCon->execute($sql);  
//        }

		if($this->isActiveModule('itemInReceive')){ 
			if(!$rsHeader[0]['isfullreceive']){
				
        		$itemInReceive = new ItemInReceive();
                $arrParam = array();
            
                for($i=0;$i<count($rsDetail); $i++){		 
                    //$arrParam['hidDetailKey'][$i] = 0;
                    $arrParam['hidItemInDetailKey'][$i] = $rsDetail[$i]['pkey'];
                    $arrParam['hidItemKey'][$i] = $rsDetail[$i]['itemkey'];
                    $arrParam['hidVendorPartNumberKey'][$i] = $rsDetail[$i]['vendorpartnumberkey'];
                    $arrParam['unitkey'][$i] = $rsDetail[$i]['unitkey'];
                    $arrParam['unitconvmultiplier'][$i] = $rsDetail[$i]['unitconvmultiplier'];
                    $arrParam['orderedQtyInBaseUnit'][$i] = $rsDetail[$i]['qtyinbaseunit']; 
                    $arrParam['receivedQtyInBaseUnit'][$i] = $rsDetail[$i]['qtyinbaseunit'];
                    $arrParam['qtyMinusInBaseUnit'][$i] = $rsDetail[$i]['qtyinbaseunit'];
                    $arrParam['costinbaseunit'][$i] = $rsDetail[$i]['costinbaseunit'];
                    $arrParam['detailDesc'][$i] = ''; 
                }


                $user = base64_decode($_SESSION[$this->loginAdminSession]['id']);
                $arrParam['code'] = 'xxxxxx';
                $arrParam['hidItemInKey'] = $rsHeader[0]['pkey'];
                $arrParam['trDate'] = $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y');
                $arrParam['trInvoiceDate'] = $this->formatDBDate($rsHeader[0]['trinvoicedate'],'d / m / Y');
                //$arrParam['hidSupplierKey'] = $rsHeader[0]['supplierkey']; 
                $arrParam['trDesc'] = '';
                $arrParam['selWarehouse'] = $rsHeader[0]['warehousekey'];
                $arrParam['createdBy'] = $user;

                $arrayToJs = $itemInReceive->addData($arrParam); 

                if (!$arrayToJs[0]['valid'])
                    $this->addErrorLog(false, '<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[201].' '.$arrayToJs[0]['message'], true); 
        
                return; 
        } 
		}
		
          // kalo pake warranty
        // hati2 kalo aktifin, karena bermasalah di API
        /* if(USE_SN){
            $rsWarranty = $warrantyPeriod->searchData();
            $rsWarranty = array_column($rsWarranty,'period', 'pkey');
            $rsWarranty[0] = 0; // default kalo gk ad warranty
         }*/
         
        
        $arrItem = $item->searchDataRow( array($item->tableName.'.pkey',$item->tableName.'.code',$item->tableName.'.warrantyperiodkey',$item->tableName.'.warrantyvendorperiodkey',$item->tableName.'.isrental'),
                                            ' and '.$item->tableName.'.pkey in ('.$this->oDbCon->paramString($arrItemkey,',').')'
                                      );
        $arrItem = array_column($arrItem,null,'pkey');

		for($i=0;$i<count($rsDetail); $i++){ 
           $rsItem = $arrItem[$rsDetail[$i]['itemkey']];		 
            
		   $itemMovement->updateItemMovement($id,$rsDetail[$i]['itemkey'],$rsDetail[$i]['qtyinbaseunit'],$rsDetail[$i]['costinbaseunit'],$this->tableName, $rsHeader[0]['warehousekey'], $note ,$rsHeader[0]['trdate'],$rsDetail[$i]['vendorpartnumberkey']);
		     
            // gk perlu rental company yg bisa merental kan barang
            if($rsItem['isrental'] == 1)
                $itemMovement->updateItemMovementRental($id,$rsDetail[$i]['itemkey'],$rsDetail[$i]['qtyinbaseunit'], 0 ,$this->tableName, $rsHeader[0]['warehousekey'], $noteRental,$rsHeader[0]['trdate'],$rsWO[0]['customerkey']);
            
            // hati2 kalo aktifin, karena bermasalah di API
            /*if(USE_SN){
                $this->setLog("Test",true);
                // set warranty enddate
                $warrantyMonth = $rsWarranty[$rsItem['warrantyperiodkey']];
                
                $warrantyVendorMonth = $rsWarranty[$rsItem['warrantyvendorperiodkey']]; 
                $date = new DateTime($rsHeader['trinvoicedate']);
                $date->add(new DateInterval('P'.$warrantyVendorMonth.'M'));
                $warrantyVendorEndDate = $date->format('d / m / Y'); 
 
                
               $rsSN = $this->getSerialNumber($rsDetail[$i]['pkey']); 
               for($j=0;$j<count($rsSN); $j++)
                        $itemMovement->updateItemSNMovement( 
                                array(
                                'refkey' => $rsDetail[$i]['pkey'],
                                'refheaderkey' => $id,
                                'itemkey' => $rsDetail[$i]['itemkey'],
                                'vendorpartnumberkey' => $rsDetail[$i]['vendorpartnumberkey'],
                                'sn' => $rsSN[$j]['serialnumber'],
                                'qtyinbaseunit' => 1,
                                'costinbaseunit' => $rsDetail[$i]['costinbaseunit'],
                                'reftable' => $this->tableName,
                                'warehousekey' => $rsHeader[0]['warehousekey'],
                                'note' => $note,
                                'trdate' => $rsHeader[0]['trdate'] ,
                                'warrantyperiodkey' => $rsItem[$i]['warrantyperiodkey'],
                                'warrantyperiodtime' => $warrantyMonth ,
                                'warrantyvendorperiodkey' => $rsItem[$i]['warrantyvendorperiodkey'],
                                'warrantyvendorperiodtime' => $warrantyVendorMonth ,
                                'warrantyvendorperiodexpireddate' => $warrantyVendorEndDate,
                                'supplierkey' => $rsHeader[0]['supplierkey'],
                                'reftabletype' => $rsTableKey['key'] 
                       )); 
            }*/
           
		}	 
        
		//update jurnal umum 
        $this->updateGL($rsHeader);
         
	} 
     
    function updateGL($rsHeader){
        if (!USE_GL) return;
        
		 $warehouse = new Warehouse();
         $coaLink = new COALink();
		 $generalJournal = new GeneralJournal();
         $item = new Item();
        
         $rsKey = $generalJournal->getTableKeyAndObj($this->tableName);
		 $arr = array();
		 $arr['pkey'] = $generalJournal->getNextKey($generalJournal->tableName);
		 $arr['code'] = 'xxxxx';
		 $arr['refkey'] = $rsHeader[0]['pkey'];
		 $arr['refTableType'] = $rsKey['key'];
		 $arr['trDate'] =  $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y'); 
		 $arr['createdBy'] = 0; 
		 $arr['selWarehouseKey'] = $rsHeader[0]['warehousekey'];
        
         $warehousekey = $rsHeader[0]['warehousekey'];
             
         $arrNote = array(); 
         $note = $this->ucFirst($this->lang['itemIn']);  
         array_push($arrNote, $note); 
          
         $arr['trDesc'] = implode('. ',$arrNote).'.';
		  
/*		$totalHPP = 0 ;
        $rsDetail = $this->getDetailById($rsHeader[0]['pkey']); 
        for($i=0;$i<count($rsDetail);$i++){
            $totalHPP += ($rsDetail[$i]['costinbaseunit']*$rsDetail[$i]['qtyinbaseunit']);
        }*/
        
  
        $temp = -1;
             
        $arrItemCOA = array(); 
        $rsDetail = $this->getDetailById($rsHeader[0]['pkey']); 
        foreach($rsDetail as $detail){
            $itemCOAKey = $item->getInventoryCOAKey($detail['itemkey'],$warehousekey); 
            $totalItemValue =  $detail['costinbaseunit']*$detail['qtyinbaseunit']; 
            $arrItemCOA[$itemCOAKey] = (!isset($arrItemCOA[$itemCOAKey])) ? $totalItemValue : $arrItemCOA[$itemCOAKey] + $totalItemValue; 
        }
        
        $totalHPP = 0;
        foreach ($arrItemCOA as $coakey => $coaValue){ 
            $temp++;
            $arr['hidCOAKey'][$temp] = $coakey;
            $arr['debit'][$temp] = $coaValue; 
            $arr['credit'][$temp] = 0; 
            $arr['refCashBankKey'][$temp] = '';
            
            $totalHPP += $coaValue;
        }
        
        /*
        $rsCOA = $coaLink->getCOALink ('inventory', $warehouse->tableName,$warehousekey, 0);
        $temp++;
        $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
        $arr['debit'][$temp] = $totalHPP; 
        $arr['credit'][$temp] = 0;  */
         
		$costcoakey = 0;
		switch($rsHeader[0]['typekey']){
			case 1 : $costcoalink = 'lostgaininventory';
					 break;
			case 2 : $costcoalink = 'otherrevenue';
						break; 
			case 999 :  $costcoakey = $rsHeader[0]['coarevenuekey'];
						break;
			default : 	$costcoalink = 'lostgaininventory';
						break;
		}
		
		if(empty($costcoakey)){ 
        	$rsCOA = $coaLink->getCOALink ($costcoalink, $warehouse->tableName,$warehousekey, 0);
			$costcoakey =  $rsCOA[0]['coakey'];
		}
		
        $temp++;
        $arr['hidCOAKey'][$temp] = $costcoakey;
        $arr['debit'][$temp] = 0; 
        $arr['credit'][$temp] = $totalHPP;   

		$arrayToJs = $generalJournal->addData($arr);
        
		if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[504].' '.$arrayToJs[0]['message']); 
	 }
     
	 
	function cancelTrans($rsHeader,$copy){  
          
		$id = $rsHeader[0]['pkey'];
		  	 
		$itemMovement = new ItemMovement();  
		$itemMovement->cancelMovement($id,$this->tableName);
        
		$itemMovement->cancelSNMovement($id,$this->tableName);
        
        $salesOrderRentalWorkOrder = new SalesOrderRentalWorkOrder();
        $tableRentalWorkOrderKey = $this->getTableKeyAndObj($salesOrderRentalWorkOrder->tableName,array('key')); 
        if($rsHeader[0]['reftabletype']==$tableRentalWorkOrderKey['key'])
            $itemMovement->cancelMovementRental($id,$this->tableName);
		 
		if($this->isActiveModule('itemInReceive')){
			$itemInReceive = new ItemInReceive();
			$rsItemInReceive = $itemInReceive->searchData('','',true,' and '.$itemInReceive->tableName.'.refkey = '.$this->oDbCon->paramString($id).' and '.$itemInReceive->tableName.'.statuskey = 1');
			
			for($i=0;$i<count($rsItemInReceive);$i++) {
				$arrayToJs = $itemInReceive->changeStatus($rsItemInReceive[$i]['pkey'],4,'',false,true);
				if (!$arrayToJs[0]['valid'])
					throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.  $arrayToJs[0]['message']);    
			}	

		}
		
		
		if ($copy)
			$this->copyDataOnCancel($id);	  
        
        $this->cancelGLByRefkey($rsHeader[0]['pkey'],$this->tableName); 
	} 
    
    function getDetailWithRelatedInformation($pkey,$criteria=''){
		// item vendor part number dinonaktifkan dulu
		
//	   $sql = 'select
//	   			'.$this->tableNameDetail .'.*,
//                '.$this->tableNameDetail.'.qtyinbaseunit * '.$this->tableNameDetail.'.costinbaseunit as totalcogs, 
//                '.$this->tableItem.'.name as itemname, 
//                '.$this->tableItem.'.code as itemcode,
//                '.$this->tableVendorPartNumber.'.pkey as vendorpartnumberkey,
//                '.$this->tableVendorPartNumber.'.partnumber,
//                '.$this->tableItem.'.needsn,
//                '.$this->tableItemUnit.'.name as unitname,
//                baseunit.name as baseunitname,
//                concat(\' / \',baseunit.name) as cogsunit 
//			  from
//			  	'. $this->tableNameDetail .'
//                left join '.$this->tableVendorPartNumber.' on 
//                    '.$this->tableNameDetail .'.vendorpartnumberkey =  '.$this->tableVendorPartNumber.'.pkey ,
//                '.$this->tableItem.',
//                '.$this->tableItemUnit.',
//                '.$this->tableItemUnit.' baseunit
//			  where
//			  	' . $this->tableNameDetail .'.itemkey = '.$this->tableItem.'.pkey and
//			  	' . $this->tableNameDetail .'.unitkey = '.$this->tableItemUnit.'.pkey and
//			  	' . $this->tableItem .'.baseunitkey = baseunit.pkey and
//			  	'.$this->tableNameDetail .'.refkey IN ('.$this->oDbCon->paramString($pkey, ',').') ';
        
       		$sql = 'select
	   			'.$this->tableNameDetail .'.*,
                '.$this->tableNameDetail.'.qtyinbaseunit * '.$this->tableNameDetail.'.costinbaseunit as totalcogs, 
                '.$this->tableItem.'.name as itemname, 
                '.$this->tableItem.'.code as itemcode, 
                '.$this->tableItem.'.needsn,
                '.$this->tableItemUnit.'.name as unitname,
                baseunit.name as baseunitname,
                concat(\' / \',baseunit.name) as cogsunit 
			  from
			  	'. $this->tableNameDetail .',
                '.$this->tableItem.',
                '.$this->tableItemUnit.',
                '.$this->tableItemUnit.' baseunit
			  where
			  	' . $this->tableNameDetail .'.itemkey = '.$this->tableItem.'.pkey and
			  	' . $this->tableNameDetail .'.unitkey = '.$this->tableItemUnit.'.pkey and
			  	' . $this->tableItem .'.baseunitkey = baseunit.pkey and
			  	'.$this->tableNameDetail .'.refkey IN ('.$this->oDbCon->paramString($pkey, ',').') ';
        
        $sql .= $criteria;
        
		return $this->oDbCon->doQuery($sql);
	
   }
        
	function getDetailForAPI($arrKey, $arrIndex = array()){
        if(in_array('detail', $arrIndex)){
            $rsDetailsCol = array();
            $rsDetails = $this->getDetailWithRelatedInformation($arrKey); 
            $rsDetails = $this->reindexDetailCollections($rsDetails,'refkey'); 
            $rsDetailsCol['detail'] = $rsDetails;
        }
        
        return $rsDetailsCol;
    }
   
    function generateItemInReport($criteria='',$order='',$pkey='',$costkey = ''){
	   // gk bisa join langsung dengan Job Order atau SPK, karean tergantung tabletype
        
	   $sql =  '
			SELECT '.$this->tableName.'.code,
                   '.$this->tableName.'.trdate, 
                   '.$this->tableName.'.trdesc, 
                   '.$this->tableItem.'.name as itemcode,
                   '.$this->tableItem.'.name as itemname,
                   '.$this->tableNameDetail.'.qty, 
                   '.$this->tableNameDetail.'.costinbaseunit, 
                   '.$this->tableNameDetail.'.qtyinbaseunit * '.$this->tableNameDetail.'.costinbaseunit as totalcogs, 
                   '.$this->tableStatus.'.status as statusname , 
                   '.$this->tableWarehouse.'.name as warehousename , 
                   '.$this->tableItemUnit.'.name as unitname,
                   concat(\' / \',baseunit.name) as cogsunit 
			FROM 
                '.$this->tableStatus.',  
                '.$this->tableItem.', 
                '.$this->tableNameDetail.',
                '.$this->tableName.',
                '.$this->tableWarehouse.',
                '.$this->tableItemUnit.',
                '.$this->tableItemUnit.' baseunit
			WHERE     
                '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and 
                '.$this->tableNameDetail.'.refkey = '.$this->tableName.'.pkey and 
                '.$this->tableNameDetail .'.itemkey = '.$this->tableItem.'.pkey and 
                '.$this->tableNameDetail .'.unitkey = '.$this->tableItemUnit.'.pkey and
			  	'.$this->tableItem .'.baseunitkey = baseunit.pkey and
                '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey
 		'; 
        
        if (!empty($criteria))  
            $sql .=  ' ' .$criteria; 
        
        if (!empty($pkey))  
            $sql .=  '  and '.$this->tableName.'.pkey = ' .$this->oDbCon->paramString($pkey);
        
        if (!empty($costkey))  
            $sql .=  '  and '.$this->tableNameDetail.'.costkey = ' .$this->oDbCon->paramString($costkey); 
         
        if (!empty($order))  
            $sql .=  ' ' .$order; 
         
        //$this->setLog($sql);
       return $this->oDbCon->doQuery($sql);
		 
    } 
      
    function normalizeParameter($arrParam, $trim = false){

        //$this->setLog($arrParam,true);
        
        $arrParam['chkIsFullReceive'] = (isset($arrParam['chkIsFullReceive'])) ? $arrParam['chkIsFullReceive'] : 1;
         
         // recount convmultiplier
        $reCountResult = $this->reCalculateConversionDetail($arrParam); 
        for($i=0;$i<count($arrParam['hidItemKey']);$i++){
            $baseUnitKey =  $reCountResult['baseUnitKey'][$i]; 
            $unitConvMultiplier = $reCountResult['unitConvMultiplier'][$i]; 
            $qtyInBaseUnit = $reCountResult['qtyInBaseUnit'][$i]; 
            
            $arrParam['unitConvMultiplier'][$i] = $unitConvMultiplier;
            $arrParam['qtyInBaseUnit'][$i] = $qtyInBaseUnit; 
        }

        
		if($arrParam['selTransactionType'] != 999)
			$arrParam['hidCOARevenueKey'] = 0;
        
         
        /*
        if( $arrParam['chkIsFullReceive'] == 0){
            for($i=0;$i<count($arrParam['snList']);$i++)
                $arrParam['snList'][$i] = ''; 
        }
                 
        for($i=0;$i<count($arrParam['hidItemKey']);$i++){
            $arrParam['snList'][$i] = (isset($arrParam['snList'][$i]) && !empty($arrParam['snList'][$i])) ?  $arrParam['snList'][$i]: '';
        }*/        
         
        $arrParam = parent::normalizeParameter($arrParam, true);  
        return $arrParam;
    }
    
    
   /* function updateDetailTablesOnCopy($id,$newPkey, $arrTableDetail){ 
         
        for($k=0;$k<count($arrTableDetail);$k++){
            $rsDetail = $this->getDetailById($id,'','',$arrTableDetail[$k]);

            $sql = 'show columns from ' . $arrTableDetail[$k] ;   
            $rsColumnsName = $this->oDbCon->doQuery ($sql); 

            for ($j=0;$j<count($rsDetail);$j++){
                $fields = '';
                $data = ''; 
                $oldDetailKey = $rsDetail[$j]['pkey'];
                
                if ($arrTableDetail[$k] == $this->tableNameDetail)  
                     $rsDetail[$j]['pkey'] = $this->getNextKey($this->tableNameDetail);  
              
                $rsDetail[$j]['refkey'] = $newPkey; 
                
                for ($i=1;$i<count($rsColumnsName);$i++){

                    $fields .= $rsColumnsName[$i]['Field'];  
                    $data .=   $this->oDbCon->paramString($rsDetail[$j][$rsColumnsName[$i]['Field']]);

                    if ($i <> count($rsColumnsName) - 1){
                      $data .= ',';   
                      $fields.= ',';    
                    }

                }

                $sql = 'insert into ' .$arrTableDetail[$k].'  ('.$fields.') values ('.$data.')'; 
                $this->oDbCon->execute ($sql);	
                
  
                // ============= update detail Package
                
                $rsItemDetail = $this->getSerialNumber($oldDetailKey);
                $sql = 'show columns from ' . $this->tableSerial;   
                $rsDetailsColumnsName = $this->oDbCon->doQuery($sql); 
                 
               for ($z=0;$z<count($rsItemDetail);$z++){
                    $fields = '';
                    $data = ''; 

                    for ($i=1;$i<count($rsDetailsColumnsName);$i++){

                        $fields .= $rsDetailsColumnsName[$i]['Field'];

                        $rsItemDetail[$z]['refheaderkey'] = $newPkey;
                        $rsItemDetail[$z]['refkey'] = $rsDetail[$j]['pkey']; 

                        $data .= $this->oDbCon->paramString($rsItemDetail[$z][$rsDetailsColumnsName[$i]['Field']]);

                        if ($i <> count($rsDetailsColumnsName) - 1){
                          $data .= ',';   
                          $fields.= ',';    
                        }

                    }

                    $sql = 'insert into ' .$this->tableSerial.'  ('.$fields.') values ('.$data.')';  
                    $this->oDbCon->execute ($sql);	 
               }
                
                
                // ============= end update detail Package
                
            }  
        }  
        
    }*/
    
    
    function updateItemInReceivedItem($pkey){ 
            $itemInReceive = new ItemInReceive();
            $rsHeader = $this->getDataRowById($pkey);  
            $rsDetail = $this->getDetailById($pkey); 

            for($i=0;$i<count($rsDetail); $i++){	
                $sql = 'select 
                        coalesce(sum(receivedqtyinbaseunit),0) as totalreceivedqtyinbaseunit
                    from 
                        '. $itemInReceive->tableName . ', '. $itemInReceive->tableNameDetail . '
                    where 
                         '. $itemInReceive->tableName . '.pkey = '. $itemInReceive->tableNameDetail . '.refkey and
                         '. $itemInReceive->tableName . '.refkey = '. $this->oDbCon->paramString($pkey) .' and
                         '. $itemInReceive->tableNameDetail . '.itemkey = ' . $rsDetail[$i]['itemkey'] .' and 
                         '. $itemInReceive->tableNameDetail . '.refitemindetailkey = ' . $rsDetail[$i]['pkey'] .' and 
                         (statuskey = 2 or statuskey = 3)';

                $rsTotal = $this->oDbCon->doQuery($sql);

                $sql = 'update 
                            ' . $this->tableNameDetail.' 
                        set  
                            receivedqtyinbaseunit = '. $rsTotal[0]['totalreceivedqtyinbaseunit'].' 
                        where 
                            refkey = '.$pkey.' and 
                            pkey = '.$rsDetail[$i]['pkey'].' and 
                            itemkey = ' . $rsDetail[$i]['itemkey'];
                $this->oDbCon->execute($sql); 
            }

            //check if all item received, change PO status to finish
            $sql = 'select * from ' . $this->tableNameDetail.' where refkey = '.$this->oDbCon->paramString($pkey).' and  receivedqtyinbaseunit < qtyinbaseunit';
            $rs = $this->oDbCon->doQuery($sql);

            $statuskey = (empty($rs)) ? 3 : 2;
 
            if ($rsHeader[0]['statuskey'] <> $statuskey)
                $this->changeStatus($pkey,$statuskey,'',false,true); // buat otomatis agar validasi security dilewatin
 
      
    } 
		
	function getTransactionType (){
		$sql = 'select * from '.$this->tableType;
		return $this->oDbCon->doQuery($sql); 
	}
}
?>
