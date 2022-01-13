<?php
  
class ItemOutDelivery extends BaseClass{  
 
    function __construct(){

        parent::__construct(); 

        $this->tableName = 'item_out_delivery_header';
        $this->tableNameDetail = 'item_out_delivery_detail';
        $this->tableSerial = 'item_out_delivery_detail_sn';
        $this->tableItemOutHeader = 'item_out_header';   
        $this->tableItemOutDetail = 'item_out_detail';
        $this->tableVendorPartNumber = 'item_vendor_part_number';
        $this->tableStatus = 'transaction_status';
        $this->tableMovement = 'item_movement'; 
        $this->tableItemSN = 'item_sn';
        //$this->tableCustomer = 'customer';
        $this->tableHistory = 'history';
        $this->tableItem = 'item'; 	
        $this->tableItemUnit = 'item_unit'; 	
        $this->tableWarehouse = 'warehouse'; 	
		$this->isTransaction = true; 	

        $this->tableNeedToBeCopyOnCancel = array($this->tableNameDetail,$this->tableSerial);

        $this->securityObject = 'ItemOutDelivery'; 
        
        /*$this->arrSN = array();
        $this->arrSN['pkey'] = array('hidDetailSNKey');
        $this->arrSN['refkey'] = array('hidDetailKey','ref');
        $this->arrSN['refheaderkey'] = array('pkey','ref');
        $this->arrSN['serialnumber'] = array('serialNumber');
        
        $this->arrDataDetail = array();  
        $this->arrDataDetail['pkey'] = array('hidDetailKey', array('dataDetail' => array('dataset' => $this->tableSerial)));
        $this->arrDataDetail['refkey'] = array('pkey','ref');
        $this->arrDataDetail['refitemoutdetailkey'] = array('hidItemOutDetailKey');
        $this->arrDataDetail['itemkey'] = array('hidItemKey');
        $this->arrDataDetail['orderedqty'] = array('orderedQty','number');
        $this->arrDataDetail['qtyminus'] = array('qtyMinus','number');
        $this->arrDataDetail['deliveredqty'] = array('deliveredQty','number');
        $this->arrDataDetail['deliveredqtyinbaseunit'] = array('deliveredqtyinbaseunit','number');
        $this->arrDataDetail['unitkey'] = array('unitkey');
        $this->arrDataDetail['unitconvmultiplier'] = array('unitconvmultiplier','number');
        $this->arrDataDetail['costinbaseunit'] = array('costinbaseunit','number');
        $this->arrDataDetail['vendorpartnumberkey'] = array('hidVendorPartNumberKey');*/
        
        $this->arrData = array(); 
        //$this->arrData['pkey'] = array('pkey', array('dataDetail' => array('dataset' => $this->arrDataDetail)));
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['code'] = array('code');
        $this->arrData['trdate'] = array('trDate','date');
        $this->arrData['refkey'] = array('hidItemOutKey');
        $this->arrData['warehousekey'] = array('selWarehouse'); 
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['shippingcourierkey'] = array('hidSupplierKey');
        $this->arrData['shippingreceipt'] = array('shippingReceipt');
        //$this->arrData['balance'] = array('balance','number'); 
        $this->arrData['statuskey'] = array('selStatus');
        
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'itemoutcode','title' => 'itemOutCode','dbfield' => 'itemoutcode','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'customer','title' => 'customer','dbfield' => 'customername','default'=>true,'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename','default'=>true,'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'note','title' => 'note','dbfield' => 'trdesc','width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
        
       
        $this->overwriteConfig();
        
    }

    function getQuery(){

       return '
            SELECT 
                 '.$this->tableName.'.* , 
                 '.$this->tableItemOutHeader.'.code as itemoutcode, 
                 '.$this->tableItemOutHeader.'.invoicenumber as refcode,  
                 '.$this->tableStatus.'.status as statusname,
                 '.$this->tableWarehouse.'.name as warehousename
            FROM 
                '.$this->tableStatus.', 
                '.$this->tableName.', 
                '.$this->tableItemOutHeader.' , 
                '.$this->tableWarehouse.'
            WHERE 
                  '.$this->tableName.'.refkey = '.$this->tableItemOutHeader.'.pkey and
                  '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey and
                  '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey  
        ' .$this->criteria ; 
        //$this->setLog($sql);

    }
    
    function afterUpdateData($arrParam, $action){  
        $this->updateDetail($arrParam); 
    }
    
     
    function updateDetail( $arrParam){
		$pkey = $arrParam['pkey'];
        $item = new Item();
        
	 	$sql = 'delete from '.$this->tableNameDetail.' where refkey = '. $this->oDbCon->paramString($pkey);
		$this->oDbCon->execute($sql);
		 
	 	$sql = 'delete from '.$this->tableSerial.' where refheaderkey = '. $this->oDbCon->paramString($pkey);
		$this->oDbCon->execute($sql);
        
		$arrItemOutKey = $arrParam['hidItemOutDetailKey']; 
		$arrItemkey = $arrParam['hidItemKey'];  
        
		$arrOrderedQtyInBaseUnit = $arrParam['orderedQtyInBaseUnit']; 
		//$arrDeliveredQty = $arrParam['deliveredQty']; 
		$arrDeliveredQtyInBase = $arrParam['deliveredQtyInBaseUnit']; 
		$arrQtyMinusInBaseUnit = $arrParam['qtyMinusInBaseUnit'];
	 	$arrSerialNumber = $arrParam['snList'];
		$arrCOGS = $arrParam['costinbaseunit']; 
        
		$arrVendorPartNumberKey = (isset($arrParam['hidVendorPartNumberKey'])) ? $arrParam['hidVendorPartNumberKey'] : array();  
        
        $item = new Item();
		        
     	for ($i=0;$i<count($arrItemkey);$i++){
			 
			if (empty($arrItemkey[$i]))
				continue;
				 
			$detailkey = $this->getNextKey($this->tableNameDetail);         
            $arrSNItem =  isset($arrSerialNumber[$i]) ? preg_split('/[\n, ]+/', $arrSerialNumber[$i]) : array();
		 	$qtyDeliveredInBaseUnit =  $this->unFormatNumber($arrDeliveredQtyInBase[$i]);
		 	//$qtyDelivered =  $this->unFormatNumber($arrDeliveredQty[$i]);
		 	$qtyMinusInBaseUnit =  $this->unFormatNumber($arrQtyMinusInBaseUnit[$i]);
			$costinbaseunit = $this->unFormatNumber($arrCOGS[$i]);
		    $orderedQtyInBaseUnit =  $this->unFormatNumber($arrOrderedQtyInBaseUnit[$i]);
            $vendorPartNumber = (isset($arrVendorPartNumberKey[$i]) && !empty($arrVendorPartNumberKey[$i])) ? $arrVendorPartNumberKey[$i] : 0;
            
            $rsItem = $item->getDataRowById($arrItemkey[$i]);
            $baseunitkey = $rsItem[0]['baseunitkey'];
            
			$sql = 'insert into '.$this->tableNameDetail.' (
                        pkey,
						refkey,
						refitemoutdetailkey,
						itemkey,
						orderedqtyinbaseunit,  
						qtyminusinbaseunit, 
						deliveredqtyinbaseunit,
						baseunitkey,  
						costinbaseunit,
                        vendorpartnumberkey
					 ) values (
						'.$this->oDbCon->paramString($detailkey).',
						'.$this->oDbCon->paramString($pkey).',
						'.$this->oDbCon->paramString($arrItemOutKey[$i]).',
						'.$this->oDbCon->paramString($arrItemkey[$i]).',
						'.$this->oDbCon->paramString($orderedQtyInBaseUnit).',
						'.$this->oDbCon->paramString($qtyMinusInBaseUnit).',
						'.$this->oDbCon->paramString($qtyDeliveredInBaseUnit).',
						'.$this->oDbCon->paramString($baseunitkey).',
						'.$this->oDbCon->paramString($costinbaseunit).',
                        '.$this->oDbCon->paramString($vendorPartNumber).' 
					)';	 
            
			$this->oDbCon->execute($sql);
                      
                for($j=0;$j<count($arrSNItem);$j++){
                     
                    $sn = preg_replace("/[^A-Za-z0-9]/", '', $arrSNItem[$j]); 
                    if (empty($sn)) continue;

                    $sql = 'insert into '.$this->tableSerial.' (
                            refkey,
                            refheaderkey,
                            serialnumber 
                         ) values (
                            '.$this->oDbCon->paramString($detailkey).',
                            '.$this->oDbCon->paramString($pkey).',
                            '.$this->oDbCon->paramString($sn).'
                        )';
                        $this->oDbCon->execute($sql);
                }                   
		} 
	}

    
     function validateForm($arr,$pkey = ''){
        $item = new Item();  
        $itemOut = new ItemOut();

        $arrayToJs = parent::validateForm($arr,$pkey); 

        $itemOutKey = $arr['hidItemOutKey']; 
        $arrItemkey = $arr['hidItemKey']; 
        $arrOrderedQty = $arr['orderedQtyInBaseUnit']; 
        $arrQtyMinus = $arr['qtyMinusInBaseUnit']; 
        $arrDeliveredQty = $arr['deliveredQtyInBaseUnit']; 
         
        $showVendorPartNumber = $this->loadSetting('showVendorPartNumber');
         
         if($showVendorPartNumber)
            $arrVendorPartNumberKey = $arr['hidVendorPartNumberKey'];

        //validasi kalo status gk menunggu gk bisa edit 
        if (!empty($pkey)){
            $rs = $this->getDataRowById($pkey);
            if ($rs[0]['statuskey'] <> 1){
                $this->addErrorList($arrayToJs,false,$this->errorMsg[212]);
            }
        } 

        if(empty($itemOutKey)){
            $this->addErrorList($arrayToJs,false,$this->errorMsg['itemOut'][1]);
        }else{
             $rsItemOut = $itemOut->getDataRowById($itemOutKey); 
             $deliveredate = strtotime(str_replace('\'','',$this->oDbCon->paramDate($arr['trDate'],' / ','Y-m-d')));
             $itemOutdate =  strtotime($rsItemOut[0]['trdate']);

            //$this->setLog($deliveredate .' < '. $itemOutdate);
            
            if ($deliveredate < $itemOutdate)
             $this->addErrorList($arrayToJs,false, $this->errorMsg['itemOutDelivery'][2]);

        } 

        $arrDetailKeys = array(); 
        for($i=0;$i<count($arrItemkey);$i++) { 
            $rsItem = $item->getDataRowById($arrItemkey[$i]);
            
            if (empty($arrItemkey[$i]) ){ 
                $this->addErrorList($arrayToJs,false, $this->errorMsg['item'][1]); 	
            }else{
                
                if ($this->unFormatNumber($arrDeliveredQty[$i]) <= 0){
                    $this->addErrorList($arrayToJs,false,$rsItem[0]['name']. '. ' . $this->errorMsg[500]); 
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
                    }
                    /*else{
                            $this->addErrorList($arrayToJs,false,$rsItem[0]['name']. '. ' . $this->errorMsg['vendorPartNumber'][1]); 
                    }*/
                }else{
                    if (in_array($arrItemkey[$i],$arrDetailKeys)){  
                        $this->addErrorList($arrayToJs,false, $rsItem[0]['name'].'. '.$this->errorMsg[215]); 	 
                    }else{ 
                        array_push($arrDetailKeys, $arrItemkey[$i]);
                    }   
                }  
            }  
            
            
            if (!empty($arrItemkey[$i]) &&  $this->unFormatNumber($arrDeliveredQty[$i]) > $this->unFormatNumber($arrQtyMinus[$i]) ){
                $rsItem = $item->getDataRowById($arrItemkey[$i]);
                $this->addErrorList($arrayToJs,false,$rsItem[0]['name']. '. ' . $this->errorMsg['purchaseReceive'][1]); 
            }
            
        }
          
        return $arrayToJs;
     }
 

    function afterStatusChanged($rsHeader){ 
        $itemOut = new ItemOut();
        $itemOut->updateItemOutDeliveredItem($rsHeader[0]['refkey']);
         
        // retrieve latest status
        $rsHeader = $this->getDataRowById($rsHeader[0]['pkey']);
        if ($rsHeader[0]['statuskey'] == 2)
            $this->changeStatus($rsHeader[0]['pkey'],3); 
    }
  
  
    function validateConfirm($rsHeader){
        $id = $rsHeader[0]['pkey'];
        
        $itemOut = new ItemOut();
        $warehouse = new Warehouse();
        $itemMovement = new itemMovement();
        $item = new Item();
        //$coaLink = new COALink();
 
        $rsDetail = $this->getDetailById($id);

        // cek sudah diterima full blm
        for($i=0;$i<count($rsDetail);$i++){ 
            $qtyMinus = $rsDetail[$i]['qtyminusinbaseunit']; 

            $rsItemOut = $itemOut->getDetailById($rsHeader[0]['refkey'],' and itemkey = ' . $this->oDbCon->paramString($rsDetail[$i]['itemkey']) );
            //$this->setLog($rsItemOut[0]['qty'].' - '.$rsItemOut[0]['deliveredqty'].' <> '.$qtyMinus);
            if ($rsItemOut[0]['qty'] - $rsItemOut[0]['deliveredqtyinbaseunit'] <> $qtyMinus ) 
                   $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['salesDelivery'][3]);
            
            $saldoakhir = $itemMovement->getItemQOH($rsDetail[$i]['itemkey'], $rsHeader[0]['warehousekey']);  
            $totalqty = $saldoakhir - $rsDetail[$i]['deliveredqtyinbaseunit'];
            if($totalqty < 0){ 
                $rsItem = $item->getDataRowById($rsDetail[$i]['itemkey']);
                $this->addErrorLog(false,'<strong>'.$rsItem[0]['name'].'</strong>. '.$this->errorMsg[402]);
            }else{ 

                if(USE_SN){ 
                    $rsItem = $item->getDataRowById($rsDetail[$i]['itemkey']); 
                    if(!$rsItem[0]['needsn'])
                        continue;

                    $rsSN = $this->getSerialNumber($rsDetail[$i]['pkey']); 

                    if($rsDetail[$i]['deliveredqtyinbaseunit'] <> count($rsSN))
                        $this->addErrorLog(false, $this->errorMsg['serialnumber'][2]); 

                    //validasi SN
                    for($j=0;$j<count($rsSN); $j++){
                        $saldoakhir = $itemMovement->getItemSNQOH($rsDetail[$i]['itemkey'], $rsSN[$j]['serialnumber'], $rsHeader[0]['warehousekey']); 
                        $totalqty = $saldoakhir - 1;
                        if($totalqty<0) 
                            $this->addErrorLog(false,'<strong>'.$rsItem[0]['name'].', '. $rsSN[$j]['serialnumber'].'</strong>. '.$this->errorMsg[402]);

                    }  
                } 
            }

        } 
         
     }
 
    function confirmTrans($rsHeader){
        $id = $rsHeader[0]['pkey'];
        
        $rsHeader = $this->getDataRowById($id); 
        $warehouse = new Warehouse(); 
        $itemOut = new ItemOut();
        $coaLink = new COALink(); 
        $warrantyPeriod = new WarrantyPeriod();
        $item = new Item();
        $itemMovement = new ItemMovement();  
        
        $rsItemOutHeader = $itemOut->getDataRowById($rsHeader[0]['refkey']); 

        $rsWarehouse = $warehouse->getDataRowById($rsHeader[0]['warehousekey']);

        $arrNote = array();
        array_push($arrNote, $rsHeader[0]['code']);
        array_push($arrNote, $this->ucFirst($this->lang['itemOutDelivery']. ' ' .$this->lang['for']) . ' '.$rsItemOutHeader[0]['code']); 
        $note = implode('. ',$arrNote).'.'; 
          
        $rsDetail = $this->getDetailById($rsHeader[0]['pkey']);
 
        if(USE_SN){
            $rsWarranty = $warrantyPeriod->searchData();
            $rsWarranty = array_column($rsWarranty,'period', 'pkey');
            $rsWarranty[0] = 0; // default kalo gk ad warranty
        }
        
       
        for($i=0;$i<count($rsDetail); $i++){	
            
           $rsItem = $item->getDataRowById($rsDetail[$i]['itemkey']);
            
           if ($rsDetail[$i]['deliveredqtyinbaseunit'] != 0)
            $itemMovement->updateItemMovement($id,$rsDetail[$i]['itemkey'],-$rsDetail[$i]['deliveredqtyinbaseunit'],$rsDetail[$i]['costinbaseunit'],$this->tableName, $rsHeader[0]['warehousekey'], $note,$rsHeader[0]['trdate']);
            if(USE_SN){
                 // set warranty enddate
                $warrantyMonth = $rsWarranty[$rsItem[0]['warrantyperiodkey']];

                $date = new DateTime($rsHeader[0]['trdate']);
                $date->add(new DateInterval('P'.$warrantyMonth.'M'));
                $warrantyEndDate = $date->format('Y-m-d'); 
                
               $rsSN = $this->getSerialNumber($rsDetail[$i]['pkey']); 
               for($j=0;$j<count($rsSN); $j++){  
                        $rsDetail[$i]['vendorpartnumberkey'] = $rsSN[$j]['vendorpartnumberkey'];
                        $itemMovement->updateItemSNMovement( 
                                array(
                                'refkey' => $rsDetail[$i]['pkey'],
                                'refheaderkey' => $id,
                                'itemkey' => $rsDetail[$i]['itemkey'],
                                'vendorpartnumberkey' => $rsDetail[$i]['vendorpartnumberkey'],
                                'sn' => $rsSN[$j]['serialnumber'],
                                'qtyinbaseunit' => -1,
                                'costinbaseunit' => $rsDetail[$i]['costinbaseunit'],
                                'reftable' => $this->tableName,
                                'warehousekey' => $rsHeader[0]['warehousekey'],
                                'note' => $note,
                                'trdate' => $rsHeader[0]['trdate'] ,
                                'warrantyperiodkey' => $rsItem[$i]['warrantyperiodkey'],
                                'warrantyperiodtime' => $warrantyMonth,
                                'warrantyperiodexpireddate' => $warrantyEndDate
                       )); 
               }
            }
        }	 


        //update jurnal umum 
        $this->updateGL($rsHeader);
    } 

    function updateGL($rsHeader){
        if (!USE_GL) return;

         $warehouse = new Warehouse();
         $coaLink = new COALink();
         $item = new Item();
         $generalJournal = new GeneralJournal();
         $rsKey = $generalJournal->getTableKeyAndObj($this->tableName);

         $arr = array();
         $arr['pkey'] = $generalJournal->getNextKey($generalJournal->tableName);
         $arr['code'] = 'xxxxx';
         $arr['refkey'] = $rsHeader[0]['pkey'];
         $arr['refTableType'] = $rsKey['key'];
         $arr['trDate'] =  $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y'); 
         $arr['createdBy'] = 0; 
		 $arr['selWarehouseKey'] = $rs[0]['warehousekey'];

        $totalHPP = 0 ;
        $rsDetail = $this->getDetailById($rsHeader[0]['pkey']); 
        for($i=0;$i<count($rsDetail);$i++)
            $totalHPP += ( $rsDetail[$i]['costinbaseunit']  * $rsDetail[$i]['qty']);

        $temp = -1;

        $rsCOA = $coaLink->getCOALink ('hpp', $warehouse->tableName,$rsHeader[0]['warehousekey'], 0);
        $temp++;
        $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
        $arr['debit'][$temp] = $totalHPP; 
        $arr['credit'][$temp] = 0;  

        $rsCOA = $coaLink->getCOALink ('inventory', $warehouse->tableName,$rsHeader[0]['warehousekey'], 0);
        $temp++;
        $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
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


        if ($copy)
            $this->copyDataOnCancel($id);	  

        $this->cancelGLByRefkey($rsHeader[0]['pkey'],$this->tableName);
    } 

    function validateCancel($rsHeader,$autoChangeStatus=false){ 
        $id = $rsHeader[0]['pkey']; 
     }

    function reCalculateDetail($arrParam){

                $item = new Item();
                $itemOut = new ItemOut();

                // default, ongkir dan cost dibagi berdasarkan proporsional gramasi/kubikasi
                $useGramasi = $this->loadSetting('costProportionalType');
  
                $rsItemOutHeader = $itemOut->getDataRowById($arrParam['hidItemOutKey']);

                $arrDeliveredQtyInBaseUnit = $arrParam['deliveredQtyInBaseUnit'];  
                $arrItem = $arrParam['hidItemKey'];  
                $arrItemOutKey = $arrParam['hidItemOutDetailKey']; 

                $gramasi = 0;
                $subtotal = 0 ;

                $arrItemDetail = array();
                for($i=0;$i<count($arrItem);$i++){ 
                    $itemkey = $arrItem[$i];  
                    $rsItem = $item->getDataRowById($itemkey); 
                    
                    $rsItemOutDetail =  $itemOut->getDetailByColumn('pkey',$arrItemOutKey[$i]); 

                    $arrItemDetail[$i]['baseunitkey'] = $rsItem[0]['baseunitkey'];
                    $arrItemDetail[$i]['costinbaseunit'] = $rsItemOutDetail[0]['costinbaseunit']; 
                 
                }


 
                $reCalculateResult = array();

                $reCalculateResult['detailItem'] = $arrItemDetail; 

                return $reCalculateResult;

    }

    function getDetailWithRelatedInformation($pkey,$criteria = ''){

      $sql = 'select
                '.$this->tableNameDetail .'.*,
                '.$this->tableItem.'.name as itemname, 
                '.$this->tableItem.'.code as itemcode,
                '.$this->tableItem.'.needsn,
                '.$this->tableVendorPartNumber.'.pkey as vendorpartnumberkey,
                '.$this->tableVendorPartNumber.'.partnumber,
                baseunit.name as baseunitname
              from
                '. $this->tableNameDetail .'
                left join '.$this->tableVendorPartNumber.' on 
                    '.$this->tableNameDetail .'.vendorpartnumberkey =  '.$this->tableVendorPartNumber.'.pkey ,
                '.$this->tableItem.',
                '.$this->tableItemUnit.' baseunit
              where
                ' . $this->tableNameDetail .'.itemkey = '.$this->tableItem.'.pkey and
                ' . $this->tableItem .'.baseunitkey = baseunit.pkey and
                ' . $this->tableNameDetail .'.refkey = '.$this->oDbCon->paramString($pkey);

        $sql .= $criteria;
        
        //$this->setLog($sql);

        return $this->oDbCon->doQuery($sql);

    }
    
    function normalizeParameter($arrParam, $trim = false){
            $item = new Item();
            $itemOut = new ItemOut();
        
            $arrParam = parent::normalizeParameter($arrParam); 
         
            $arrItemkey = $arrParam['hidItemKey'];
            
            $rsItemOut = $itemOut->getDataRowById($arrParam['hidItemOutKey']);
            $reCalculateResult = $this->reCalculateDetail($arrParam);  
            $arrParam['selWarehouse'] = $rsItemOut[0]['warehousekey'];
            $arrParam['hidCustomerKey'] = $rsItemOut[0]['customerkey']; 
         
            $arrParam['snList'] = (isset($arrParam['snList'])) ? $arrParam['snList'] : array();
            foreach($arrParam['snList'] as $key=>$row)
                $arrParam['snList'][$key] = implode(',',$item->normalizeSNList($row));
            
            $arrParam['hidSupplierKey'] = (isset($arrParam['hidSupplierKey'])) ? $arrParam['hidSupplierKey'] : 0;
            $arrParam['shippingReceipt'] = (isset($arrParam['shippingReceipt'])) ? $arrParam['shippingReceipt'] : ''; 
        
            for ($i=0;$i<count($arrItemkey);$i++){   
                $arrParam['baseunitkey'][$i] = $reCalculateResult['detailItem'][$i]['baseunitkey']; 
                $arrParam['costinbaseunit'][$i] = $reCalculateResult['detailItem'][$i]['costinbaseunit'];
            }
 
            return $arrParam;
    }
    
   function getSerialNumber($refkey){
        $sql = 'select 
                    '.$this->tableSerial.'.* ,
                    '.$this->tableItemSN.'.vendorpartnumberkey
                from 
                    '.$this->tableSerial.' 
                    left join '.$this->tableItemSN.' on '.$this->tableSerial.'.serialnumber = '.$this->tableItemSN.'.serialnumber
                where 
                    '.$this->tableSerial.'.refkey = ' . $this->oDbCon->paramString($refkey);
        return $this->oDbCon->doQuery($sql);
    }

    
    function updateDetailTablesOnCopy($id,$newPkey, $arrTableDetail){ 
         
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
        
    }
     
}
?>
