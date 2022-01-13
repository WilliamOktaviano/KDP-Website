<?php
  
class WarehouseTransfer extends BaseClass{ 
 
   function __construct(){
		
		parent::__construct(); 
       
		$this->tableName = 'warehouse_transfer_header';
		$this->tableNameDetail = 'warehouse_transfer_detail'; 
        $this->tableNameDetailDestination = 'warehouse_transfer_destination_detail';
		$this->tableWarehouse = 'warehouse';
		$this->tableItemUnit = 'item_unit';
        $this->tableItem = 'item';
		$this->tableStatus = 'transaction_status';
        //$this->tableSerial = 'warehouse_transfer_detail_sn';
        $this->tableItemSN = 'item_sn';
		$this->isTransaction = true;
         
		$this->securityObject = 'WarehouseTransfer'; 
       
        $this->arrSN = array();  
        $this->arrSN['pkey'] = array('hidDetailSNKey');
        $this->arrSN['refkey'] = array('hidDetailKey','ref');
        $this->arrSN['serialnumber'] = array('serialNumber', array('mandatory'=>true));
       
        $this->arrDataDetail = array();   
        //$this->arrDataDetail['pkey'] = array('hidDetailKey', array('dataDetail' =>  array('dataset' => $this->arrSN, 'tableName' => $this->tableSerial)));
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
	   	$this->arrDataDetail['refkey'] = array('pkey','ref');
        $this->arrDataDetail['itemkey'] = array('hidItemKey');
        $this->arrDataDetail['qty'] = array('qty','number');
        $this->arrDataDetail['qtyinbaseunit'] = array('qtyInBaseUnit','number');
        $this->arrDataDetail['costinbaseunit'] = array('costInBaseUnit','number');
        $this->arrDataDetail['unitkey'] = array('selUnit');
        $this->arrDataDetail['unitconvmultiplier'] = array('unitConvMultiplier','number');
        $this->arrDataDetail['needsn'] = array('hidNeedSN');
 

        $this->arrDataDestDetail = array();   
        $this->arrDataDestDetail['pkey'] = array('hidDetailDestKey');
        $this->arrDataDestDetail['refkey'] = array('pkey','ref');
        $this->arrDataDestDetail['itemkey'] = array('hidItemDestKey');
        $this->arrDataDestDetail['qty'] = array('qtyDest','number');
        $this->arrDataDestDetail['qtyinbaseunit'] = array('qtyInBaseUnitDest','number');
        $this->arrDataDestDetail['costinbaseunit'] = array('costInBaseUnitDest','number');
        $this->arrDataDestDetail['unitkey'] = array('selDestUnit');
        $this->arrDataDestDetail['unitconvmultiplier'] = array('unitConvMultiplierDest','number'); 
       
        $arrDetails = array();
        array_push($arrDetails, array('dataset' => $this->arrDataDetail, 'tableName' => $this->tableNameDetail)); 
        array_push($arrDetails, array('dataset' => $this->arrDataDestDetail, 'tableName' => $this->tableNameDetailDestination));
       
        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
        $this->arrData['code'] = array('code');
        $this->arrData['trdate'] = array('trDate','date');
        $this->arrData['fromwarehousekey'] = array('selWarehouseFromKey');
        $this->arrData['towarehousekey'] = array('selWarehouseToKey');
        $this->arrData['refkey'] = array('refkey');
        $this->arrData['reftabletype'] = array('reftabletype');
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['statuskey'] = array('selStatus');
		 
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'origin','title' => 'origin','dbfield' => 'warehousefromname','default'=>true,'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'destination','title' => 'destination','dbfield' => 'warehousetoname','default'=>true ,'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
       
        $this->printMenu = array();  
        array_push($this->printMenu,array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/warehouseTransfer'));
 
        
       $this->includeClassDependencies(array(  
            'Item.class.php', 
            'ItemConversion.class.php', 
            'ItemUnit.class.php', 
            'ItemMovement.class.php', 
            'Warehouse.class.php', 
            'COALink.class.php', 
            'GeneralJournal.class.php', 
        ));
        $this->overwriteConfig();

   }
    
    function getQuery(){
	   
	   $sql = '
			SELECT '.$this->tableName.'.* ,
			   warehousefrom.name as warehousefromname,
			   warehouseto.name as warehousetoname,
			   '.$this->tableStatus.'.status as statusname
			FROM '.$this->tableStatus.', '.$this->tableName.' , '.$this->tableWarehouse.' warehousefrom,  '.$this->tableWarehouse.' warehouseto    
			WHERE '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and  '.$this->tableName.'.fromwarehousekey = warehousefrom.pkey and  '.$this->tableName.'.towarehousekey = warehouseto.pkey
		' .$this->criteria ; 
		 
        $sql .=  $this->getCompanyCriteria() ;
        return $sql;
    }  
    
    function afterStatusChanged($rsHeader){   
        // retrieve latest status
        $rsHeader = $this->getDataRowById($rsHeader[0]['pkey']);
        if ($rsHeader[0]['statuskey'] == 2)
            $this->changeStatus($rsHeader[0]['pkey'],3); 
    }
    
    function validateForm($arr,$pkey = ''){
		$item = new Item();   
		  
		$arrayToJs = parent::validateForm($arr,$pkey); 
         
		$arrItemkey = $arr['hidItemKey']; 
		$arrSelUnit = $arr['selUnit']; 
		$arrQty = $arr['qty'];

        if ($arr['selWarehouseFromKey'] == $arr['selWarehouseToKey']){  
            $this->addErrorList($arrayToJs,false,$this->errorMsg['warehouseTransfer'][2]); 
		}  
		
        
		$arrDetailKeys = array(); 
         
		for($i=0;$i<count($arrItemkey);$i++) {
			if (empty($arrItemkey[$i]) ){ 
				$this->addErrorList($arrayToJs,false, $this->errorMsg['item'][1]); 	
			}else{
                	if ( $this->unFormatNumber($arrQty[$i]) <= 0){
                        $rsItem = $item->getDataRowById($arrItemkey[$i]);
                        $this->addErrorList($arrayToJs,false,$rsItem[0]['name']. '. ' . $this->errorMsg[500]); 
                    }

                    // cek punya konversi unit utk satuan yg dipilih gk   
                    $conv = $item->getConvMultiplier($arrItemkey[$i],$arrSelUnit[$i]);
                    if (empty($conv)){
                        $rsItem = $item->getDataRowById($arrItemkey[$i]);
                        $this->addErrorList($arrayToJs,false,$rsItem[0]['name']. '. ' . $this->errorMsg['itemUnitConversion'][3]); 
                    }  
                
                    // cek ada detail double gk  
                    if (in_array($arrItemkey[$i],$arrDetailKeys)){  
                        $rsItem = $item->getDataRowById($arrItemkey[$i]);
                        $this->addErrorList($arrayToJs,false, $rsItem[0]['name'].'. '.$this->errorMsg[215]); 	 
                    }else{ 
                        array_push($arrDetailKeys, $arrItemkey[$i]);
                    }       
            }
		
		} 
       
         
		return $arrayToJs;
	 }
	 
	function validateConfirm($rsHeader){
        $id = $rsHeader[0]['pkey'];
        $warehouse = new Warehouse();  
        $coaLink = new COALink();

		$rsDetail = $this->getDetailById($id);
        
        
         if (USE_GL){ 
            $arrCOA = array();
            array_push($arrCOA, 'hpp' , 'inventory' ); 
            for ($i=0;$i<count($arrCOA);$i++){
                $rsCOA = $coaLink->getCOALink ($arrCOA[$i], $warehouse->tableName,$rsHeader[0]['fromwarehousekey'], 0); 
                if (empty($rsCOA))	
                    $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['coa'][3]); 

                $rsCOA = $coaLink->getCOALink ($arrCOA[$i], $warehouse->tableName,$rsHeader[0]['towarehousekey'], 0); 
                if (empty($rsCOA))	
                    $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['coa'][3]);
            }  
         }

          //validasi stock
        $itemMovement = new itemMovement();
        for($i=0;$i<count($rsDetail);$i++){
             $saldoakhir = $itemMovement->getItemQOH($rsDetail[$i]['itemkey'], $rsHeader[0]['fromwarehousekey']);  
             $totalqty = $saldoakhir - $rsDetail[$i]['qtyinbaseunit'];  
            if($totalqty<0){
                $item = new Item();
                $rsItem = $item->getDataRowById($rsDetail[$i]['itemkey']); 
                $this->addErrorLog(false,'<strong>'.$rsItem[0]['name'].'</strong>. '.$this->errorMsg[402]);
            }
        }
            
	 }	
    
    function updateCOGS($id){
        $item = new Item();
        $rsDetail = $this->getDetailById($id); 
        for($i=0;$i<count($rsDetail); $i++){
            $rsItem = $item->getDataRowById($rsDetail[$i]['itemkey']); 
            $sql = 'update '. $this->tableNameDetail .' set costinbaseunit = '.$this->oDbCon->paramString($rsItem[0]['cogs']).' where pkey = ' .$this->oDbCon->paramString($rsDetail[$i]['pkey']);
            $this->oDbCon->execute($sql); 
        }
    }

    function updateCOGSDestination($id){
        $item = new Item();
        $rsDetail = $this->getDetailById($id,'','',$this->tableNameDetailDestination); 
        for($i=0;$i<count($rsDetail); $i++){
            $rsItem = $item->getDataRowById($rsDetail[$i]['itemkey']); 
            $sql = 'update '. $this->tableNameDetailDestination .' set costinbaseunit = '.$this->oDbCon->paramString($rsItem[0]['cogs']).' where pkey = ' .$this->oDbCon->paramString($rsDetail[$i]['pkey']);
            $this->oDbCon->execute($sql); 
        }
    }

    function convertItemConversion($rsDetail){
        $item = new Item();
        $itemConvert = new ItemConversion();
        
        // cari konversi yg available dulu
        $rsItemConvert = $itemConvert->searchDataRow(array($itemConvert->tableName.'.itemconvertkey',' concat_ws(\'-\','.$itemConvert->tableName.'.categorykey,'.$itemConvert->tableName.'.brandkey ) as catbrandkey' ),
                                                     ' and '.$itemConvert->tableName.'.statuskey = 1'
                                                    );
        $rsItemConvert = array_column($rsItemConvert,null,'catbrandkey'); 
        $arrItemKey = $item->searchDataRow( array($item->tableName.'.pkey',$item->tableName.'.name', $item->tableName.'.categorykey',$item->tableName.'.brandkey' ),
                                            ' and '. $item->tableName.'.pkey in ('.$this->oDbCon->paramString(array_column($rsDetail,'itemkey'),',').') '
                                          );
        $arrItemKey =  array_column($arrItemKey,null,'pkey');
            
        // hitung konversi
        $arrItem = array(); 
        foreach($rsDetail as $row){
            
            $index = $arrItemKey[$row['itemkey']]['categorykey'].'-'.$arrItemKey[$row['itemkey']]['brandkey']; 
            $index = (isset($rsItemConvert[$index])) ? $rsItemConvert[$index]['itemconvertkey'] : $row['itemkey'];
            
            if(!isset($arrItem[$index])){
                $arrItem[$index]['qty'] = 0;
                $arrItem[$index]['cogs'] = 0;
            }
              
            $newCOGS = (($arrItem[$index]['qty'] * $arrItem[$index]['cogs']) + ($row['qtyinbaseunit'] * $row['costinbaseunit'])) / ($arrItem[$index]['qty']+$row['qtyinbaseunit']);  
            $arrItem[$index]['qty'] += $row['qtyinbaseunit'];
            $arrItem[$index]['cogs'] = $newCOGS;
        }
        
        return $arrItem;
    }
    
	function confirmTrans($rsHeader){
		$itemMovement = new ItemMovement();  
		$warehouse = new Warehouse();
        
		$id = $rsHeader[0]['pkey']; 
        
        $this->updateCOGS($id);
		$this->updateConvMultiplier($id); 
		$rsWarehouseFrom = $warehouse->getDataRowById($rsHeader[0]['fromwarehousekey']);
		$rsWarehouseTo = $warehouse->getDataRowById($rsHeader[0]['towarehousekey']);
	 	$rsDetail = $this->getDetailById($rsHeader[0]['pkey']);  
		        
        $destinationConversion = $this->loadSetting('warehouseTransferConversion');
     
		$note = $rsHeader[0]['code'] .'. Perpindahan Gudang dari '.$rsWarehouseFrom[0]['name'].' ke ' .$rsWarehouseTo[0]['name'];
		
		for($i=0;$i<count($rsDetail); $i++){	 
		   $itemMovement->updateItemMovement($id,$rsDetail[$i]['itemkey'],-$rsDetail[$i]['qtyinbaseunit'], $rsDetail[$i]['costinbaseunit'] ,$this->tableName, $rsHeader[0]['fromwarehousekey'], $note, $rsHeader[0]['trdate']);
           if($destinationConversion != 1) // model normal
                $itemMovement->updateItemMovement($id,$rsDetail[$i]['itemkey'],$rsDetail[$i]['qtyinbaseunit'], $rsDetail[$i]['costinbaseunit'] ,$this->tableName, $rsHeader[0]['towarehousekey'], $note, $rsHeader[0]['trdate']);
 
            if(USE_SN){
               
                $rsSN = $this->getSerialNumber($rsDetail[$i]['pkey']); 
                for($j=0;$j<count($rsSN); $j++){ 
                    
                    $rsDetail[$i]['vendorpartnumberkey'] = $rsSN[$j]['vendorpartnumberkey'];
                     
                    $arrMovementParam =  array(
                                            'refkey' => $rsDetail[$i]['pkey'],
                                            'refheaderkey' => $id,
                                            'itemkey' => $rsDetail[$i]['itemkey'],
                                            'vendorpartnumberkey' => $rsDetail[$i]['vendorpartnumberkey'],
                                            'sn' => $rsSN[$j]['serialnumber'],
                                            'qtyinbaseunit' => -1,
                                            'costinbaseunit' => $rsDetail[$i]['costinbaseunit'],
                                            'reftable' => $this->tableName,
                                            'warehousekey' => $rsHeader[0]['fromwarehousekey'],
                                            'note' => $note,
                                            'trdate' => $rsHeader[0]['trdate'] 
                                        ) ;
                    
                    $itemMovement->updateItemSNMovement($arrMovementParam);
                    
                    
                    $arrMovementParam['qtyinbaseunit'] = 1;
                    $arrMovementParam['warehousekey'] =  $rsHeader[0]['towarehousekey']; 
                        
                    $itemMovement->updateItemSNMovement($arrMovementParam); 
                     
               }

            }    
        }
        
        if($destinationConversion == 1){ 
            $rsItemConvert = $this->convertItemConversion($rsDetail);
            foreach($rsItemConvert as $row => $value)
                $itemMovement->updateItemMovement($id,$row,$value['qty'],$value['cogs'],$this->tableName, $rsHeader[0]['towarehousekey'], $note, $rsHeader[0]['trdate']);
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
		  
		/*$totalHPP = 0 ;
        $rsDetail = $this->getDetailById($rsHeader[0]['pkey']); 
        for($i=0;$i<count($rsDetail);$i++){
            $rsItem = $item->getDataRowById($rsDetail[$i]['itemkey']);
            $totalHPP += ($rsItem[0]['cogs'] * $rsDetail[$i]['qtyinbaseunit']);
        }*/
        
        $temp = -1;
      
        $rsDetail = $this->getDetailById($rsHeader[0]['pkey']); 
        
        // from warehousekey
        $totalHPP = 0;
        $arrItemCOA = array(); 
        foreach($rsDetail as $detail){
            $itemCOAKey = $item->getInventoryCOAKey($detail['itemkey'],$rsHeader[0]['fromwarehousekey']); 
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
        
        // to warehousekey
        $totalHPP = 0;
        $arrItemCOA = array(); 
        foreach($rsDetail as $detail){
            $itemCOAKey = $item->getInventoryCOAKey($detail['itemkey'],$rsHeader[0]['towarehousekey']); 
            $totalItemValue =  $detail['costinbaseunit']*$detail['qtyinbaseunit']; 
            $arrItemCOA[$itemCOAKey] = (!isset($arrItemCOA[$itemCOAKey])) ? $totalItemValue : $arrItemCOA[$itemCOAKey] + $totalItemValue; 
        }
        
        foreach ($arrItemCOA as $coakey => $coaValue){ 
            $temp++;
            $arr['hidCOAKey'][$temp] = $coakey;
            $arr['debit'][$temp] = $coaValue; 
            $arr['credit'][$temp] = 0; 
            $arr['refCashBankKey'][$temp] = '';
            
            $totalHPP += $coaValue;
        }        
        
        
/*       // hpp gk ikut pindah

        $rsCOA = $coaLink->getCOALink ('hpp', $warehouse->tableName,$rsHeader[0]['fromwarehousekey'], 0);  
        $temp++;
        $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
        $arr['debit'][$temp] = $totalHPP; 
        $arr['credit'][$temp] = 0;  */
         
/*        $rsCOA = $coaLink->getCOALink ('inventory', $warehouse->tableName,$rsHeader[0]['towarehousekey'], 0);  
        $temp++;
        $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
        $arr['debit'][$temp] = $totalHPP; 
        $arr['credit'][$temp] = 0;  */
        
         
/*        $rsCOA = $coaLink->getCOALink ('inventory', $warehouse->tableName,$rsHeader[0]['fromwarehousekey'], 0);  
        $temp++;
        $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
        $arr['debit'][$temp] = 0; 
        $arr['credit'][$temp] = $totalHPP; */
 
/*        $rsCOA = $coaLink->getCOALink ('hpp', $warehouse->tableName,$rsHeader[0]['towarehousekey'], 0);  
        $temp++;
        $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
        $arr['debit'][$temp] = 0; 
        $arr['credit'][$temp] = $totalHPP;  */
        
        $arr['_mnv_ungroup'] = 1;
        //$this->setLog($arr,true);
        
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
    
    function getDetailWithRelatedInformation($pkey, $criteria=''){
	   $sql = 'select
	   			'.$this->tableNameDetail .'.*,
                '.$this->tableNameDetail.'.qty * '.$this->tableNameDetail.'.costinbaseunit as totalcogs, 
                '.$this->tableItem.'.name as itemname, 
                '.$this->tableItem.'.code as itemcode,
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
			  	refkey = '.$this->oDbCon->paramString($pkey);
        
        $sql .= $criteria;
        
		return $this->oDbCon->doQuery($sql);
	
   }
    
   function getDetailWithRelatedInformationDestination($pkey, $criteria=''){
    $sql = 'select
             '.$this->tableNameDetailDestination .'.*,
             '.$this->tableNameDetailDestination.'.qty * '.$this->tableNameDetailDestination.'.costinbaseunit as totalcogs, 
             '.$this->tableItem.'.name as itemname, 
             '.$this->tableItem.'.code as itemcode,
             '.$this->tableItemUnit.'.name as unitname,
             baseunit.name as baseunitname,
             concat(\' / \',baseunit.name) as cogsunit 
           from
               '. $this->tableNameDetailDestination .',
             '.$this->tableItem.',
             '.$this->tableItemUnit.',
             '.$this->tableItemUnit.' baseunit
           where
               ' . $this->tableNameDetailDestination .'.itemkey = '.$this->tableItem.'.pkey and
               ' . $this->tableNameDetailDestination .'.unitkey = '.$this->tableItemUnit.'.pkey and
               ' . $this->tableItem .'.baseunitkey = baseunit.pkey and
               refkey = '.$this->oDbCon->paramString($pkey);
     
     $sql .= $criteria;
     
     return $this->oDbCon->doQuery($sql);
 
}
    
    function reCalculateConversionDetailDest($arrParam){
 
				$itemdestkey = $arrParam['hidItemDestKey'];
                $item = new Item();
			  
                $arrTransUnitKey = (isset($arrParam['selDestUnit']) && !empty($arrParam['selDestUnit'])) ? $arrParam['selDestUnit'] : array();
        
				$arrQty = $arrParam['qtyDest'];

                $baseUnitDestKey = array();
                $unitDestConvMultiplier = array();
                $qtyDestInBaseUnit = array();
        
				for ($i=0;$i<count($itemdestkey);$i++){
					
					if (empty($itemdestkey[$i]))  
						continue;

                    $qty =  $this->unFormatNumber($arrQty[$i]);
                    
                    $rsItem = $item->getDataRowById($itemdestkey[$i]);
                    
                    if (!isset($arrTransUnitKey[$i]) || empty($arrTransUnitKey[$i]))
                        $arrTransUnitKey[$i] = $rsItem[0]['baseunitkey'];
                    
                    $baseUnitDestKey[$i] = $rsItem[0]['baseunitkey'];
                    $unitDestConvMultiplier[$i] = $item->getConvMultiplier($itemdestkey[$i],$arrTransUnitKey[$i], $baseUnitDestKey[$i]); 
                    $qtyDestInBaseUnit[$i] = $unitDestConvMultiplier[$i]  * $qty; 
						 
				} 
				    
				
				$reCountDestResult = array(); 
				$reCountDestResult['baseUnitKey'] = $baseUnitDestKey;
				$reCountDestResult['unitConvMultiplier'] = $unitDestConvMultiplier;
				$reCountDestResult['qtyInBaseUnit'] = $qtyDestInBaseUnit;
				 
				return $reCountDestResult;
				
	}

    function normalizeParameter($arrParam, $trim = false){
          
        $item = new Item();
         
        $arrParam = parent::normalizeParameter($arrParam); 
          
        $reCountResult = $this->reCalculateConversionDetail($arrParam); 
        $reCountDestResult = $this->reCalculateConversionDetailDest($arrParam); 
         
        $arrParam['baseUnitKey'] =  $reCountResult['baseUnitKey']; 
        $arrParam['unitConvMultiplier'] =  $reCountResult['unitConvMultiplier']; 
        $arrParam['qtyInBaseUnit'] =  $reCountResult['qtyInBaseUnit']; 

        $arrParam['baseUnitDestKey'] =  $reCountDestResult['baseUnitKey']; 
        $arrParam['unitConvMultiplierDest'] =  $reCountDestResult['unitConvMultiplier']; 
        $arrParam['qtyInBaseUnitDest'] =  $reCountDestResult['qtyInBaseUnit']; 
        
	 	$arrSerialNumber = $arrParam['snList'];
        $arrParam['serialNumber'] = array();
        $arrParam['hidDetailSNKey'] = array();
        $arrParam['hidDetailSNKeyTotalRows'][1] = array();
        

        $arrItemkey = $arrParam['hidItemKey'];  
        
        for ($i=0;$i<count($arrItemkey);$i++){
            $rsItem = $item->getDataRowById($arrItemkey[$i]);   
            $arrParam['costInBaseUnit'][$i] = $rsItem[0]['cogs'];
             
            $arrSNItem =  preg_split('/[\n, ]+/', $arrSerialNumber[$i]);
            
            $rowCtr = 0;
            for($j=0;$j<count($arrSNItem);$j++){
                                    
                $sn = preg_replace("/[^A-Za-z0-9]/", '', $arrSNItem[$j]); 
                if (empty($sn)) continue;
    
                $sn = strtoupper($sn);
                array_push($arrParam['serialNumber'],$sn); 
                array_push($arrParam['hidDetailSNKey'],0);  
                $rowCtr++;
            }      
            
            array_push($arrParam['hidDetailSNKeyTotalRows'][1],$rowCtr);  
        }
        
        $details = array();
        array_push($details,$this->arrSN);
        $arrParam = $this->prepareMultiLevelDetail($arrParam,$details);
        
        return $arrParam;
    }
      function generateWarehouseTransferReport($criteria='',$detailCriteria='',$order='',$pkey=''){
	   // gk bisa join langsung dengan Job Order atau SPK, karean tergantung tabletype
        
	   $sql =  '
			SELECT '.$this->tableName.'.code,
                    warehousefrom.name as warehousefromname,
			        warehouseto.name as warehousetoname,
                   '.$this->tableName.'.trdate, 
                   '.$this->tableName.'.trdesc, 
                   '.$this->tableItem.'.code as itemcode,
                   '.$this->tableItem.'.name as itemname,
                   '.$this->tableNameDetail.'.qty, 
                   '.$this->tableNameDetail.'.costinbaseunit, 
                   '.$this->tableNameDetail.'.qty * '.$this->tableNameDetail.'.costinbaseunit as totalcogs, 
                   '.$this->tableStatus.'.status as statusname , 
                   '.$this->tableItemUnit.'.name as unitname,
                   concat(\' / \',baseunit.name) as cogsunit 
			FROM 
                '.$this->tableStatus.',  
                '.$this->tableItem.', 
                '.$this->tableNameDetail.',
                '.$this->tableName.',
                '.$this->tableWarehouse.' warehousefrom, 
                '.$this->tableWarehouse.' warehouseto,
                '.$this->tableItemUnit.',
                '.$this->tableItemUnit.' baseunit
			WHERE     
                '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and 
                '.$this->tableName.'.fromwarehousekey = warehousefrom.pkey and  
                '.$this->tableName.'.towarehousekey = warehouseto.pkey and
                '.$this->tableNameDetail.'.refkey = '.$this->tableName.'.pkey and 
                '.$this->tableNameDetail .'.itemkey = '.$this->tableItem.'.pkey and 
                '.$this->tableNameDetail .'.unitkey = '.$this->tableItemUnit.'.pkey and
			  	'.$this->tableItem .'.baseunitkey = baseunit.pkey  
                
 		'; 
        
        if (!empty($criteria))  
            $sql .=  ' ' .$criteria;
        
        if (!empty($detailCriteria))  
            $sql .=  ' ' .$detailCriteria; 
        
        if (!empty($pkey))  
            $sql .=  '  and '.$this->tableName.'.pkey = ' .$this->oDbCon->paramString($pkey);

        if (!empty($order))  
            $sql .=  ' ' .$order; 
          
       return $this->oDbCon->doQuery($sql);
		 
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
    
}
?>
