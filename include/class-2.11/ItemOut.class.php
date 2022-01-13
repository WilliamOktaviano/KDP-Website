<?php
  
class ItemOut extends BaseClass{ 
 
function __construct(){

    parent::__construct();

    $this->tableName = 'item_out_header';
    $this->tableNameDetail = 'item_out_detail';
    $this->tableItemUnit = 'item_unit';
    $this->tableItem = 'item';
    $this->tableBrand = 'brand';
    $this->tableItemCategory = 'item_category';
   // $this->tableVendorPartNumber = 'item_vendor_part_number';
    //$this->tableSerial = 'item_out_detail_sn';
    $this->tableItemSN = 'item_sn';
    $this->tableWarehouse = 'warehouse';
    //$this->tableCustomer = 'customer';
    $this->tableEmployee = 'employee';
    $this->tableStatus = 'transaction_status';
	$this->tableType = 'item_out_type';
    $this->isTransaction = true; 

    $this->tableNeedToBeCopyOnCancel = array($this->tableNameDetail);

    $this->securityObject = 'ItemOut';  
 
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
    $this->arrData['warehousekey'] = array('selWarehouseKey');
    $this->arrData['trdesc'] = array('trDesc'); 
    $this->arrData['refkey'] = array('refkey');
    $this->arrData['reftabletype'] = array('reftabletype'); 
    $this->arrData['statuskey'] = array('selStatus');
    $this->arrData['isfulldelivered'] = array('chkIsFullDelivered');
    //$this->arrData['customerkey'] = array('hidCustomerKey');
    $this->arrData['refcode'] = array('refCode');
    $this->arrData['isinternal'] = array('chkIsInternal');
    $this->arrData['employeekey'] = array('hidEmployeeKey');
    $this->arrData['recipientname'] = array('recipientName');
    $this->arrData['islinked'] = array('islinked');
    $this->arrData['coacostkey'] = array('hidCOACostKey');
    $this->arrData['typekey'] = array('selTransactionType');
    
    $this->arrDataListAvailableColumn = array(); 
    array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 120));
    array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align' =>'center', 'format' => 'date'));
    array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename','default'=>true, 'width' => 100));
    array_push($this->arrDataListAvailableColumn, array('code' => 'refcode','title' => 'refCode','dbfield' => 'refcode', 'width' => 100));    
    array_push($this->arrDataListAvailableColumn, array('code' => 'recipient','title' => 'recipient','dbfield' => 'recipientname','default'=>true, 'width' => 250));
    array_push($this->arrDataListAvailableColumn, array('code' => 'description','title' => 'note','dbfield' => 'trdesc' , 'default'=>true, 'width' => 250));
    array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
       
    $this->importUrl = 'import/itemOut';
    
    $this->printMenu = array();  
    array_push($this->printMenu,array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/itemOut'));
     
    $this->includeClassDependencies(array( 
              'COALink.class.php',
              'GeneralJournal.class.php',
              'Item.class.php',
              'ItemOutDelivery.class.php',
              'ItemUnit.class.php',
              'WarrantyPeriod.class.php', 
              //'Customer.class.php', 
              'Marketplace.class.php', 
              'ItemMovement.class.php', 
    ));
     
    $this->overwriteConfig();

}
 
    function getQuery(){

        $sql = '
            SELECT '.$this->tableName.'.* ,
               '.$this->tableWarehouse.'.name as warehousename, 
               '.$this->tableEmployee.'.username as employeeusername,
                '.$this->tableName.'.recipientname as recipientname, 
                '.$this->tableEmployee.'.pkey as recipientkey, 
               '.$this->tableStatus.'.status as statusname
            FROM 
               '.$this->tableStatus.', 
               '.$this->tableName.' 
                    left join ' . $this->tableEmployee .' on  '.$this->tableName.'.employeekey = ' . $this->tableEmployee .'.pkey  ,
               '.$this->tableWarehouse.'  
            WHERE '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and  '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey
        ' .$this->criteria ; 

        $sql .=  $this->getWarehouseCriteria() ;
        $sql .=  $this->getCompanyCriteria() ;
          
        return $sql;

    }

    /*function afterUpdateData($arrParam, $action){  
        $this->updateDetail($arrParam); 
    }*/
 
    function afterStatusChanged($rsHeader){ 
        $rsHeader = $this->getDataRowById($rsHeader[0]['pkey']);
        
        if ($rsHeader[0]['isfulldelivered'] == 1 && $rsHeader[0]['statuskey'] == 2){  
            $sql = 'update '.$this->tableNameDetail.' set deliveredqtyinbaseunit = qtyinbaseunit where refkey  = '.$this->oDbCon->paramString($rsHeader[0]['pkey']);
            $this->oDbCon->execute($sql); 
             
            $this->changeStatus($rsHeader[0]['pkey'],3); 
        }  
         
        // update marketplace QOH
        if($this->isActiveModule('marketplace')){
			if ($rsHeader[0]['isfulldelivered'] == 1 && ($rsHeader[0]['statuskey'] == 2 || $rsHeader[0]['statuskey'] == 4)){  
				$marketplace = new Marketplace();
				$rsDetail = $this->getDetailById($rsHeader[0]['pkey']);
				$arrItemKey = array_column($rsDetail,'itemkey'); 
				$marketplace->updateProductsQOHInAllMarketplace($arrItemKey); 
			}
		}
    }
/*

    function updateDetail($arrParam){
    $pkey = $arrParam['pkey'];
        
    // recount convmultiplier
    $reCountResult = $this->reCalculateConversionDetail($arrParam); 
    $arrBaseUnitKey =  $reCountResult['baseUnitKey']; 
    $arrConvMultiplier = $reCountResult['unitConvMultiplier']; 
    $arrQtyInBaseUnit = $reCountResult['qtyInBaseUnit']; 

    $sql = 'delete from '.$this->tableNameDetail.' where refkey = '. $this->oDbCon->paramString($pkey);
    $this->oDbCon->execute($sql);

    $sql = 'delete from '.$this->tableSerial.' where refheaderkey = '. $this->oDbCon->paramString($pkey);
    $this->oDbCon->execute($sql);

    $arrItemkey = $arrParam['hidItemKey']; 
    $arrUnitKey = $arrParam['selUnit']; 
    $arrQty = $arrParam['qty'];    
    $arrSerialNumber = $arrParam['snList'];
    
    $arrVendorPartNumberKey = (isset($arrParam['hidVendorPartNumberKey'])) ? $arrParam['hidVendorPartNumberKey'] : array();  
  
    $item = new Item();

    for ($i=0;$i<count($arrItemkey);$i++){

        if (empty($arrItemkey[$i]))
            continue; 

        $detailkey = $this->getNextKey($this->tableNameDetail);        
        $arrSNItem =  preg_split('/[\n, ]+/', $arrSerialNumber[$i]); 
        $qty =  $this->unFormatNumber($arrQty[$i]); 
        $qtyinbaseunit = $this->unFormatNumber($arrQtyInBaseUnit[$i]); 
        $vendorPartNumber = (isset($arrVendorPartNumberKey[$i]) && !empty($arrVendorPartNumberKey[$i])) ? $arrVendorPartNumberKey[$i] : 0;
        $rsItem = $item->getDataRowById($arrItemkey[$i]);
        $costItem = $rsItem[0]['cogs'];

        $sql = 'insert into '.$this->tableNameDetail.' (
                    pkey,
                    refkey,
                    itemkey,
                    qty,  
                    unitkey,
                    unitconvmultiplier, 
                    qtyinbaseunit,
                    costinbaseunit,
                    vendorpartnumberkey
                 ) values ( 
                    '.$this->oDbCon->paramString($detailkey).',
                    '.$this->oDbCon->paramString($pkey).',
                    '.$this->oDbCon->paramString($arrItemkey[$i]).',
                    '.$this->oDbCon->paramString($qty).',
                    '.$this->oDbCon->paramString($arrUnitKey[$i]).',
                    '.$this->oDbCon->paramString($arrConvMultiplier[$i]).', 
                    '.$this->oDbCon->paramString($qtyinbaseunit).' , 
                    '.$this->oDbCon->paramString($costItem).' , 
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
*/

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

    function validateForm($arr,$pkey = ''){

     $showVendorPartNumber = $this->loadSetting('showVendorPartNumber'); 
        
     // validasi kalo pake parts number, yg gk boleh sama item dan parts number
     // validasi parts number harus sesuai dengan itemnya

    $item = new Item();   

    $arrayToJs = parent::validateForm($arr,$pkey); 

    $arrItemkey = $arr['hidItemKey'];        
    $arrSelUnit = $arr['selUnit']; 
    $arrQty = $arr['qty'];  

    if($showVendorPartNumber)
        $arrVendorPartNumberKey = isset($arr['hidVendorPartNumberKey']) ? $arr['hidVendorPartNumberKey'] : array();
        
    //validasi kalo status gk menunggu gk bisa edit 
    if (!empty($pkey)){
        $rs = $this->getDataRowById($pkey);
        if ($rs[0]['statuskey'] <> 1){
            $this->addErrorList($arrayToJs,false,$this->errorMsg[212]);
        }
    }  

 	if ($arr['selTransactionType'] == 999 && empty($arr['hidCOACostKey']) )
        $this->addErrorList($arrayToJs,false,$this->errorMsg['coa'][1]); 
		

		
    $arrDetailKeys = array(); 

    for($i=0;$i<count($arrItemkey);$i++) {
        if (empty($arrItemkey[$i]) ){ 
            $this->addErrorList($arrayToJs,false, $this->errorMsg['item'][1]); 	
        } else{

            $rsItem = $item->getDataRowById($arrItemkey[$i]);

            if ($this->unFormatNumber($arrQty[$i]) <= 0) 
                $this->addErrorList($arrayToJs,false,$rsItem[0]['name']. '. ' . $this->errorMsg[500]); 

            // cek punya konversi unit utk satuan yg dipilih gk  
            $conv = $item->getConvMultiplier($arrItemkey[$i],$arrSelUnit[$i]);
            if (empty($conv)) 
                $this->addErrorList($arrayToJs,false,$rsItem[0]['name']. '. ' . $this->errorMsg['itemUnitConversion'][3]);  

            // cek ada detail double gk 
            if($showVendorPartNumber && !empty($arrVendorPartNumberKey[$i])){
//                if (in_array( array($arrItemkey[$i], $arrVendorPartNumberKey[$i]),$arrDetailKeys)) 
//                    $this->addErrorList($arrayToJs,false, $rsItem[0]['name'].'. '.$this->errorMsg[215]); 	 
//                 else 
//                    array_push($arrDetailKeys, array($arrItemkey[$i], $arrVendorPartNumberKey[$i]));
//
//                // cek itemkey sesuai gk dengan partnumber
//                if(!empty($arrVendorPartNumberKey[$i])){ 
//                    $rsVendor = $item->getVendorPartNumber($arrItemkey[$i], 'and '.$this->tableVendorPartNumber.'.pkey ='.$this->oDbCon->paramString($arrVendorPartNumberKey[$i]));
//                    if(empty($rsVendor))
//                        $this->addErrorList($arrayToJs,false,$rsItem[0]['name']. '. ' . $this->errorMsg['vendorPartNumber'][3]); 
//                }
                
                //sementara vendorpartnumber boleh kosong
                /*else{
                        $this->addErrorList($arrayToJs,false,$rsItem[0]['name']. '. ' . $this->errorMsg['vendorPartNumber'][1]); 
                }*/
            }else{
                if (in_array($arrItemkey[$i],$arrDetailKeys)) 
                    $this->addErrorList($arrayToJs,false, $rsItem[0]['name'].'. '.$this->errorMsg[215]); 	 
                 else  
                    array_push($arrDetailKeys, $arrItemkey[$i]);

            }

        } 
 
    } 

    return $arrayToJs;
    }
    
     
    function validateCancel($rsHeader,$autoChangeStatus=false){ 
        $id = $rsHeader[0]['pkey'];

        //cek apakah sudah ad penerimaan
		 if($this->isActiveModule('itemOutDelivery')){
			if (!$rsHeader[0]['isfulldelivered']) {
				$itemOutDelivery = new ItemOutDelivery();
				$rsItemOutDelivery = $itemOutDelivery->searchData('','',true,' and '.$itemOutDelivery->tableName.'.refkey = '.$this->oDbCon->paramString($id).' and ('.$itemOutDelivery->tableName.'.statuskey in (2,3))');

				if (!empty($rsItemOutDelivery))
					 $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' ' .$this->errorMsg['itemOut'][2]);
			} 
		 }

       if ( !$autoChangeStatus && $rsHeader[0]['statuskey'] == 1 ) {
            
                if(!empty($rsHeader[0]['islinked'])) 
                    $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg['itemOut'][4]);    
        } 
     }
    
 
    function validateConfirm($rsHeader){
  
        $id = $rsHeader[0]['pkey'];
        $warehouse = new Warehouse();  
        $coaLink = new COALink();

        $rsDetail = $this->getDetailById($id);

        //validasi stock
        $itemMovement = new itemMovement();
        for($i=0;$i<count($rsDetail);$i++){
             $saldoakhir = $itemMovement->getItemQOH($rsDetail[$i]['itemkey'], $rsHeader[0]['warehousekey']);  
             $totalqty = $saldoakhir - $rsDetail[$i]['qtyinbaseunit'];  

            $item = new Item();

            if($totalqty < 0){ 
                $rsItem = $item->getDataRowById($rsDetail[$i]['itemkey']);
                $this->addErrorLog(false,'<strong>'.$rsItem[0]['name'].'</strong>. '.$this->errorMsg[402]);
            }else{ 
  
                // hati2 kalo aktifin, karena bermasalah di API
                /*if(USE_SN && $rsHeader[0]['isfulldelivered'] == 1){ 
                    $rsItem = $item->getDataRowById($rsDetail[$i]['itemkey']); 
                    if(!$rsItem[0]['needsn'])
                        continue;
                    
                    $rsSN = $this->getSerialNumber($rsDetail[$i]['pkey']); 

                    if($rsDetail[$i]['qtyinbaseunit'] <> count($rsSN))
                        $this->addErrorLog(false, $this->errorMsg['serialnumber'][2]); 

                    //validasi SN
                    for($j=0;$j<count($rsSN); $j++){
                        $saldoakhir = $itemMovement->getItemSNQOH($rsDetail[$i]['itemkey'], $rsSN[$j]['serialnumber'], $rsHeader[0]['warehousekey']); 
                        $totalqty = $saldoakhir - 1;
                        if($totalqty<0) 
                            $this->addErrorLog(false,'<strong>'.$rsItem[0]['name'].', '. $rsSN[$j]['serialnumber'].'</strong>. '.$this->errorMsg[402]);

                    } 
                }*/ 
            }
        } 
        
         if (USE_GL){
            $arrCOA = array();
            array_push($arrCOA, 'hpp' , 'inventory' ); 
            for ($i=0;$i<count($arrCOA);$i++){
                $rsCOA = $coaLink->getCOALink ($arrCOA[$i], $warehouse->tableName,$rsHeader[0]['warehousekey'], 0); 
                if (empty($rsCOA))	
                    $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '. $this->errorMsg['coa'][3]);
            } 
         }


    }
    
    
    function confirmTrans($rsHeader){ 
        $id = $rsHeader[0]['pkey'];  
        
        $item = new Item();
        $warrantyPeriod = new WarrantyPeriod();
        $itemMovement = new ItemMovement();  
        $employee = new Employee();
        //$customer = new Customer();
        
        $arrNote = array();
        array_push($arrNote, $rsHeader[0]['code']); 
          
        
        $recipientName = '';
        if(!empty($rsHeader[0]['recipientname']) || !empty($rsHeader[0]['employeekey'])){  
            
            if(!empty($rsHeader[0]['recipientname'])){
               $recipientName = $rsHeader[0]['recipientname']; 
            }else{
                $rsEmployee = $employee->getDataRowById($rsHeader[0]['employeekey']);
                $recipientName = $rsEmployee[0]['name'];  
            }
                
        }
        $recipient = (!empty($recipientName)) ? ' ' .$this->lang['for'] . ' ' .$recipientName : ''; 
        
        //IF HAS CUSTOMER
//        $customerName = '';
//        if(!empty($rsHeader[0]['customerkey']) || !empty($rsHeader[0]['customerkey'])){   
//            $rsCustomer = $customer->getDataRowById($rsHeader[0]['customerkey']);
//            $customerName = $rsCustomer[0]['name'];   
//        }
//        $customer = (!empty($customerName)) ? ' ' .$this->lang['for'] . ' ' .$customerName : ''; 
//        
//        array_push($arrNote, $this->ucFirst($this->lang['itemOut']).$recipient.$customer);
        
        
        if(!empty($rsHeader[0]['refcode']))
            array_push($arrNote, $rsHeader[0]['refcode']); 
        
        $note = implode('. ', $arrNote).'.';
        
        $this->updateConvMultiplier($id); 
        $this->updateCOGS($id);
         
        $rsDetail = $this->getDetailById($id);  
        
		if($this->isActiveModule('itemOutDelivery')){
			$itemOutDelivery = new ItemOutDelivery();
			if(!$rsHeader[0]['isfulldelivered']){
					$arrParam = array();

					for($i=0;$i<count($rsDetail); $i++){		 
						//$arrParam['hidDetailKey'][$i] = 0;
						$arrParam['hidItemOutDetailKey'][$i] = $rsDetail[$i]['pkey'];
						$arrParam['hidItemKey'][$i] = $rsDetail[$i]['itemkey'];
						//$arrParam['hidVendorPartNumberKey'][$i] = $rsDetail[$i]['vendorpartnumberkey'];
						$arrParam['unitkey'][$i] = $rsDetail[$i]['unitkey'];
						$arrParam['unitconvmultiplier'][$i] = $rsDetail[$i]['unitconvmultiplier'];
						$arrParam['orderedQtyInBaseUnit'][$i] = $rsDetail[$i]['qtyinbaseunit']; 
						$arrParam['deliveredQtyInBaseUnit'][$i] = $rsDetail[$i]['qtyinbaseunit'];
						$arrParam['qtyMinusInBaseUnit'][$i] = $rsDetail[$i]['qtyinbaseunit'];
						$arrParam['costinbaseunit'][$i] = $rsDetail[$i]['costinbaseunit'];
						//$arrParam['detailDesc'][$i] = ''; 
					}


					$user = base64_decode($_SESSION[$this->loginAdminSession]['id']);
					$arrParam['code'] = 'xxxxxx';
					$arrParam['hidItemOutKey'] = $rsHeader[0]['pkey'];
					$arrParam['trDate'] = $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y');
					//$arrParam['hidCustomerKey'] = $rsHeader[0]['customerkey']; 
					$arrParam['trDesc'] = '';
					$arrParam['selWarehouse'] = $rsHeader[0]['warehousekey'];
					$arrParam['createdBy'] = $user;

					$arrayToJs = $itemOutDelivery->addData($arrParam); 

					if (!$arrayToJs[0]['valid'])
						$this->addErrorLog(false, '<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[201].' '.$arrayToJs[0]['message'], true); 

					return;
			} 

		}
		
        // hati2 kalo aktifin, karena bermasalah di API
        // kalo pake warranty
         /*if(USE_SN){
            $rsWarranty = $warrantyPeriod->searchData();
            $rsWarranty = array_column($rsWarranty,'period', 'pkey');
            $rsWarranty[0] = 0; // default kalo gk ad warranty
         }*/

        for($i=0;$i<count($rsDetail); $i++){	 
           $rsItem = $item->getDataRowById($rsDetail[$i]['itemkey']);
            
           // get SN Vendor part Number    
           // gk bisa pake vendor part number, karena masalah kalo keluar 1 jenis brg 10 pcs misalny dr 2 vendor part number yg berbeda.
           // gk efisien
           $itemMovement->updateItemMovement($id,$rsDetail[$i]['itemkey'],-$rsDetail[$i]['qtyinbaseunit'], $rsDetail[$i]['costinbaseunit'] ,$this->tableName, $rsHeader[0]['warehousekey'], $note, $rsHeader[0]['trdate']);

            // hati2 kalo aktifin, karena bermasalah di API
           /* if(USE_SN){
                // set warranty enddate
                $warrantyMonth = $rsWarranty[$rsItem[0]['warrantyperiodkey']];

                $date = new DateTime($rsHeader[0]['trdate']);
                $date->add(new DateInterval('P'.$warrantyMonth.'M'));
                $warrantyEndDate = $date->format('d / m / Y'); 


               $rsSN = $this->getSerialNumber($rsDetail[$i]['pkey']); 
               for($j=0;$j<count($rsSN); $j++){ 
                   
                    // vendor warranty harus tetap diisi di SN movement
                    // kalo gk, nanti nge bug pada saat pembatalan barang masuk (kalo masuk 2xm buyback)
                    $rsSNINfo = $item->getSNInformation($rsSN[$j]['serialnumber']);
                    
                    $rsDetail[$i]['vendorpartnumberkey'] = $rsSN[$j]['vendorpartnumberkey'];
                        
                    $arrParam = array(
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
                                    'trdate' => $rsHeader[0]['trdate'],
                                    'warrantyvendorperiodkey' => $rsSNINfo[0]['warrantyvendorperiodkey'],
                                    'warrantyvendorperiodtime' => $rsSNINfo[0]['warrantyvendorperiod'],
                                    'warrantyvendorperiodexpireddate' => $this->formatDBDate($rsSNINfo[0]['warrantyvendorperiodexpireddate']),
                                    'warrantyperiodkey' => $rsItem[$i]['warrantyperiodkey'],
                                    'warrantyperiodtime' => $warrantyMonth,
                                    'warrantyperiodexpireddate' => $warrantyEndDate
                                    );
                   
                    //$this->setLog($arrParam,true);
                    $itemMovement->updateItemSNMovement($arrParam);
               }

            }*/

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
         $employee = new Employee();
         $rsKey = $generalJournal->getTableKeyAndObj($this->tableName);

         $warehousekey = $rsHeader[0]['warehousekey'];
        
         $arr = array();
         $arr['pkey'] = $generalJournal->getNextKey($generalJournal->tableName);
         $arr['code'] = 'xxxxx';
         $arr['refkey'] = $rsHeader[0]['pkey'];
         $arr['refTableType'] = $rsKey['key'];
         $arr['trDate'] =  $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y'); 
         $arr['createdBy'] = 0; 
		 $arr['selWarehouseKey'] = $rsHeader[0]['warehousekey'];
         
        // ============= DESCRIPTION 
         $desc = array(); 
                 
         $recipientName = '';
         if (!empty($rsHeader[0]['recipientname'])){
            $recipientName = $rsHeader[0]['recipientname'];
         }else{
            $rsEmployee = $employee->getDataRowById($rsHeader[0]['employeekey']);
            $recipientName = $rsEmployee[0]['name'];
         }
         
         $recipient = (!empty($recipientName)) ? ' ' .$this->lang['for'] . ' ' .$recipientName : '';
        
         array_push($desc, $this->ucFirst($this->lang['itemOut']).$recipient);
        
         // refcode
         if (!empty($rsHeader[0]['refcode'])) array_push($desc, $rsHeader[0]['refcode']);
          
		 $arr['trDesc'] = implode('. ',$desc) .'.'; 

        // ============= DESCRIPTION 
        
        
       /* $totalHPP = 0 ;
        $rsDetail = $this->getDetailById($rsHeader[0]['pkey']); 
        for($i=0;$i<count($rsDetail);$i++)
            $totalHPP += ( $rsDetail[$i]['costinbaseunit']  * $rsDetail[$i]['qtyinbaseunit']);
*/
        $temp = -1;

        $totalHPP = 0;
        $arrItemCOA = array(); 
        $rsDetail = $this->getDetailById($rsHeader[0]['pkey']); 
        foreach($rsDetail as $detail){
            $itemCOAKey = $item->getInventoryCOAKey($detail['itemkey'],$warehousekey); 
            $totalItemValue =  $detail['costinbaseunit']*$detail['qtyinbaseunit'];
            $arrItemCOA[$itemCOAKey] = (!isset($arrItemCOA[$itemCOAKey])) ? $totalItemValue : $arrItemCOA[$itemCOAKey] + $totalItemValue; 
        }
        
        foreach ($arrItemCOA as $coakey => $coaValue){ 
            $temp++;
            $arr['hidCOAKey'][$temp] = $coakey;
            $arr['debit'][$temp] = 0; 
            $arr['credit'][$temp] = $coaValue; 
            $arr['refCashBankKey'][$temp] = '';
            
            $totalHPP += $coaValue;
        }        
        
		$costcoakey = 0;
		switch($rsHeader[0]['typekey']){
			case 1 : $costcoalink = 'lostgaininventory';
					 break;
			case 2 : $costcoalink = 'operationalcost';
						break;
			case 3 : $costcoalink = 'maintenancecost';
						break;
			case 4 : $costcoalink = 'othercost';
						break; 
			case 999 :  $costcoakey = $rsHeader[0]['coacostkey'];
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
        $arr['debit'][$temp] = $totalHPP; 
        $arr['credit'][$temp] = 0;  

/*        $rsCOA = $coaLink->getCOALink ('inventory', $warehouse->tableName,$warehousekey, 0);
        $temp++;
        $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
        $arr['debit'][$temp] = 0; 
        $arr['credit'][$temp] = $totalHPP; */ 
        
        $arrayToJs = $generalJournal->addData($arr);

        if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[504].' '.$arrayToJs[0]['message']); 
    }

    function cancelTrans($rsHeader,$copy){ 
		
        $id = $rsHeader[0]['pkey']; 

        $itemMovement = new ItemMovement();  
        $itemMovement->cancelMovement($id,$this->tableName);
        //$itemMovement->cancelSNMovement($id,$this->tableName);

		if($this->isActiveModule('itemOutDelivery')){
			$itemOutDelivery = new ItemOutDelivery();
			$rsItemOutDelivery = $itemOutDelivery->searchData('','',true,' and '.$itemOutDelivery->tableName.'.refkey = '.$this->oDbCon->paramString($id).' and '.$itemOutDelivery->tableName.'.statuskey = 1');
			for($i=0;$i<count($rsItemOutDelivery);$i++) {
				$arrayToJs = $itemOutDelivery->changeStatus($rsItemOutDelivery[$i]['pkey'],4,'',false,true);
				if (!$arrayToJs[0]['valid'])
					throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.  $arrayToJs[0]['message']);    
			}
		}

        if ($copy)
            $this->copyDataOnCancel($id); 

        $this->cancelGLByRefkey($rsHeader[0]['pkey'],$this->tableName);
    } 
     
    function getDetailWithRelatedInformation($pkey,$criteria='',$orderby =''){
        $sql = 'select
                '.$this->tableNameDetail .'.*,
                '.$this->tableNameDetail.'.qtyinbaseunit * '.$this->tableNameDetail.'.costinbaseunit as totalcogs, 
                '.$this->tableItem.'.name as itemname, 
                '.$this->tableItem.'.code as itemcode,
                '.$this->tableItem.'.brandkey, 
                '.$this->tableBrand.'.name as brandname ,
                '.$this->tableItemCategory.'.pkey as itemcategorykey,
                '.$this->tableItemCategory.'.name as itemcategoryname,
                '.$this->tableItem.'.needsn,
                '.$this->tableItemUnit.'.name as unitname,
                baseunit.name as baseunitname ,
                concat(\' / \',baseunit.name) as cogsunit 
              from
                '. $this->tableNameDetail .'  ,
                '.$this->tableItem.'
                    left join '.$this->tableBrand.' on 	' . $this->tableItem .'.brandkey = '.$this->tableBrand.'.pkey ,
                '.$this->tableItemUnit.',
                '.$this->tableItemCategory.',
                '.$this->tableItemUnit.' baseunit
              where
                ' . $this->tableNameDetail .'.itemkey = '.$this->tableItem.'.pkey and
                ' . $this->tableNameDetail .'.unitkey = '.$this->tableItemUnit.'.pkey and
			  	' . $this->tableItem .'.baseunitkey = baseunit.pkey and
                '.$this->tableItem.'.categorykey = '.$this->tableItemCategory.'.pkey and
                '.$this->tableNameDetail .'.refkey IN ('.$this->oDbCon->paramString($pkey, ',').') ';

        $sql .= $criteria; 
        
        $sql .= ' ' .$orderby;

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
    function generateDefaultQueryForAutoComplete($returnField){ 
        $sql = 'select
                '.$returnField['key'].',
                '.$returnField['value'].' as value  
            from 
                '.$this->tableName . ',
                '.$this->tableStatus.'  
            where  		
                '.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey  
        ';
        
        $sql .=  $this->getCompanyCriteria() ;
        return $sql;
        
    }
    function generateItemOutReport($criteria='',$order='',$pkey=''){        
          
	   $sql =  '
       
       		SELECT '.$this->tableName.'.code,
                   '.$this->tableName.'.trdate, 
                   '.$this->tableName.'.trdesc, 
                   '.$this->tableName.'.trdesc, 
                   '.$this->tableName.'.recipientname as recipientname, 
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
                '.$this->tableName.' 
                    left join ' . $this->tableEmployee .' on  '.$this->tableName.'.employeekey = ' . $this->tableEmployee .'.pkey ,
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
         
        if (!empty($order))  
            $sql .=  ' ' .$order; 
         
        //$this->setLog($sql);
       return $this->oDbCon->doQuery($sql);
		 
    } 
    function normalizeParameter($arrParam, $trim = false){  
  
        $arrParam['chkIsInternal'] = (isset($arrParam['chkIsInternal'])) ? $arrParam['chkIsInternal'] : 0; 
        if($arrParam['chkIsInternal'] == 0)
            $arrParam['hidEmployeeKey'] = 0; 
        else
            $arrParam['recipientName'] = '';
        
        $arrParam['chkIsFullDelivered'] = (isset($arrParam['chkIsFullDelivered'])) ? $arrParam['chkIsFullDelivered'] : 1;

       /* if( $arrParam['chkIsFullDelivered'] == 0){
            for($i=0;$i<count($arrParam['snList']);$i++)
                $arrParam['snList'][$i] = ''; 
        }*/
        
       /* for($i=0;$i<count($arrParam['hidItemKey']);$i++){
            $arrParam['snList'][$i] = (isset($arrParam['snList'][$i]) && !empty($arrParam['snList'][$i])) ?  $arrParam['snList'][$i]: '';
        }*/

		if($arrParam['selTransactionType'] != 999)
			$arrParam['hidCOACostKey'] = 0;
		
        $arrParam = parent::normalizeParameter($arrParam, true);  
        return $arrParam; 
    }
 
    /*function updateDetailTablesOnCopy($id,$newPkey, $arrTableDetail){ 
         
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
     
    function updateCOGS($id){
        $item = new Item();
        $rsDetail = $this->getDetailById($id); 
        for($i=0;$i<count($rsDetail); $i++){
            $rsItem = $item->getDataRowById($rsDetail[$i]['itemkey']); 
            $sql = 'update '. $this->tableNameDetail .' set costinbaseunit = '.$this->oDbCon->paramString($rsItem[0]['cogs']).' where pkey = ' .$this->oDbCon->paramString($rsDetail[$i]['pkey']);
            $this->oDbCon->execute($sql); 
        } 
    }

  
    function updateItemOutDeliveredItem($pkey){ 
            $itemOutDelivery = new ItemOutDelivery();
            $rsHeader = $this->getDataRowById($pkey);  
            $rsDetail = $this->getDetailById($pkey); 

            for($i=0;$i<count($rsDetail); $i++){	
                $sql = 'select 
                        coalesce(sum(deliveredqtyinbaseunit),0) as totaldelivered
                    from 
                        '. $itemOutDelivery->tableName . ', '. $itemOutDelivery->tableNameDetail . '
                    where 
                         '. $itemOutDelivery->tableName . '.pkey = '. $itemOutDelivery->tableNameDetail . '.refkey and
                         '. $itemOutDelivery->tableName . '.refkey = '. $this->oDbCon->paramString($pkey) .' and
                         '. $itemOutDelivery->tableNameDetail . '.itemkey = ' . $rsDetail[$i]['itemkey'] .' and 
                         '. $itemOutDelivery->tableNameDetail . '.refitemoutdetailkey = ' . $rsDetail[$i]['pkey'] .' and 
                         (statuskey = 2 or statuskey = 3)';
                
//                $this->setLog($sql);
                $rsTotal = $this->oDbCon->doQuery($sql);

                $sql = 'update 
                            ' . $this->tableNameDetail.' 
                        set  
                            deliveredqtyinbaseunit = '. $rsTotal[0]['totaldelivered'].' 
                        where 
                            refkey = '.$pkey.' and 
                            pkey = '.$rsDetail[$i]['pkey'].' and 
                            itemkey = ' . $rsDetail[$i]['itemkey'];
                $this->oDbCon->execute($sql); 
//                $this->setLog($sql);
            }

            
            $sql = 'select * from ' . $this->tableNameDetail.' where refkey = '.$this->oDbCon->paramString($pkey).' and  deliveredqtyinbaseunit < qtyinbaseunit';
            $rs = $this->oDbCon->doQuery($sql);

            $statuskey = (empty($rs)) ? 3 : 2;
 
            if ($rsHeader[0]['statuskey'] <> $statuskey)
                $this->changeStatus($pkey,$statuskey);
 
      
    }
 
	function getTransactionType (){
		$sql = 'select * from '.$this->tableType;
		return $this->oDbCon->doQuery($sql); 
	}
	
}
?>