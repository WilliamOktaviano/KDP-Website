<?php
  
class ItemInReceive extends BaseClass{  
 
    function __construct(){

        parent::__construct(); 

        $this->tableName = 'item_in_receive_header';
        $this->tableNameDetail = 'item_in_receive_detail';
        $this->tableSerial = 'item_in_receive_detail_sn';
        $this->tableItemInHeader = 'item_in_header';   
        $this->tableItemInDetail = 'item_in_detail';
        $this->tableVendorPartNumber = 'item_vendor_part_number';
        $this->tableStatus = 'transaction_status';
        $this->tableMovement = 'item_movement'; 
        $this->tableSupplier = 'supplier';
        $this->tableHistory = 'history';
        $this->tableItem = 'item'; 	
        $this->tableItemUnit = 'item_unit'; 	
        $this->tableWarehouse = 'warehouse'; 	
		$this->isTransaction = true; 	

        $this->tableNeedToBeCopyOnCancel = array($this->tableNameDetail);

        $this->securityObject = 'ItemInReceive'; 
        
  
        /*$this->arrSN = array();
        $this->arrSN['pkey'] = array('hidDetailSNKey');
        $this->arrSN['refkey'] = array('hidDetailKey','ref');
        $this->arrSN['refheaderkey'] = array('pkey','ref');
        $this->arrSN['serialnumber'] = array('serialNumber');
        
        $this->arrDataDetail = array();  
        $this->arrDataDetail['pkey'] = array('hidDetailKey', array('dataDetail' => array('dataset' => $this->tableSerial)));
        $this->arrDataDetail['refkey'] = array('pkey','ref');
        $this->arrDataDetail['refitemindetailkey'] = array('hidItemInDetailKey');
        $this->arrDataDetail['itemkey'] = array('hidItemKey');
        $this->arrDataDetail['vendorpartnumberkey'] = array('hidVendorPartNumberKey');
        $this->arrDataDetail['orderedqty'] = array('orderedQty','number');
        $this->arrDataDetail['qtyminus'] = array('qtyMinus','number');
        $this->arrDataDetail['receivedqty'] = array('receivedQty','number');
        $this->arrDataDetail['receivedqtyinbaseunit'] = array('receivedqtyinbaseunit','number');
        $this->arrDataDetail['unitkey'] = array('unitkey');
        $this->arrDataDetail['unitconvmultiplier'] = array('unitconvmultiplier','number');
        $this->arrDataDetail['costinbaseunit'] = array('costinbaseunit','number');*/
       
        $this->arrData = array(); 
        //$this->arrData['pkey'] = array('pkey', array('dataDetail' => array('dataset' => $this->arrDataDetail)));
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['code'] = array('code');
        $this->arrData['trdate'] = array('trDate','date');
        $this->arrData['trinvoicedate'] = array('trInvoiceDate','date');
        $this->arrData['refkey'] = array('hidItemInKey');
        $this->arrData['warehousekey'] = array('selWarehouse');
        $this->arrData['supplierkey'] = array('hidSupplierKey');
        $this->arrData['trdesc'] = array('trDesc');
        //$this->arrData['balance'] = array('balance','number'); 
        $this->arrData['statuskey'] = array('selStatus');
        
    }

    function getQuery(){

       return '
            SELECT 
                 '.$this->tableName.'.* , 
                 '.$this->tableItemInHeader.'.code as itemincode, 
                 '.$this->tableItemInHeader.'.refcode, 
                 '.$this->tableSupplier.'.name as suppliername,
                 '.$this->tableStatus.'.status as statusname,
                 '.$this->tableWarehouse.'.name as warehousename
            FROM 
                '.$this->tableStatus.', 
                '.$this->tableName.' , 
                '.$this->tableItemInHeader.'
                    left join '.$this->tableSupplier.' on
                      '.$this->tableItemInHeader.'.supplierkey = '.$this->tableSupplier.'.pkey , 
                '.$this->tableWarehouse.'
            WHERE 
                  '.$this->tableName.'.refkey = '.$this->tableItemInHeader.'.pkey and
                  '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey and 
                  '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey  
        ' .$this->criteria ;  

    }
    
    function afterUpdateData($arrParam, $action){  
        $this->updateDetail($arrParam); 
    }
    
    function updateDetail( $arrParam){
        $item = new Item();
		$pkey = $arrParam['pkey'];
         
	 	$sql = 'delete from '.$this->tableNameDetail.' where refkey = '. $this->oDbCon->paramString($pkey);
		$this->oDbCon->execute($sql);
		 
	 	$sql = 'delete from '.$this->tableSerial.' where refheaderkey = '. $this->oDbCon->paramString($pkey);
		$this->oDbCon->execute($sql);
        
		$arrItemInkey = $arrParam['hidItemInDetailKey']; 
		$arrItemkey = $arrParam['hidItemKey'];  
		$arrOrderedQtyInBaseUnit = $arrParam['orderedQtyInBaseUnit'];  
		$arrReceivedQtyInBase = $arrParam['receivedQtyInBaseUnit']; 
		$arrQtyMinusInBaseUnit = $arrParam['qtyMinusInBaseUnit']; 
	 	$arrSerialNumber = $arrParam['snList'];
		$arrCOGS = $arrParam['costinbaseunit']; 
        
		$arrVendorPartNumberKey = (isset($arrParam['hidVendorPartNumberKey'])) ? $arrParam['hidVendorPartNumberKey'] : array();  
         
        for ($i=0;$i<count($arrItemkey);$i++){
			 
			if (empty($arrItemkey[$i]))
				continue;
				 
			$detailkey = $this->getNextKey($this->tableNameDetail);        
            $arrSNItem =  isset($arrSerialNumber[$i]) ? preg_split('/[\n, ]+/', $arrSerialNumber[$i]) : array(); 
		 	$qtyReceivedInBaseUnit =  $this->unFormatNumber($arrReceivedQtyInBase[$i]);
		 	$qtyMinusInBaseUnit =  $this->unFormatNumber($arrQtyMinusInBaseUnit[$i]);
		 	$orderedQtyInBaseUnit =  $this->unFormatNumber($arrOrderedQtyInBaseUnit[$i]);
			$costinbaseunit = $this->unFormatNumber($arrCOGS[$i]); 
            $vendorPartNumber = (isset($arrVendorPartNumberKey[$i])) ? $arrVendorPartNumberKey[$i] : '';
          
            $rsItem = $item->getDataRowById($arrItemkey[$i]);
            $baseunitkey = $rsItem[0]['baseunitkey'];
                
			$sql = 'insert into '.$this->tableNameDetail.' (
                        pkey,
						refkey,
						refitemindetailkey,
						itemkey, 
						orderedqtyinbaseunit,  
						qtyminusinbaseunit,   
						receivedqtyinbaseunit,   
						baseunitkey,   
						costinbaseunit,
                        vendorpartnumberkey
					 ) values (
						'.$this->oDbCon->paramString($detailkey).',
						'.$this->oDbCon->paramString($pkey).',
						'.$this->oDbCon->paramString($arrItemInkey[$i]).',
						'.$this->oDbCon->paramString($arrItemkey[$i]).', 
						'.$this->oDbCon->paramString($orderedQtyInBaseUnit).',
						'.$this->oDbCon->paramString($qtyMinusInBaseUnit).', 
						'.$this->oDbCon->paramString($qtyReceivedInBaseUnit).', 
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
                            '.$this->oDbCon->paramString(strtoupper($sn)).'
                        )';
                        $this->oDbCon->execute($sql);
                }                   
		} 
	}


    
     function validateForm($arr,$pkey = ''){
        $item = new Item();  
        $itemIn = new itemIn();

        $showVendorPartNumber = $this->loadSetting('showVendorPartNumber');
         
        $arrayToJs = parent::validateForm($arr,$pkey); 

        $itemInKey = $arr['hidItemInKey']; 
        $arrItemkey = $arr['hidItemKey'];   
        $arrReceivedQtyInBaseUnit = $arr['receivedQtyInBaseUnit']; 
        $arrQtyMinusInBaseUnit = $arr['qtyMinusInBaseUnit']; 
        if($showVendorPartNumber)
            $arrVendorPartNumberKey = $arr['hidVendorPartNumberKey'];
         
        //validasi kalo status gk menunggu gk bisa edit 
        if (!empty($pkey)){
            $rs = $this->getDataRowById($pkey);
            if ($rs[0]['statuskey'] <> 1){
                $this->addErrorList($arrayToJs,false,$this->errorMsg[212]);
            }
        } 

        if(empty($itemInKey)){
            $this->addErrorList($arrayToJs,false,$this->errorMsg['itemIn'][1]);
        }else{
             $rsItemIn = $itemIn->getDataRowById($itemInKey); 
             $receivedate = strtotime(str_replace('\'','',$this->oDbCon->paramDate($arr['trDate'],' / ','Y-m-d')));
             $itemIndate =  strtotime($rsItemIn[0]['trdate']);

            if ($receivedate < $itemIndate)
             $this->addErrorList($arrayToJs,false, $this->errorMsg['itemInReceive'][2]);

        } 

        $arrDetailKeys = array(); 
        for($i=0;$i<count($arrItemkey);$i++) { 
            $rsItem = $item->getDataRowById($arrItemkey[$i]);
            
            if (empty($arrItemkey[$i]) ){ 
                $this->addErrorList($arrayToJs,false, $this->errorMsg['item'][1]); 	
            }else{
                
                if ($this->unFormatNumber($arrReceivedQtyInBaseUnit[$i]) <= 0){
                    $this->addErrorList($arrayToJs,false,$rsItem[0]['name']. '. ' . $this->errorMsg[500]); 
                } 

                // cek punya konversi unit utk satuan yg dipilih gk  
               /* $conv = $item->getConvMultiplier($arrItemkey[$i],$arrSelUnit[$i]);
                if (empty($conv)){
                    $this->addErrorList($arrayToJs,false,$rsItem[0]['name']. '. ' . $this->errorMsg['itemUnitConversion'][3]); 
                } */ 

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
            
            
            if (!empty($arrItemkey[$i]) &&  $this->unFormatNumber($arrReceivedQtyInBaseUnit[$i]) > $this->unFormatNumber($arrQtyMinusInBaseUnit[$i]) ){ 
                $this->addErrorList($arrayToJs,false,$rsItem[0]['name']. '. ' . $this->errorMsg['purchaseReceive'][1]); 
            } 
            
        }
          

        return $arrayToJs;
     }
 

    function afterStatusChanged($rsHeader){ 
        $itemIn = new ItemIn();
        $itemIn->updateItemInReceivedItem($rsHeader[0]['refkey']);
         
        // retrieve latest status
        $rsHeader = $this->getDataRowById($rsHeader[0]['pkey']);
        if ($rsHeader[0]['statuskey'] == 2)
            $this->changeStatus($rsHeader[0]['pkey'],3); 
    }
     
    
  
    function validateConfirm($rsHeader){
        $id = $rsHeader[0]['pkey'];
          
        $itemIn = new itemIn(); 
        $warehouse = new Warehouse();
        //$coaLink = new COALink();
 
        $rsDetail = $this->getDetailById($id);

  
        $totalPayment = 0; 
        /*for($i=0;$i<count($rsPayment); $i++)
            $totalPayment += $rsPayment[$i]['amount']; */
        
        
         /*if (USE_GL){
                $arrCOA = array();
                array_push($arrCOA, 'inventorytemp' , 'inventory' ); 
                for ($i=0;$i<count($arrCOA);$i++){
                    $rsCOA = $coaLink->getCOALink ($arrCOA[$i], $warehouse->tableName,$rsHeader[0]['warehousekey'], 0); 
                    if (empty($rsCOA))	
                        $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '. $this->errorMsg['coa'][3]);
                }  
             
                if ($isCash){
                    $rsPayment = $this->getPaymentMethodDetail($id);  
                    for($i=0;$i<count($rsPayment); $i++){ 
                        if ($rsPayment[$i]['amount'] > 0 ){ 
                            $rsCOA = $coaLink->getCOALink ('payment', $warehouse->tableName,$rsHeader[0]['warehousekey'], $rsPayment[$i]['paymentkey']); 
                            if (empty($rsCOA))	
                                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['coa'][3]);
                        }
                    }      
                }else{ 

                     $rsCOA = $coaLink->getCOALink ('ap', $warehouse->tableName,$rsHeader[0]['warehousekey'],0); 
                     if (empty($rsCOA))	
                        $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['coa'][3]);

                } 
        }*/
        
      
        // cek sudah diterima full blm
        for($i=0;$i<count($rsDetail);$i++){ 
            $qtyMinusInBaseUnit = $rsDetail[$i]['qtyminusinbaseunit']; 

            $rsItemIn = $itemIn->getDetailById($rsHeader[0]['refkey'],' and itemkey = ' . $this->oDbCon->paramString($rsDetail[$i]['itemkey']) );
            //$this->setLog($rsItemIn[0]['qty'].' - '.$rsItemIn[0]['receivedqty'].' <> '.$qtyMinus);
            if ($rsItemIn[0]['qtyinbaseunit'] - $rsItemIn[0]['receivedqtyinbaseunit'] <> $qtyMinusInBaseUnit ) 
                   $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['purchaseReceive'][3]);

        }
        
         if(USE_SN){ 
            $item = new Item();
            $arrSNLog = array();
            for($i=0;$i<count($rsDetail);$i++){
                $rsItem = $item->getDataRowById($rsDetail[$i]['itemkey']);
                if(!$rsItem[0]['needsn'])  continue;

                $rsSN = $this->getSerialNumber($rsDetail[$i]['pkey']);
                foreach($rsSN as $row){ 
                    if (in_array($row['serialnumber'], $arrSNLog)) 
                          $this->addErrorLog(false,'<strong>'.$row['serialnumber'].'</strong>, '. $this->errorMsg['serialnumber'][3]);  
                        
                    array_push($arrSNLog, $row['serialnumber']);
                }
                
                
                $receivedQty = $this->unFormatNumber($rsDetail[$i]['receivedqtyinbaseunit']);  
                if($receivedQty <> count($rsSN))
                    $this->addErrorLog(false, $this->errorMsg['serialnumber'][2]); 

            }	 
        } 
        
         
     }
 
    function confirmTrans($rsHeader){
        $id = $rsHeader[0]['pkey'];
        
        $rsHeader = $this->getDataRowById($id); 
        $warehouse = new Warehouse(); 
        $itemIn = new itemIn();
        $coaLink = new COALink(); 
        $warrantyPeriod = new WarrantyPeriod();
        $itemMovement = new ItemMovement();  
        $item = new Item();
        
        $rsItemInHeader = $itemIn->getDataRowById($rsHeader[0]['refkey']); 

        $rsWarehouse = $warehouse->getDataRowById($rsHeader[0]['warehousekey']);
        $rsTableKey = $this->getTableKeyAndObj($this->tableName); 

        $arrNote = array();
        array_push($arrNote, $rsHeader[0]['code']);
        array_push($arrNote, $this->ucFirst($this->lang['itemInReceive']. ' ' .$this->lang['from']) . ' '.$rsItemInHeader[0]['code']); 
        $note = implode('. ',$arrNote).'.'; 
         
        
        $rsDetail = $this->getDetailById($rsHeader[0]['pkey']); 
        
         // kalo pake warranty
         if(USE_SN){
            $rsWarranty = $warrantyPeriod->searchData();
            $rsWarranty = array_column($rsWarranty,'period', 'pkey');
            $rsWarranty[0] = 0; // default kalo gk ad warranty
         }

        
        for($i=0;$i<count($rsDetail); $i++){	 
           if ($rsDetail[$i]['receivedqtyinbaseunit'] != 0)
            
            $rsItem = $item->getDataRowById($rsDetail[$i]['itemkey']);
            $itemMovement->updateItemMovement($id,$rsDetail[$i]['itemkey'],$rsDetail[$i]['receivedqtyinbaseunit'],$rsDetail[$i]['costinbaseunit'],$this->tableName, $rsHeader[0]['warehousekey'], $note,$rsHeader[0]['trdate'], $rsDetail[$i]['vendorpartnumberkey']);
            
            if(USE_SN){
                  
                // set warranty enddate 
                $warrantyMonth = $rsWarranty[$rsItem[0]['warrantyperiodkey']];
                 
                $warrantyVendorMonth = $rsWarranty[$rsItem[0]['warrantyvendorperiodkey']]; 
                $date = new DateTime($rsHeader[0]['trinvoicedate']); 
                $date->add(new DateInterval('P'.$warrantyVendorMonth.'M'));
                $warrantyVendorEndDate = $date->format('d / m / Y');  
                
                $rsSN = $this->getSerialNumber($rsDetail[$i]['pkey']); 
                for($j=0;$j<count($rsSN); $j++){ 
                        // kalo sudah pernah ad SN nya (buyback atau retur)
                        // garansi vendor jgn diupdate
                      
                        // informasi vendor hanya diudpate ketika barnag masuk pertama kali
                        // SATU TRANSAKSI BISA MEMILIKI BEBERAPA VARIAS, bisa sebagian barang baru, sebagian barnag buyback
                        // lama garansi dan jenis garansi harusnya sama karena itemnya sama
                        $result = $itemMovement->searchItemSerialNumber($rsSN[$j]['serialnumber']);   
                        $vendorExpDate = (empty($result)) ? $warrantyVendorEndDate :  $this->formatDBDate($result[0]['warrantyvendorperiodexpireddate']);
                      
                        $arrMovementParam = array(
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
                                'warrantyperiodtime' => $warrantyMonth,
                                'warrantyperiodexpireddate' => DEFAULT_EMPTY_DATE,
                                'reftabletype' => $rsTableKey['key'],
                                'warrantyvendorperiodkey' => $rsItem[$i]['warrantyvendorperiodkey'],
                                'warrantyvendorperiodtime' => $warrantyVendorMonth ,
                                'warrantyvendorperiodexpireddate' => $vendorExpDate,
                                'supplierkey' => $rsHeader[0]['supplierkey']
                       );
                          
                     /* $this->setLog("--", true);
                      $this->setLog($result, true);
                      $this->setLog($arrMovementParam, true);*/
                    
                      $itemMovement->updateItemSNMovement($arrMovementParam); 
                 }
            }
        }	 


        //update jurnal umum 
        $this->updateGL($rsHeader);
    } 


    function updateGL($rs){
        if (!USE_GL) return; 
        
/*
        $itemIn = new itemIn();
        $warehouse = new Warehouse();
        $coaLink = new COALink();
        $generalJournal = new GeneralJournal();
        $supplier = new Supplier();
        $item = new Item();
        
        $warehousekey = $rs[0]['warehousekey'];
        
        $rsKey = $generalJournal->getTableKeyAndObj($this->tableName);
        $arr = array();
        $arr['pkey'] = $generalJournal->getNextKey($generalJournal->tableName);
        $arr['code'] = 'xxxxx';
        $arr['refkey'] = $rs[0]['pkey'];
		$arr['refTableType'] = $rsKey['key'];
        $arr['trDate'] =  $this->formatDBDate($rs[0]['trdate'],'d / m / Y');  
        $arr['createdBy'] = 0;
        $arr['trDesc'] = ''; 
		$arr['selWarehouseKey'] = $rs[0]['warehousekey'];

        $temp = -1;
        $totalPayment = 0; 

        $rsDetail = $this->getDetailById($rs[0]['pkey']);
 
        $arrItemCOA = array();
        $arrItemTempCOA = array();
            
        for ($i=0;$i<count($rsDetail);$i++) { 
            
            $itemCOAKey = $item->getInventoryCOAKey($rsDetail[$i]['itemkey'],$warehousekey);
            $itemTempCOAKey = $item->getInventoryTempCOAKey($rsDetail[$i]['itemkey'],$warehousekey);
            
            $rsItemInDetail = $itemIn->getDetailByColumn('pkey',$rsDetail[$i]['refpodetailkey']);
            
            $totalItemValue = $rsDetail[$i]['receivedqtyinbaseunit'] * $rsItemInDetail[0]['priceinbaseunit'];
                
            $arrItemCOA[$itemCOAKey] = (!isset($arrItemCOA[$itemCOAKey])) ? $totalItemValue : $arrItemCOA[$itemCOAKey] + $totalItemValue; 
            $arrItemTempCOA[$itemTempCOAKey] = (!isset($arrItemTempCOA[$itemTempCOAKey])) ? $totalItemValue : $arrItemTempCOA[$itemTempCOAKey] + $totalItemValue; 
        }
        
        foreach ($arrItemCOA as $coakey => $coaValue){   
            $temp++;
            $arr['hidCOAKey'][$temp] = $coakey; 
            $arr['debit'][$temp] = $coaValue; 
            $arr['credit'][$temp] = 0;    
        }
         
        
        foreach ($arrItemTempCOA as $coakey => $coaValue){    
            $temp++;
            $arr['hidCOAKey'][$temp] = $coakey; 
            $arr['debit'][$temp] = 0;
            $arr['credit'][$temp] =  $coaValue; 
        }
         
        
        if (!empty($rs[0]['shipmentfee'])){ 
            $rsCOA = $coaLink->getCOALink ('othercost', $warehouse->tableName,$warehousekey, 0); 
            $temp++;
            $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
            $arr['debit'][$temp] =  $rs[0]['shipmentfee']; 
            $arr['credit'][$temp] = 0; 
            
            $termOfPayment = new TermOfPayment();
            $rsTOP = $termOfPayment->getDataRowById($rs[0]['termofpaymentkey']); 
            $isCash = ($rsTOP[0]['duedays'] == 0) ? true : false; 

            if ($isCash) {
                $rsPayment = $this->getPaymentMethodDetail($rs[0]['pkey']);  
                for($i=0;$i<count($rsPayment); $i++){ 
                     $rsCOA = $coaLink->getCOALink ('payment', $warehouse->tableName,$warehousekey,$rsPayment[$i]['paymentkey']); 
                     $temp++;
                     $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
                     $arr['debit'][$temp] = 0;
                     $arr['credit'][$temp] = $rsPayment[$i]['amount'];  

                     $totalPayment +=  $arr['credit'][$temp]; 
                }
 
                //selisih pembayaran   
                $temp++; 
                if ($rs[0]['balance'] < 0){ 
                    $rsCOA = $coaLink->getCOALink ('otherrevenue', $warehouse->tableName,$warehousekey, 0); 
                    $arr['debit'][$temp] = 0; 
                    $arr['credit'][$temp] = abs($rs[0]['balance']); 
                }else{ 
                    $rsCOA = $coaLink->getCOALink ('othercost', $warehouse->tableName,$warehousekey, 0); 
                    $arr['debit'][$temp] = abs($rs[0]['balance']);  
                    $arr['credit'][$temp] = 0;
                }
                $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
                
            }else {  
                    $temp++;
                    $arr['hidCOAKey'][$temp] =  $supplier->getAPCOAKey($rs[0]['supplierkey'],$warehousekey);
                    $arr['debit'][$temp] = 0; 
                    $arr['credit'][$temp] =  $rs[0]['shipmentfee']; 

                    $totalPayment +=  $arr['credit'][$temp]; 
            } 
        } 
 
        $arrayToJs = $generalJournal->addData($arr); 
        if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rs[0]['code'] . '</strong>. '.$this->errorMsg[504].' '.$arrayToJs[0]['message']);    */
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
                $itemIn = new ItemIn();

                // default, ongkir dan cost dibagi berdasarkan proporsional gramasi/kubikasi
                $useGramasi = $this->loadSetting('costProportionalType');

                $shipmentFee = 0; //$this->unFormatNumber($arrParam['shipmentFee']);    
                $rsItemInHeader = $itemIn->getDataRowById($arrParam['hidItemInKey']);

                $arrReceivedQtyInBaseUnit = $arrParam['receivedQtyInBaseUnit'];
                $arrItem = $arrParam['hidItemKey'];  
                $arrItemInKey = $arrParam['hidItemInDetailKey']; 

                $gramasi = 0;
                $subtotal = 0 ;

                $arrItemDetail = array();
                for($i=0;$i<count($arrItem);$i++){  
                    $itemkey = $arrItem[$i];  
                    $rsItem = $item->getDataRowById($itemkey); 
                    $rsItemInDetail =  $itemIn->getDetailByColumn('pkey',$arrItemInKey[$i]);


                    $arrItemDetail[$i]['baseunitkey'] = $rsItem[0]['baseunitkey'];   
                    $arrItemDetail[$i]['costinbaseunit'] = $rsItemInDetail[0]['costinbaseunit'];   

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
    
    function normalizeParameter($arrParam, $trim=false){
            $item = new Item();
            $itemIn = new itemIn();
        
            $arrParam = parent::normalizeParameter($arrParam); 
         
            $arrItemkey = $arrParam['hidItemKey'];
        
            //$arrParam['shipmentFee'] = (isset($arrParam['shipmentFee']) && !empty($arrParam['shipmentFee'])) ? $arrParam['shipmentFee'] : 0; 
            $arrParam['balance'] = (isset($arrParam['balance'])) ? $arrParam['balance'] : 0;
            $arrParam['trInvoiceDate'] = (isset($arrParam['trInvoiceDate'])) ? $arrParam['trInvoiceDate'] : DEFAULT_EMPTY_DATE;
        
            $arrParam['snList'] = (isset($arrParam['snList'])) ? $arrParam['snList'] : array();
            foreach($arrParam['snList'] as $key=>$row)
                $arrParam['snList'][$key] = implode(',',$item->normalizeSNList($row));
        
            $rsItemIn = $itemIn->getDataRowById($arrParam['hidItemInKey']);
            $reCalculateResult = $this->reCalculateDetail($arrParam);  
            $arrParam['selWarehouse'] = $rsItemIn[0]['warehousekey'];
            $arrParam['hidSupplierKey'] = $rsItemIn[0]['supplierkey'];
         
 
            for ($i=0;$i<count($arrItemkey);$i++){   
               $arrParam['baseunitkey'][$i] = $reCalculateResult['detailItem'][$i]['baseunitkey']; 
               $arrParam['costinbaseunit'][$i] = $reCalculateResult['detailItem'][$i]['costinbaseunit'];
            }
 
            return $arrParam;
    }
    
    function getSerialNumber($refkey){
        $sql = 'select * from '.$this->tableSerial.' where refkey = ' . $this->oDbCon->paramString($refkey); 
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
