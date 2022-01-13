<?php 
class ItemMovement extends BaseClass{
 
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'item_movement' ;   
		$this->tableItemInWarehouse = 'item_in_warehouse' ; 
		$this->tableSNMovement = 'item_sn_movement' ;  
		$this->tableItem = 'item' ; 
		$this->tableItemVendorPartNumber = 'item_vendor_part_number' ; 
		$this->tableItemUnit = 'item_unit' ; 
		$this->tableWarehouse = 'warehouse' ;  
        $this->tableSerialNumber = 'item_sn';
        $this->tableWarranty = 'warranty_claim_header';
        $this->tableWarrantyVendor = 'vendor_warranty_claim_header';
		$this->tableCustomer = 'customer' ; 
		$this->tableSupplier = 'supplier' ; 
		$this->tableSalesOrderHeader = 'sales_order_header' ; 
		$this->tableSalesOrderDetail = 'sales_order_detail' ; 
		$this->tableRentalMovement = 'item_rental_movement' ; 
 		 
   }
   
   function getQuery(){
	     
	   return '
		    select 
				 '.$this->tableName.'.*,
				 '. $this->tableWarehouse .'.name as warehousename,
				 '.$this->tableItemUnit.'.name as baseunitname 
			from  
                '.$this->tableName.' 
                    left join ' . $this->tableWarehouse .' on  '.$this->tableName.'.warehousekey = ' . $this->tableWarehouse .'.pkey,
                
                '.$this->tableItemUnit.',
                '.$this->tableItem.'    
			where
				 '.$this->tableName.'.itemkey =  '.$this->tableItem.'.pkey and
                 '.$this->tableItem.'.baseunitkey =  '.$this->tableItemUnit.'.pkey 
		   ' .$this->criteria ; 
   }
    
   function sumItemsMovement($arrItemKey, $warehousekey = '',$endDate=''){
      	$criteria = '';
		if (!empty($warehousekey)){
            
            if(!is_array($warehousekey))
                $warehousekey = explode(',',$warehousekey);
            
            $warehousekey = implode(',',$this->oDbCon->paramString($warehousekey));
            
			$criteria .= ' and warehousekey in ('.$warehousekey.')';  
		}
       
		if (!empty($endDate)){
            $dateMethod = $this->loadSetting('movementDateMethod');  
            $datefield = ($dateMethod == 2)  ? 'trdate' : 'createdon';  
            $criteria .= ' and '.$datefield.' <= '.$this->oDbCon->paramDate($endDate,' / ', 'Y-m-d 23:59:59'); 
		}
       
         $sql = 'select 
                        '.$this->tableName.'.itemkey, 
                        coalesce(sum('.$this->tableName.'.qtyinbaseunit),0) as "qtyinbaseunit" 
                    from '.$this->tableName.'  where '.$this->tableName.'.statuskey = 1 and '.$this->tableName.'.itemkey in ('. $this->oDbCon->paramString($arrItemKey, ',' ) . ') '. $criteria.
                    ' group by '.$this->tableName.'.itemkey';
 
           
        //$this->setLog($sql,true);
        
        $rs =  $this->oDbCon->doQuery($sql);	 
        return $rs;     

   }
   
     function sumItemMovement($itemkey, $warehousekey = '',$endDate=''){
		       
		$criteria = '';
		if (!empty($warehousekey)){
            
            if(!is_array($warehousekey))
                $warehousekey = explode(',',$warehousekey);
            
            $warehousekey = implode(',',$this->oDbCon->paramString($warehousekey));
            
			$criteria .= ' and warehousekey in ('.$warehousekey.')';
            //$criteria .= $this->getCompanyCriteria($this->tableWarehouse);
            
		}
		if (!empty($endDate)){
            $dateMethod = $this->loadSetting('movementDateMethod');  
            $datefield = ($dateMethod == 2)  ? 'trdate' : 'createdon';  
            $criteria .= ' and '.$datefield.' <= '.$this->oDbCon->paramDate($endDate,' / ', 'Y-m-d 23:59:59'); 
		}
       
       
		$sql = 'select coalesce(sum(qtyinbaseunit),0) as "qtyinbaseunit" from '.$this->tableName.'  where statuskey = 1 and itemkey in ('. $itemkey . ') '. $criteria;	
       
        $rs =  $this->oDbCon->doQuery($sql);		 
	 	return $rs[0]['qtyinbaseunit'];
	}
	
	
    function sumItemsCOGSMovement($arrItemKey, $warehousekey = '',$endDate=''){
		       
		$criteria = '';
		if (!empty($warehousekey)){
            
            if(!is_array($warehousekey))
                $warehousekey = explode(',',$warehousekey);
            
            $warehousekey = implode(',',$this->oDbCon->paramString($warehousekey));
            
			$criteria .= ' and warehousekey in ('.$warehousekey.')';
		}
		if (!empty($endDate)){
            $dateMethod = $this->loadSetting('movementDateMethod'); 
            
            $datefield = 'createdon';
            if ($dateMethod == 2) 
                $datefield = 'trdate'; 
            
            $criteria .= ' and '.$datefield.' <= '.$this->oDbCon->paramDate($endDate,' / ', 'Y-m-d 23:59:59'); 
		}
       
       
		$sql = 'select 
                     '.$this->tableName.'.itemkey,
                    coalesce(sum( '.$this->tableName.'.qtyinbaseunit *  '.$this->tableName.'.costinbaseunit),0) as "costinbaseunit"
                from '.$this->tableName.' 
                where  '.$this->tableName.'.statuskey = 1 and  '.$this->tableName.'.itemkey in ('. $this->oDbCon->paramString($arrItemKey,',') . ') '. $criteria .'
                group by '.$this->tableName.'.itemkey
                ';		 
        
		$rs =  $this->oDbCon->doQuery($sql);		 
	 	return $rs;
	}
    
    function sumItemCOGSMovement($itemkey, $warehousekey = '',$endDate=''){
		       
		$criteria = '';
		if (!empty($warehousekey)){
            
            if(!is_array($warehousekey))
                $warehousekey = explode(',',$warehousekey);
            
            $warehousekey = implode(',',$this->oDbCon->paramString($warehousekey));
            
			$criteria .= ' and warehousekey in ('.$warehousekey.')';
		}
		if (!empty($endDate)){
            $dateMethod = $this->loadSetting('movementDateMethod'); 
            
            $datefield = 'createdon';
            if ($dateMethod == 2) 
                $datefield = 'trdate'; 
            
            $criteria .= ' and '.$datefield.' < '.$this->oDbCon->paramDate($endDate,' / ', 'Y-m-d 23:59:59'); 
		}
       
       
		$sql = 'select coalesce(sum(qtyinbaseunit * costinbaseunit),0) as "costinbaseunit"
                from '.$this->tableName.' 
                where statuskey = 1 and itemkey in ('. $this->oDbCon->paramString($itemkey,',') . ') '. $criteria .'
                group by '.$this->tableName.'.itemkey
                ';		 
        
        //$this->setLog($sql,true);
       
		$rs =  $this->oDbCon->doQuery($sql);		 
	 	return $rs[0]['costinbaseunit'];
	}
	
	 
   function getItemQOH($itemkey, $warehousekey = ''){ 
	
		$criteria = '';
		if (!empty($warehousekey)){
            
            if(!is_array($warehousekey))
                $warehousekey = explode(',',$warehousekey);
             
            $warehousekey = implode(',',$this->oDbCon->paramString($warehousekey));
			$criteria .= ' and warehousekey in ('.$warehousekey.')';
		}
       
        // filter by company warehouse
        $warehouse = new Warehouse();
        $arrWarehouse = implode(',',$warehouse->getCompanyWarehouse());
        if (!empty($arrWarehouse)) 
            $criteria .= ' and warehousekey in ('.$arrWarehouse.')';
        
        
		$sql = 'select coalesce(sum(qtyinbaseunit),0) as "qtyinbaseunit" from '.$this->tableItemInWarehouse.'  where itemkey = '.$this->oDbCon->paramString($itemkey) . $criteria;		 
      
		$rs =  $this->oDbCon->doQuery($sql);		 
	 	return $rs[0]['qtyinbaseunit'];
	}
	  	 
   function getItemsQOH($arrItemKey, $warehousekey = ''){
       
		$criteria = '';
		if (!empty($warehousekey)){
            
            if(!is_array($warehousekey))
                $warehousekey = explode(',',$warehousekey);
             
            $warehousekey = implode(',',$this->oDbCon->paramString($warehousekey));
			$criteria .= ' and warehousekey in ('.$warehousekey.')';
		}
       
        // filter by company warehouse
        $warehouse = new Warehouse();
        $arrWarehouse = implode(',',$warehouse->getCompanyWarehouse());
        if (!empty($arrWarehouse)) 
            $criteria .= ' and warehousekey in ('.$arrWarehouse.')';
        
        
		$sql = 'select 
                    itemkey,
                    isvariant,
                    parentkey,
                    '.$this->tableItem.'.code as itemcode,
                    coalesce(sum(qtyinbaseunit),0) as "qtyinbaseunit" 
                from 
                    '.$this->tableItemInWarehouse.', '.$this->tableItem.' 
                where 
                    itemkey in('.$this->oDbCon->paramString($arrItemKey,',').') ' . $criteria .' and
                    '.$this->tableItemInWarehouse.'.itemkey = '.$this->tableItem.'.pkey 
                group by itemkey ';		 
                 
	 	return  $this->oDbCon->doQuery($sql);
	}
	  
    function getItemQOR($itemkey, $warehousekey = ''){
       
		$criteria = '';
		if (!empty($warehousekey)){
            
            if(!is_array($warehousekey))
                $warehousekey = explode(',',$warehousekey);
             
            $warehousekey = implode(',',$this->oDbCon->paramString($warehousekey));
			$criteria .= ' and warehousekey in ('.$warehousekey.')';
		}
       
        // filter by company warehouse
        $warehouse = new Warehouse();
        $arrWarehouse = implode(',',$warehouse->getCompanyWarehouse());
        if (!empty($arrWarehouse)) 
            $criteria .= ' and warehousekey in ('.$arrWarehouse.')';
        
       
		$sql = 'select coalesce(sum(qtyonreserveinbaseunit),0) as "qtyonreserveinbaseunit" from '.$this->tableItemInWarehouse.'  where itemkey = '.$this->oDbCon->paramString($itemkey) . $criteria;		 
        
		$rs =  $this->oDbCon->doQuery($sql);		 
	 	return $rs[0]['qtyonreserveinbaseunit'];
	}
    
    function getItemsQOR($arrItemKey, $warehousekey = ''){
       
		$criteria = '';
		if (!empty($warehousekey)){
            
            if(!is_array($warehousekey))
                $warehousekey = explode(',',$warehousekey);
             
            $warehousekey = implode(',',$this->oDbCon->paramString($warehousekey));
			$criteria .= ' and warehousekey in ('.$warehousekey.')';
		}
       
        // filter by company warehouse
        $warehouse = new Warehouse();
        $arrWarehouse = implode(',',$warehouse->getCompanyWarehouse());
        if (!empty($arrWarehouse)) 
            $criteria .= ' and warehousekey in ('.$arrWarehouse.')';
        
        
		$sql = 'select 
                    itemkey,
                    isvariant,
                    parentkey,
                    '.$this->tableItem.'.code as itemcode,
                    coalesce(sum(qtyonreserveinbaseunit),0) as "qtyonreserveinbaseunit" 
                from 
                    '.$this->tableItemInWarehouse.', '.$this->tableItem.' 
                where 
                    itemkey in('.$this->oDbCon->paramString($arrItemKey,',').') ' . $criteria .' and
                    '.$this->tableItemInWarehouse.'.itemkey = '.$this->tableItem.'.pkey 
                group by itemkey ';		 
                 
	 	return  $this->oDbCon->doQuery($sql);
	}
	  

    function getItemSNQOH($itemkey, $sn, $warehousekey = '', $vendorpartnumberkey = ''){
       
		$criteria = '';
		if (!empty($warehousekey)){
            if(!is_array($warehousekey))
                $warehousekey = explode(',',$warehousekey);
            
            $warehousekey = implode(',',$this->oDbCon->paramString($warehousekey));
			$criteria .= ' and warehousekey in ('.$warehousekey.')';
		}
       
        // filter by company warehouse
        $warehouse = new Warehouse();
        $arrWarehouse = implode(',',$warehouse->getCompanyWarehouse());
        if (!empty($arrWarehouse)) 
            $criteria .= ' and warehousekey in ('.$arrWarehouse.')';
        
        if (!empty($vendorpartnumberkey)) 
			$criteria .= ' and vendorpartnumberkey = ' . $this->oDbCon->paramString($vendorpartnumberkey);
		  
		$sql = 'select coalesce(sum(qtyinbaseunit),0) as "qtyinbaseunit" from '.$this->tableSNMovement.'  where '.$this->tableSNMovement.'.statuskey = 1 and itemkey = '.$this->oDbCon->paramString($itemkey) .' and serialnumber = '. $this->oDbCon->paramString($sn) . $criteria;		 
         
        $rs =  $this->oDbCon->doQuery($sql);		 
	 	return $rs[0]['qtyinbaseunit'];
	}
	
	function updateItemSNMovement($arrParam){
        return ;
		
        //$this->setLog($arrParam);
		
        $refkey = $arrParam['refkey'];
        $refheaderkey = $arrParam['refheaderkey'];
        $itemkey = $arrParam['itemkey'];
        $vendorpartnumberkey = $arrParam['vendorpartnumberkey'];
        $qtyinbaseunit = $arrParam['qtyinbaseunit'];
        $sn = strtoupper($arrParam['sn']);
        $costinbaseunit = $arrParam['costinbaseunit'];
        $reftable = $arrParam['reftable'];
        $warehousekey = $arrParam['warehousekey'];
        $note = $arrParam['note'];
        $trdate = $arrParam['trdate'];
        
        $warrantyPeriodKey = (isset($arrParam['warrantyperiodkey'])) ? $arrParam['warrantyperiodkey'] : 0;
        $warrantyPeriodTime = (isset($arrParam['warrantyperiodtime'])) ? $arrParam['warrantyperiodtime'] : 0;
        $warrantyPeriodEndDate = (isset($arrParam['warrantyperiodexpireddate'])) ? $arrParam['warrantyperiodexpireddate'] :DEFAULT_EMPTY_DATE  ;
             
	    $warrantyVendorPeriodKey = (isset($arrParam['warrantyvendorperiodkey'])) ? $arrParam['warrantyvendorperiodkey'] : 0;
        $warrantyVendorPeriodTime = (isset($arrParam['warrantyvendorperiodtime'])) ? $arrParam['warrantyvendorperiodtime'] : 0;
        $warrantyVendorPeriodEndDate = (isset($arrParam['warrantyvendorperiodexpireddate'])) ? $arrParam['warrantyvendorperiodexpireddate'] : DEFAULT_EMPTY_DATE ;
      
		$createdby =  base64_decode($_SESSION[$this->loginAdminSession]['id']);
		 
        $totalqty = 0;
		$saldoakhir = $this->getItemSNQOH($itemkey, $sn, $warehousekey); 
		$totalqty = $saldoakhir + $qtyinbaseunit;
  
        
		if($totalqty < 0){   
		 	$item = new Item();
			$rsItem = $item->getDataRowById($itemkey);
            
			throw new Exception('<strong>'.$rsItem[0]['name']. ', '. $sn. '</strong>. ' .$this->errorMsg[402]);
		}
			  
        //$arrType = $this->getMovementType($reftable);
        $arrType = $this->getTableKeyAndObj($reftable);
        
		$sql = '
			INSERT INTO		
			  '.$this->tableSNMovement.' (
			  	refkey,
			  	refheaderkey,
                trdate,
				itemkey,
                vendorpartnumberkey,
                serialnumber,
				warehousekey, 
				qtyinbaseunit,
				costinbaseunit,
				reftabletype,  
                warrantyperiodkey,
                warrantyperiod,
                warrantyperiodexpireddate,
                warrantyvendorperiodkey,
                warrantyvendorperiod,
                warrantyvendorperiodexpireddate,
				statuskey,
				createdon,
				createdby
			)
			VALUES (
				'.$this->oDbCon->paramString($refkey).',
				'.$this->oDbCon->paramString($refheaderkey).',
				'.$this->oDbCon->paramString($trdate).',
				'.$this->oDbCon->paramString($itemkey).', 
				'.$this->oDbCon->paramString($vendorpartnumberkey).', 
				'.$this->oDbCon->paramString($sn).', 
				'.$this->oDbCon->paramString($warehousekey).',
				'.$this->oDbCon->paramString($qtyinbaseunit).',
				'.$this->oDbCon->paramString($costinbaseunit).',
				'.$this->oDbCon->paramString($arrType['key']).', 
				'.$this->oDbCon->paramString($warrantyPeriodKey).', 
				'.$this->oDbCon->paramString($warrantyPeriodTime).', 
				'.$this->oDbCon->paramDate($warrantyPeriodEndDate,' / ').', 
				'.$this->oDbCon->paramString($warrantyVendorPeriodKey).', 
				'.$this->oDbCon->paramString($warrantyVendorPeriodTime).', 
				'.$this->oDbCon->paramDate($warrantyVendorPeriodEndDate,' / ').', 
				1  ,
				now(),
				'.$this->oDbCon->paramString($createdby).'
			)';			 
           
		$this->oDbCon->execute($sql);  

          
        // item ITEM_SN
        
        // insertItemSN ==> SN baru, pembelian
        // removeItemSN ==> SN keluar, terjual atau claim garansi ke vendor
        // updateItemSN ==> SN masuk lg, retur dr vendor sebagai item baru (garansi kereset / update)
        
         if($qtyinbaseunit > 0){
            $result = $this->searchItemSerialNumber($sn);  
            
            if(!empty($result))  { 
                 // kalo terakhir statusnya masi dlm gudang, harusnya gk boleh masuk lg 
                 if ( $result[0]['warehousekey'] <> 0 )
                    throw new Exception('<strong>'.$sn. '</strong>. ' .$this->errorMsg['serialnumber'][3]);  
                
                $this->updateItemSN($arrParam);
            }else{  
                $this->insertItemSN($arrParam);
            }
        }else if($qtyinbaseunit < 0 ){    
            $this->removeItemSN($arrParam); 
         }
		return true;
	}
    
    
    function updateItemSN($arrParam){
          
        // kalo insert, pasti udpate summary karena SN baru
        
        //$refkey = $arrParam['refkey'];
        //$refheaderkey = $arrParam['refheaderkey'];
        $itemkey = $arrParam['itemkey'];
        //$vendorpartnumberkey = $arrParam['vendorpartnumberkey'];
        $sn = $arrParam['sn'];
        $warehousekey = $arrParam['warehousekey'];
         
        $warrantyPeriodKey = (isset($arrParam['warrantyperiodkey'])) ? $arrParam['warrantyperiodkey'] : 0;
        $warrantyPeriodTime = (isset($arrParam['warrantyperiodtime'])) ? $arrParam['warrantyperiodtime'] : 0;
             
	    $warrantyVendorPeriodKey = (isset($arrParam['warrantyvendorperiodkey'])) ? $arrParam['warrantyvendorperiodkey'] : 0;
        $warrantyVendorPeriodTime = (isset($arrParam['warrantyvendorperiodtime'])) ? $arrParam['warrantyvendorperiodtime'] : 0;
       
        
        $result = $this->searchItemSerialNumber($sn); 
        if(empty($result)) 
            throw new Exception('<strong>'.$sn. '</strong>. ' .$this->errorMsg['serialnumber'][4]);  
        
        $arrSQL = array(); 
        if (isset($arrParam['warrantyperiodexpireddate']))
            array_push($arrSQL, 'warrantyperiodexpireddate = '.$this->oDbCon->paramDate($arrParam['warrantyperiodexpireddate'],' / '));
                     
        if (isset($arrParam['warrantyvendorperiodexpireddate']))
            array_push($arrSQL, 'warrantyvendorperiodexpireddate = '.$this->oDbCon->paramDate($arrParam['warrantyvendorperiodexpireddate'],' / '));
               
         
        $rsSnMovement = $this->searchSNMovement($itemkey,'',$sn,'',' and '.$this->tableSNMovement.'.statuskey = 1' ,' order by trdate desc,  '.$this->tableSNMovement.'.pkey desc limit 1');
        if(!empty($rsSnMovement)) 
            $warehousekey = ($rsSnMovement[0]['qtyinbaseunit'] < 0) ? 0 : $rsSnMovement[0]['warehousekey']; 

        $sql = 'UPDATE	
                     '.$this->tableSerialNumber .'
                    SET	  
                     warehousekey ='.$this->oDbCon->paramString($warehousekey);

        if (!empty($arrSQL)) $sql .= ',';
        $sql .= implode (',',$arrSQL);

        $sql .= ' WHERE	
                     itemkey = '.$this->oDbCon->paramString($itemkey).' and
                     serialnumber = '.$this->oDbCon->paramString($sn).'
            ';    


          $this->oDbCon->execute($sql);
    }

    function insertItemSN($arrParam){
          
        // kalo insert, pasti udpate summary karena SN baru
         
        $refkey = $arrParam['refkey'];
        $refheaderkey = $arrParam['refheaderkey'];
        $itemkey = $arrParam['itemkey']; 
        $trdate = $arrParam['trdate']; // formatnya sudah tgl database
        
        $vendorpartnumberkey = $arrParam['vendorpartnumberkey'];
        $sn = $arrParam['sn'];
        $warehousekey = $arrParam['warehousekey'];
         
        $warrantyPeriodKey = (isset($arrParam['warrantyperiodkey'])) ? $arrParam['warrantyperiodkey'] : 0;
        $warrantyPeriodTime = (isset($arrParam['warrantyperiodtime'])) ? $arrParam['warrantyperiodtime'] : 0;
        $warrantyPeriodEndDate = (isset($arrParam['warrantyperiodexpireddate'])) ? $arrParam['warrantyperiodexpireddate'] : DEFAULT_EMPTY_DATE;
             
	    $warrantyVendorPeriodKey = (isset($arrParam['warrantyvendorperiodkey'])) ? $arrParam['warrantyvendorperiodkey'] : 0;
        $warrantyVendorPeriodTime = (isset($arrParam['warrantyvendorperiodtime'])) ? $arrParam['warrantyvendorperiodtime'] : 0;
        $warrantyVendorPeriodEndDate = (isset($arrParam['warrantyvendorperiodexpireddate'])) ? $arrParam['warrantyvendorperiodexpireddate'] : DEFAULT_EMPTY_DATE;
        $supplierkey = (isset($arrParam['supplierkey'])) ? $arrParam['supplierkey'] : 0;
        $reftabletype = (isset($arrParam['reftabletype'])) ? $arrParam['reftabletype'] : 0;
    
        // gk perlu validasi, karena dr item in vendor jg bisa punya SN yg sama
        
/*        $result = $this->searchItemSerialNumber($sn); 
        if(!empty($result)) 
            throw new Exception('<strong>'.$sn. '</strong>. ' .$this->errorMsg['serialnumber'][3]);  */
        
        $sql = 'INSERT INTO '.$this->tableSerialNumber.' (
                    refkey, 
                    refheaderkey,
                    warehousekey,
                    itemkey,
                    vendorpartnumberkey,
                    serialnumber,
                    warrantyperiodkey,
                    warrantyperiod,
                    warrantyperiodexpireddate,
                    warrantyvendorperiodkey,
                    warrantyvendorperiod,
                    warrantyvendorperiodexpireddate, 
                    reftabletype,
                    itemindate
                ) VALUES ( 
                    '.$this->oDbCon->paramString($refkey).',
                    '.$this->oDbCon->paramString($refheaderkey).',
                    '.$this->oDbCon->paramString($warehousekey).',
                    '.$this->oDbCon->paramString($itemkey).',
                    '.$this->oDbCon->paramString($vendorpartnumberkey).',
                    '.$this->oDbCon->paramString($sn).', 
                    '.$this->oDbCon->paramString($warrantyPeriodKey).', 
                    '.$this->oDbCon->paramString($warrantyPeriodTime).', 
                    '.$this->oDbCon->paramDate($warrantyPeriodEndDate,' / ').', 
                    '.$this->oDbCon->paramString($warrantyVendorPeriodKey).', 
                    '.$this->oDbCon->paramString($warrantyVendorPeriodTime).', 
                    '.$this->oDbCon->paramDate($warrantyVendorPeriodEndDate,' / ').', 
                    '.$this->oDbCon->paramString($reftabletype).',
                    '.$this->oDbCon->paramString($trdate).'  
                )';
 
          $this->oDbCon->execute($sql);
    }
    
    function deleteItemSN($sn){
         
        $result = $this->searchItemSerialNumber($sn,' and warehousekey <> 0'); 
        if(empty($result)) 
            throw new Exception('<strong>'.$sn. '</strong>. ' .$this->errorMsg['serialnumber'][4]);  
        
        $sql = 'delete from  '.$this->tableSerialNumber.' where serialnumber = ' . $this->oDbCon->paramString($sn); 
        $this->oDbCon->execute($sql);
    }
    
    function removeItemSN($arrParam){
        // terjual
        // kirim ke vendor utk retur
        
        $tableKeyUpdate = array();
        
        $arrObj = array(new ItemOut(), new ItemOutDelivery(), new WarrantyClaimProgress());
        foreach($arrObj as $obj){
            $tableKey = $this->getTableKeyAndObj($obj->tableName);
            array_push($tableKeyUpdate, $tableKey['key']);
        }
          
        $sn = $arrParam['sn'];
        $warrantyPeriodKey = (isset($arrParam['warrantyperiodkey'])) ? $arrParam['warrantyperiodkey'] : 0;
        $warrantyPeriodTime = (isset($arrParam['warrantyperiodtime'])) ? $arrParam['warrantyperiodtime'] : 0;
        $warrantyPeriodEndDate = (isset($arrParam['warrantyperiodexpireddate'])) ? $arrParam['warrantyperiodexpireddate'] : $this->oDbCon->paramDate(DEFAULT_EMPTY_DATE,' / ');
 
         
        $result = $this->searchItemSerialNumber($sn); 
        if(empty($result)) 
            throw new Exception('<strong>'.$sn. '</strong>. ' .$this->errorMsg['serialnumber'][4]);  
          
        // tablekey for item out
        $itemOutTableTypeKey = $this->getTableKeyAndObj($arrParam['reftable']);
        $itemOutTableTypeKey = $itemOutTableTypeKey['key'];
        
        $sqlUpdateSummary = '';
        if (in_array($itemOutTableTypeKey,$tableKeyUpdate)){
            $sqlUpdateSummary = ',
                    warrantyperiodkey = '.$this->oDbCon->paramString($warrantyPeriodKey).',
                    warrantyperiod = '.$this->oDbCon->paramString($warrantyPeriodTime).',
                    warrantyperiodexpireddate =  '.$this->oDbCon->paramDate($warrantyPeriodEndDate,' / ').',
                    itemoutrefkey = '.$arrParam['refkey'].' ,
                    itemoutrefheaderkey = '.$arrParam['refheaderkey'].' ,
                    itemoutreftabletype = '.$this->oDbCon->paramString($itemOutTableTypeKey).' 
             ';
        }
        
        $sql = 'update 
                    '.$this->tableSerialNumber.' 
                set 
                    warehousekey = 0 
                    '.$sqlUpdateSummary.' 
                where 
                    serialnumber = ' . $this->oDbCon->paramString($sn); 
        $this->oDbCon->execute($sql);
    }
    
    function reinstateItemSN($sn,$movementHistory){ 

        //$this->setLog($movementHistory,true);
        // kalo itemnya keluar, warehousekey pasti jadi 0 
        //$warehousekey = ($rsMovement[$sn]['qtyinbaseunit'] > 0) ? $rsMovement[$sn]['warehousekey'] : 0;
        $warehousekey = ($movementHistory['qtyinbaseunit'] > 0) ? $movementHistory['warehousekey'] : 0;
        
        // hanya digunakan untuk membatalkan item yg sudah keluar / terjual
        // jgn gunakan utk membatalkan transaksi retur ke vendor / kirim  ke vendor
        // karena da informasi warranty expired datenya
        
        $sql = 'update 
                    '.$this->tableSerialNumber.' 
                set
                    itemoutrefkey = 0 ,
                    itemoutrefheaderkey = 0,
                    itemoutreftabletype = 0, 
                    warehousekey = '.$this->oDbCon->paramString($warehousekey).' , 
                    warrantyperiodexpireddate = '.$this->oDbCon->paramString($movementHistory['warrantyperiodexpireddate']).' , 
                    warrantyperiod = '.$this->oDbCon->paramString($movementHistory['warrantyperiod']).' , 
                    warrantyperiodkey = '.$this->oDbCon->paramString($movementHistory['warrantyperiodkey']).' , 
                    warrantyvendorperiodexpireddate = '.$this->oDbCon->paramString($movementHistory['warrantyvendorperiodexpireddate']).' , 
                    warrantyvendorperiod = '.$this->oDbCon->paramString($movementHistory['warrantyvendorperiod']).' , 
                    warrantyvendorperiodkey = '.$this->oDbCon->paramString($movementHistory['warrantyvendorperiodkey']).' 
                where 
                    serialnumber = ' . $this->oDbCon->paramString($sn); 
        $this->oDbCon->execute($sql);
    }

    function searchItemSerialNumber($sn,$criteria=''){
         $sql  = 'SELECT 
                    *
                FROM 
                    '.$this->tableSerialNumber.'
                WHERE 
                    '.$this->tableSerialNumber.'.serialnumber = '.$this->oDbCon->paramString($sn);
        
        if (!empty($criteria))
            $sql .= $criteria;
         
        return $this->oDbCon->doQuery($sql); 
    }
    
 
    function searchSNMovement($itemkey='',$vendorpartnumberkey = '', $sn = '', $warehousekey = '', $criteria = '', $order = ''){ 
        
        $customer = new Customer();
        
        $itemIn = new ItemIn();
        $itemInTableKey = $this->getTableKeyAndObj($itemIn->tableName);
        $itemInTableKey = $itemInTableKey['key'];
        
        $itemInReceive = new ItemInReceive();
        $itemInReceiveTableKey = $this->getTableKeyAndObj($itemInReceive->tableName);
        $itemInReceiveTableKey = $itemInReceiveTableKey['key'];
        
        $itemOut = new ItemOut();
        $itemOutTableKey = $this->getTableKeyAndObj($itemOut->tableName);
        $itemOutTableKey = $itemOutTableKey['key'];
        
        $itemOutDelivery = new ItemOutDelivery();
        $itemOutDeliveryTableKey = $this->getTableKeyAndObj($itemOutDelivery->tableName);
        $itemOutDeliveryTableKey = $itemOutDeliveryTableKey['key'];

        $warrantyClaim = new WarrantyClaim();
        $warrantyClaimTableKey = $this->getTableKeyAndObj($warrantyClaim->tableName);
        $warrantyClaimTableKey = $warrantyClaimTableKey['key'];
        
        $warrantyClaimProgress = new WarrantyClaimProgress();
        $warrantyClaimProgressTableKey = $this->getTableKeyAndObj($warrantyClaimProgress->tableName);
        $warrantyClaimProgressTableKey = $warrantyClaimProgressTableKey['key'];
        
         $sql  = 'SELECT 
                    CONCAT_WS(\'\','.$itemIn->tableName.'.code,'.$itemOut->tableName.'.code,'.$itemInReceive->tableName.'.code,'.$itemOutDelivery->tableName.'.code,'.$warrantyClaim->tableName.'.code,'.$warrantyClaimProgress->tableName.'.code) as refcode, 
                    CONCAT_WS(\'\', '.$customer->tableName.'.name ,warranty_customer.name) as customername,
                    '.$this->tableSNMovement.'.itemkey,
                    '.$this->tableSNMovement.'.trdate,
                    '.$this->tableSNMovement.'.refkey,
                    '.$this->tableSNMovement.'.refheaderkey,
                    '.$this->tableSNMovement.'.warehousekey,
                    '.$this->tableSNMovement.'.reftabletype,
                    '.$this->tableSNMovement.'.serialnumber,
                    '.$this->tableSerialNumber.'.warrantyvendorperiod,
                    '.$this->tableSerialNumber.'.warrantyvendorperiodexpireddate, 
                    '.$this->tableSerialNumber.'.warrantyperiod,
                    '.$this->tableSerialNumber.'.warrantyperiodexpireddate, 
                    datediff(now(),'.$this->tableSerialNumber.'.warrantyvendorperiodexpireddate) as warrantyvendordatediff, 
                    datediff(now(),'.$this->tableSerialNumber.'.warrantyperiodexpireddate) as warrantydatediff, 
                    '.$this->tableSNMovement.'.qtyinbaseunit, 
                    '.$this->tableSNMovement.'.createdon, 
                    '.$this->tableItem.'.name as itemname,
                    '.$this->tableItem.'.code as itemcode,
                    '.$this->tableWarehouse.'.name as warehousename,
                    '.$this->tableItemVendorPartNumber.'.partnumber 
                 FROM 
                    '.$this->tableSNMovement.'
                    	left join '.$itemIn->tableName.' on '.$this->tableSNMovement.'.refheaderkey = '.$itemIn->tableName.'.pkey and '.$this->tableSNMovement.'.reftabletype = '.$itemInTableKey.' 
                    	left join '.$itemOut->tableName.' on '.$this->tableSNMovement.'.refheaderkey = '.$itemOut->tableName.'.pkey and '.$this->tableSNMovement.'.reftabletype = '.$itemOutTableKey.'
                            left join '.$customer->tableName.' on '.$itemOut->tableName.'.customerkey = '.$customer->tableName.'.pkey
                    	left join '.$itemInReceive->tableName.' on '.$this->tableSNMovement.'.refheaderkey = '.$itemInReceive->tableName.'.pkey and '.$this->tableSNMovement.'.reftabletype = '.$itemInReceiveTableKey.'
                    	left join '.$itemOutDelivery->tableName.' on '.$this->tableSNMovement.'.refheaderkey = '.$itemOutDelivery->tableName.'.pkey and '.$this->tableSNMovement.'.reftabletype = '.$itemOutDeliveryTableKey.' 
                   	    left join '.$warrantyClaim->tableName.' on '.$this->tableSNMovement.'.refheaderkey = '.$warrantyClaim->tableName.'.pkey and '.$this->tableSNMovement.'.reftabletype = '.$warrantyClaimTableKey.'
                   	        left join '.$customer->tableName.' warranty_customer on '.$warrantyClaim->tableName.'.customerkey = warranty_customer.pkey
                        left join '.$warrantyClaimProgress->tableName.' on '.$this->tableSNMovement.'.refheaderkey = '.$warrantyClaimProgress->tableName.'.pkey and '.$this->tableSNMovement.'.reftabletype = '.$warrantyClaimProgressTableKey.',
                    '.$this->tableItem.',
                    '.$this->tableWarehouse.',
                    '.$this->tableItemVendorPartNumber .',
                    '.$this->tableSerialNumber.' 
                 WHERE 
                    '.$this->tableSNMovement.'.itemkey = '.$this->tableItem.'.pkey and
                    '.$this->tableSNMovement.'.warehousekey = '.$this->tableWarehouse.'.pkey and
                    '.$this->tableSNMovement.'.serialnumber = '.$this->tableSerialNumber.'.serialnumber and
                    '.$this->tableSNMovement.'.vendorpartnumberkey = '.$this->tableItemVendorPartNumber.'.pkey ';
         
         
         if (!empty($itemkey))
             $sql .= ' and '.$this->tableSNMovement.'.itemkey = '.$this->oDbCon->paramString($itemkey);
           
         if (!empty($vendorpartnumberkey))
             $sql .= ' and '.$this->tableSNMovement.'.vendorpartnumberkey = '.$this->oDbCon->paramString($vendorpartnumberkey);
          
         if (!empty($sn))
             $sql .= ' and '.$this->tableSNMovement.'.serialnumber = '.$this->oDbCon->paramString($sn);
          
         if (!empty($warehousekey))
             $sql .= ' and '.$this->tableSNMovement.'.warehousekey = '.$this->oDbCon->paramString($warehousekey);
         
         if (!empty($criteria))
             $sql .= ' ' .$criteria;
        
        if (!empty($order))
             $sql .= ' ' .$order;
           
        //$this->setLog($sql,true);
        return $this->oDbCon->doQuery($sql); 
    }
    
    
     function searchItemRefCode($detailkey,$refTable){
        $itemIn = new ItemIn();
        $itemOut = new ItemOut();
        $tableName = '';
        $tableNameDetail = '';
        if($refTable==1){
            $tableName = $itemIn->tableName;
            $tableNameDetail = $itemIn->tableNameDetail;
        }else{
            $tableName = $itemOut->tableName;
            $tableNameDetail = $itemOut->tableNameDetail;
        }
         
         $sql  = 'SELECT 
                    '.$this->$tableName.'.pkey,
                    '.$this->$tableName.'.code
                FROM 
                    '.$this->$tableName.',
                    '.$this->$tableNameDetail.'
                WHERE 
                    '.$this->$tableName.'.pkey =  '.$this->$tableNameDetail.'.refkey and
                    '.$this->$tableNameDetail.'.pkey = '.$this->oDbCon->paramString($detailkey);
        
        return $this->oDbCon->doQuery($sql); 
    }
    	
 function updateItemMovementRental($refkey, $itemkey, $qtyinbaseunit, $costinbaseunit, $reftable, $warehousekey,$note,$trdate, $customerkey){
		
		$warehouse = new Warehouse();
		$item = new Item();
        
		$rsItem = $item->getDataRowById($itemkey);
        
		$createdby =  base64_decode($_SESSION[$this->loginAdminSession]['id']);
		 		
		$sql = '
			INSERT INTO		
			  '.$this->tableRentalMovement.' (
			  	refkey,
                trdate,
				itemkey,
				customerkey,
				warehousekey, 
				qtyinbaseunit,
				costinbaseunit,
				reftable, 
				note,
				statuskey,
				createdon,
				createdby
			)
			VALUES (
				'.$this->oDbCon->paramString($refkey).',
				'.$this->oDbCon->paramString($trdate).',
				'.$this->oDbCon->paramString($itemkey).', 
				'.$this->oDbCon->paramString($customerkey).', 
				'.$this->oDbCon->paramString($warehousekey).',
				'.$this->oDbCon->paramString($qtyinbaseunit).',
				'.$this->oDbCon->paramString($costinbaseunit).',
				'.$this->oDbCon->paramString($reftable).',
				'.$this->oDbCon->paramString($note).',
				1  ,
				now(),
				'.$this->oDbCon->paramString($createdby).'
			)';			 
         
		$this->oDbCon->execute($sql); 
	}
    
    function cancelMovementRental($refkey,$tableName){
		$sql = 'update '.$this->tableRentalMovement.' set statuskey = 2 where refkey = ' . $this->oDbCon->paramString($refkey) .' and reftable  = ' . $this->oDbCon->paramString($tableName);
		$this->oDbCon->execute($sql);
	}
    
    function getItemRental($arrItemKey='', $customerkey = '', $warehousekey = '',$criteria = '',$orderBy=''){
        
		if (!empty($warehousekey)){
            
            if(!is_array($warehousekey))
                $warehousekey = explode(',',$warehousekey);
             
            $warehousekey = implode(',',$this->oDbCon->paramString($warehousekey));
			$criteria .= ' and warehousekey in ('.$warehousekey.')';
		}
       
        if (!empty($customerkey)){
            
            if(!is_array($customerkey))
                $customerkey = explode(',',$customerkey);
             
            $customerkey = implode(',',$customerkey);
			$criteria .= ' and customerkey in ('.$customerkey.')';
		}
        
        if (!empty($arrItemKey)){
            
            if(!is_array($arrItemKey))
                $arrItemKey = explode(',',$arrItemKey);
             
            $itemkey = implode(',',$this->oDbCon->paramString($arrItemKey));
			$criteria .= ' and itemkey in ('.$itemkey.')';
		}
        
        
		$sql = 'select 
                    '.$this->tableRentalMovement.'.*,
                    '.$this->tableWarehouse.'.name as warehousename,
                    '.$this->tableItem.'.code as itemcode,
                    '.$this->tableItem.'.name as itemname,
                    '.$this->tableCustomer.'.name as customername,
                    coalesce(sum(qtyinbaseunit),0) as "qtyinbaseunit" 
                from 
                    '.$this->tableRentalMovement.', 
                    '.$this->tableCustomer.', 
                    '.$this->tableWarehouse.', 
                    '.$this->tableItem.' 
                where
                    '.$this->tableRentalMovement.'.itemkey = '.$this->tableItem.'.pkey and
                    '.$this->tableRentalMovement.'.warehousekey = '.$this->tableWarehouse.'.pkey and
                    '.$this->tableRentalMovement.'.customerkey = '.$this->tableCustomer.'.pkey and
                    '.$this->tableRentalMovement.'.statuskey = 1 
                    ' . $criteria .'
                group by itemkey,customerkey  having coalesce(sum(qtyinbaseunit),0) < 0 ';
        
        if(!empty($orderBy))
        $sql .= ' '.$orderBy;
        
//        $this->setLog($sql,true);         
	 	return  $this->oDbCon->doQuery($sql);
	}
    
    function sumItemRentalMovement($itemkey, $warehousekey = '',$endDate=''){
		       
		$criteria = '';
		if (!empty($warehousekey)){
            
            if(!is_array($warehousekey))
                $warehousekey = explode(',',$warehousekey);
            
            $warehousekey = implode(',',$this->oDbCon->paramString($warehousekey));
            
			$criteria .= ' and warehousekey in ('.$warehousekey.')';
            //$criteria .= $this->getCompanyCriteria($this->tableWarehouse);
            
		}
		if (!empty($endDate)){
            $dateMethod = $this->loadSetting('movementDateMethod');  
            $datefield = ($dateMethod == 2)  ? 'trdate' : 'createdon';  
            $criteria .= ' and '.$datefield.' < '.$this->oDbCon->paramDate($endDate,' / ', 'Y-m-d 23:59:59'); 
		}
       
       
		$sql = 'select coalesce(sum(qtyinbaseunit),0) as "qtyinbaseunit" from '.$this->tableRentalMovement.'  where statuskey = 1 and itemkey in ('. $itemkey . ') '. $criteria;	
       
       // $this->setLog($sql);
       
        $rs =  $this->oDbCon->doQuery($sql);		 
	 	return $rs[0]['qtyinbaseunit'];
	}

    function updateItemMovement($refkey, $itemkey, $qtyinbaseunit, $costinbaseunit, $reftable, $warehousekey,$note,$trdate, $vendorpartnumberkey = 0){
		
		$warehouse = new Warehouse();
		$item = new Item();
        
		$rsItem = $item->getDataRowById($itemkey);
        
		$createdby =  base64_decode($_SESSION[$this->loginAdminSession]['id']);
		
        $negativeQOH = $this->loadSetting('negativeQOH');
        if($negativeQOH != 1){
            
            $totalqty = 0; 
            $saldoakhir = $this->getItemQOH($itemkey, $warehousekey);  
            $totalqty = $saldoakhir + $qtyinbaseunit; 

            // stok gk boleh minus, hanya jika dr transaksi barang keluar
            if($qtyinbaseunit < 0 && $totalqty < 0)
                throw new Exception('<strong>'.$rsItem[0]['name']. '</strong>. ' .$this->errorMsg[402]);

            $rsWarehouse = $warehouse->getDataRowById($warehousekey);

            //klo barang di keluarin di cabang/warehouse tidak cukup
            // ini gk tau buat ap .. lupa...
            if($totalqty < 0)
                throw new Exception('<strong>'.$rsItem[0]['name']. '</strong>. ' .$this->errorMsg[402]); 

        }
        
		$sql = '
			INSERT INTO		
			  '.$this->tableName.' (
			  	refkey,
                trdate,
				itemkey,
				vendorpartnumberkey,
				warehousekey, 
				qtyinbaseunit,
				costinbaseunit,
				reftable, 
				note,
				statuskey,
				createdon,
				createdby
			)
			VALUES (
				'.$this->oDbCon->paramString($refkey).',
				'.$this->oDbCon->paramString($trdate).',
				'.$this->oDbCon->paramString($itemkey).', 
				'.$this->oDbCon->paramString($vendorpartnumberkey).', 
				'.$this->oDbCon->paramString($warehousekey).',
				'.$this->oDbCon->paramString($qtyinbaseunit).',
				'.$this->oDbCon->paramString($costinbaseunit).',
				'.$this->oDbCon->paramString($reftable).',
				'.$this->oDbCon->paramString($note).',
				1  ,
				now(),
				'.$this->oDbCon->paramString($createdby).'
			)';			 
         
		$this->oDbCon->execute($sql); 
		$arrReturn = $this->updateItemInWarehouse($itemkey,$warehousekey);
		 
        // lock vendor part number 
//        $sql = 'update '.$this->tableItemVendorPartNumber.' set islock = 1 where pkey = ' . $this->oDbCon->paramString($vendorpartnumberkey); 
//        $this->oDbCon->execute($sql);  
         
		return $arrReturn;
	}

	function updateItemInWarehouse($itemkey,$warehousekey){
		
		
		$item = new Item();
		$warehouse = new Warehouse();
        //$marketplace = new Marketplace();
        
		$rsItem = $item->getDataRowById($itemkey);
		$rsWarehouse = $warehouse->getDataRowById($warehousekey);
		 
        $saldoakhir = $this->sumItemMovement($itemkey, $warehousekey); 
        
        //klo barang di keluarin di cabang/warehouse tidak cukup
        
        $negativeQOH = $this->loadSetting('negativeQOH');
        if($negativeQOH != 1){ 
            if($saldoakhir < 0)
                throw new Exception('<strong>'.$rsItem[0]['name']. '</strong>. ' .$this->errorMsg[402]);
            
            // others script
        }
		
        $sql = 'select itemkey from '.$this->tableItemInWarehouse.' where itemkey='.$this->oDbCon->paramString($itemkey).' and  warehousekey = '.$this->oDbCon->paramString($warehousekey).' limit 0,1';
        $result = $this->oDbCon->doQuery($sql);

        if(empty($result)){ 
            $sql = '
                INSERT INTO	'.$this->tableItemInWarehouse.' (
                    itemkey,
                    warehousekey,
                    qtyinbaseunit 
                    )
                VALUES (
                    '.$this->oDbCon->paramString($itemkey).',
                    '.$this->oDbCon->paramString($warehousekey).',
                    '.$this->oDbCon->paramString($saldoakhir).' 
                    )';			
            
            $this->oDbCon->execute($sql);

        }else{
            $sql = '
                UPDATE '.$this->tableItemInWarehouse.'
                    SET	 
                        qtyinbaseunit = '.$saldoakhir.' 
                    WHERE	
                        itemkey='.$this->oDbCon->paramString($itemkey).' and 
                        warehousekey = '.$this->oDbCon->paramString($warehousekey).'
                ';			
            $this->oDbCon->execute($sql);
        }
		 
	 					 
		$sql = 'update 
					item 
				set 
					cogs = '.$item->getCOGS($itemkey).'
				where 
					pkey = '.$this->oDbCon->paramString($itemkey);
		 
		$this->oDbCon->execute($sql);
					
        // dummy qtyonhand utk item parent..
	 	if ($rsItem[0]['isvariant'] == 1) {
			$rsVariant = $item->getVariant($rsItem[0]['parentkey']); 
            
			$arrTemp = array();
			for($i=0;$i<count($rsVariant);$i++){
				array_push($arrTemp,$rsVariant[$i]['pkey']);
			}
			$variant = implode(",",$arrTemp);
            $saldoakhir = $this->sumItemMovement($variant, $warehousekey);   
			 
            
            $sql = 'select itemkey from '.$this->tableItemInWarehouse.' where itemkey='.$this->oDbCon->paramString($rsItem[0]['parentkey']).' and  warehousekey = '.$this->oDbCon->paramString($warehousekey).' limit 0,1';
            $result = $this->oDbCon->doQuery($sql);

            if(empty($result)){ 
                $sql = '
                    INSERT INTO	'.$this->tableItemInWarehouse.' (
                        itemkey,
                        warehousekey,
                        qtyinbaseunit 
                        )
                    VALUES (
                        '.$rsItem[0]['parentkey'].',
                        '.$warehousekey.',
                        '.$saldoakhir.' 
                        )';			

                $this->oDbCon->execute($sql);

            }else{
                $sql = '
                    UPDATE '.$this->tableItemInWarehouse.'
                        SET	 
                            qtyinbaseunit = '.$saldoakhir.' 
                        WHERE	
                            itemkey='.$this->oDbCon->paramString($rsItem[0]['parentkey']).' and 
                            warehousekey = '.$this->oDbCon->paramString($warehousekey).'
                    ';			
                $this->oDbCon->execute($sql);
            }
		}
        
        //$marketplace->updateProductsQOHInAllMarketplace($itemkey);
		  
        return  array('itemkey' => $itemkey, 'warehousekey' => $warehousekey, 'qtyinbaseunit' => $saldoakhir);
	}
    
    function updateQOR($itemkey,$warehousekey){
		 
		$item = new Item();
		$warehouse = new Warehouse(); 
        
		$rsItem = $item->getDataRowById($itemkey);
		$rsWarehouse = $warehouse->getDataRowById($warehousekey);
	 
        // select detail sales order
        // kedepan ad kemungkinan bisa nambah dr table lain
        $sql = 'select 
                    coalesce(sum(qtyinbaseunit-deliveredqtyinbaseunit),0) as qor 
                from 
                    '.$this->tableSalesOrderDetail.',
                    '.$this->tableSalesOrderHeader.' 
                where 
                    '.$this->tableSalesOrderDetail.'.refkey = '.$this->tableSalesOrderHeader.'.pkey and 
                    '.$this->tableSalesOrderDetail.'.itemkey = '.$this->oDbCon->paramString($itemkey).' and 
                    '.$this->tableSalesOrderHeader.'.warehousekey = '.$this->oDbCon->paramString($warehousekey).' and
                    '.$this->tableSalesOrderHeader.'.statuskey in (1,2,3)';
        
        //$this->setLog($sql,true);
        $result = $this->oDbCon->doQuery($sql);
        $qor = $result[0]['qor'];
        
        $sql = 'select itemkey, qtyonreserveinbaseunit from '.$this->tableItemInWarehouse.' where itemkey='.$this->oDbCon->paramString($itemkey).' and  warehousekey = '.$this->oDbCon->paramString($warehousekey).' limit 0,1';
        $result = $this->oDbCon->doQuery($sql);

        if(empty($result)){  
            $sql = '
                INSERT INTO	'.$this->tableItemInWarehouse.' (
                    itemkey,
                    warehousekey,
                    qtyonreserveinbaseunit 
                )VALUES (
                    '.$this->oDbCon->paramString($itemkey).',
                    '.$this->oDbCon->paramString($warehousekey).',
                    '.$this->oDbCon->paramString($qor).' 
                )';			
            
            $this->oDbCon->execute($sql);

        }else{
           
            $sql = '
                UPDATE '.$this->tableItemInWarehouse.'
                    SET	 
                        qtyonreserveinbaseunit = '.$qor.'  
                    WHERE	
                        itemkey='.$this->oDbCon->paramString($itemkey).' and 
                        warehousekey = '.$this->oDbCon->paramString($warehousekey).'
                ';			
            $this->oDbCon->execute($sql);
        } 
        
        /*$sql = 'update '.$this->tableItem.' set qtyonreserve = (
                    select sum(qtyonreserveinbaseunit) as qor from '.$this->tableItemInWarehouse.' where itemkey = '.$this->oDbCon->paramString($itemkey).'
                ) where pkey = '.$this->oDbCon->paramString($itemkey);
        $this->oDbCon->execute($sql);
		 */
	}	
    
    function updateQORRental($itemkey,$warehousekey,$qor=0){
		 
		$item = new Item();
		$warehouse = new Warehouse(); 
        
		$rsItem = $item->getDataRowById($itemkey);
		$rsWarehouse = $warehouse->getDataRowById($warehousekey);
	 
        $sql = 'select itemkey, qtyonreserveinbaseunit from '.$this->tableItemInWarehouse.' where itemkey='.$this->oDbCon->paramString($itemkey).' and  warehousekey = '.$this->oDbCon->paramString($warehousekey).' limit 0,1';
        $result = $this->oDbCon->doQuery($sql);

        if(empty($result)){  
            $sql = '
                INSERT INTO	'.$this->tableItemInWarehouse.' (
                    itemkey,
                    warehousekey,
                    qtyonreserveinbaseunit 
                )VALUES (
                    '.$this->oDbCon->paramString($itemkey).',
                    '.$this->oDbCon->paramString($warehousekey).',
                    '.$this->oDbCon->paramString($qor).' 
                )';			
            
            $this->oDbCon->execute($sql);

        }else{
           
            $sql = '
                UPDATE '.$this->tableItemInWarehouse.'
                    SET	 
                        qtyonreserveinbaseunit = qtyonreserveinbaseunit + '.$qor.'  
                    WHERE	
                        itemkey='.$this->oDbCon->paramString($itemkey).' and 
                        warehousekey = '.$this->oDbCon->paramString($warehousekey).'
                ';			
            $this->oDbCon->execute($sql);
        } 
		 
	}	
    
	function cancelMovement($refkey,$tableName){
		$sql = 'update '.$this->tableName.' set statuskey = 2 where refkey = ' . $this->oDbCon->paramString($refkey) .' and reftable  = ' . $this->oDbCon->paramString($tableName);
		$this->oDbCon->execute($sql);
		
		$sql = 'select * from '.$this->tableName.'  where refkey = ' . $this->oDbCon->paramString($refkey) .' and reftable  = ' . $this->oDbCon->paramString($tableName);
		$rs = $this->oDbCon->doQuery($sql);
		
        $arrItemMovement = array();
		for($i=0;$i<count($rs);$i++){ 
			$arrResult = $this->updateItemInWarehouse($rs[$i]['itemkey'],$rs[$i]['warehousekey']);
            array_push($arrItemMovement, $arrResult);
        }
        
        return $arrItemMovement;
	}
	  
    
	
	function cancelSNMovement($refkey,$tableName){
        return;
		
        $arrType = $this->getTableKeyAndObj($tableName); //$this->getMovementType($tableName);
        
		$sql = 'update '.$this->tableSNMovement.' set statuskey = 2 where refheaderkey = ' . $this->oDbCon->paramString($refkey) .' and reftabletype  = ' . $this->oDbCon->paramString($arrType['key']);
        $this->oDbCon->execute($sql);  
         
        $sql = 'select * from  '.$this->tableSNMovement.' where refheaderkey = ' . $this->oDbCon->paramString($refkey) .' and reftabletype  = ' . $this->oDbCon->paramString($arrType['key']);
        $rs = $this->oDbCon->doQuery($sql);   
       
  
        // get latest movement details  from item_sn_movement
        $arrSN =  array_column($rs,'serialnumber'); 
        $sql = 'select * from ' .$this->tableSNMovement. ' where serialnumber in (' . $this->oDbCon->paramString($arrSN,',') .') and statuskey = 1 order by trdate desc, pkey desc';
         
        $rsTemp = $this->oDbCon->doQuery($sql); 
        
        $rsMovement = array();
        foreach($rsTemp as $row){   
            $sn = $row['serialnumber'];  
            if(!isset($rsMovement[$sn]))
                $rsMovement[$sn] = $row;
        }
            
        // update item SN
        // kalo transfer gudang perlu cek ulang
       /* $itemIn = new ItemIn();
        $itemInTableKey = $this->getTableKeyAndObj($itemIn->tableName);
        $itemInTableKey = $itemInTableKey['key'];
        
        $itemInReceive = new ItemInReceive();
        $itemInReceiveTableKey = $this->getTableKeyAndObj($itemInReceive->tableName);
        $itemInReceiveTableKey = $itemInReceiveTableKey['key'];
        */  
        
        // kalo dr garansi, warehousekey harus dibalik jd 0 ....
        // reinstateItemSN agak problem kalo kasus garansi
           
        foreach($rs as $row){
            $sn = $row['serialnumber'];
               
            if (!isset($rsMovement[$sn]) || empty($rsMovement[$sn])){  
                $this->deleteItemSN($sn);
            }else {  
                $this->reinstateItemSN($sn,$rsMovement[$sn]);
            }
             
        }
              
 
        
        
/*        
        if ($arrType['key'] == $itemInTableKey || $arrType['key'] == $itemInReceiveTableKey  ){
            //item in
            for($i=0;$i<count($rs);$i++) 
                $this->deleteItemSN($rs[$i]['serialnumber']);
         
        }else{
            //item out 
            for($i=0;$i<count($rs);$i++) 
                $this->reinstateItemSN($rs[$i]['serialnumber'],$warehousekey);
           
        }*/
	}
	  
    
    // ** FOR MAINTENANCE ONLY ** //
    function recalculateCOGS($movementkey, $itemkey){
        
         $sql = 'select 
					coalesce(sum(costinbaseunit*qtyinbaseunit) / sum(qtyinbaseunit),0)  as cogs
				from 
					'.$this->tableName.','.$this->tableWarehouse.'
			 	where 
					'.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey and 
					'.$this->tableName.'.statuskey = 1 and 
					'.$this->tableWarehouse.'.isqohcount = 1 and 
					'.$this->tableName.'.pkey  < '.$movementkey.' and  
					'.$this->tableName.'.itemkey = '.$this->oDbCon->paramString($itemkey); 
         
    
        $rs = $this->oDbCon->doQuery($sql);	
        if (empty($rs[0]['cogs']))
            $rs[0]['cogs'] = 0;
         
		return $rs[0]['cogs'] ;
		
    
    }
    
	function updateCOGS(){
	 	try{ 
	
			if(!$this->oDbCon->startTrans()) 
				throw new Exception($this->errorMsg[100]);	

			$item = new Item();
			$salesOrder = new SalesOrder();

		 	//$rsItem = $item->searchData('item.pkey','105',true);
            $rsItem = $item->searchData();

            
			for ($i=0; $i<count($rsItem); $i++) { 

				$rsItemMovement = $this->searchData($this->tableName.'.itemkey', $rsItem[$i]['pkey'],true,'','order by pkey asc');

				for ($j=0; $j<count($rsItemMovement); $j++) { 
                    
                    if ($rsItemMovement[$j]['reftable'] == "sales_order_header" || $rsItemMovement[$j]['reftable'] == "item_out_header" || $rsItemMovement[$j]['reftable'] == "warehouse_transfer_header" ) {
                        
                        if ($rsItemMovement[$j]['reftable'] == "warehouse_transfer_header" && $rsItemMovement[$j]['qtyinbaseunit'] >  0){
                              $rsPrevCOGS =  $this->searchData($this->tableName.'.itemkey', $rsItem[$i]['pkey'],true,' and reftable = \'warehouse_transfer_header\' and '.$this->tableName.'.pkey < ' . $rsItemMovement[$j]['pkey'],'order by pkey desc limit 1');
					          $cogs = $rsPrevCOGS[0]['costinbaseunit'];
                        }else{
                             $cogs = $this->recalculateCOGS($rsItemMovement[$j]['pkey'],$rsItemMovement[$j]['itemkey'] ); 
                        }
                        
                        $sql = 'update '.$this->tableName.' set costinbaseunit = '.$cogs.' where pkey = ' . $this->oDbCon->paramString($rsItemMovement[$j]['pkey']);
                       // echo $sql.'<br>';
                        $this->oDbCon->execute($sql); 
                        
                        if ($rsItemMovement[$j]['reftable'] == "sales_order_header"){
                                $rsSalesOrderHeader = $salesOrder->getDataRowById($rsItemMovement[$j]['refkey']);  
                                $rsSalesOrderDetailAll = $salesOrder->getDetailById($rsItemMovement[$j]['refkey']);
                                $rsSalesOrderDetail = $salesOrder->getDetailById($rsItemMovement[$j]['refkey'], ' and itemkey = ' . $rsItemMovement[$j]['itemkey']); 
                                $taxPercentage = $rsSalesOrderHeader[0]['taxpercentage'];
                                    
                                for ($k=0; $k<count($rsSalesOrderDetail); $k++) { 

                                    $qty =  $rsSalesOrderDetail[$k]['qty'];
                                    $priceInUnit = $rsSalesOrderDetail[$k]['priceinunit'];
                                    $discount = $rsSalesOrderDetail[$k]['discount'];
                                    $discountType =  $rsSalesOrderDetail[$k]['discounttype']; 
                                        
                                    if ($discount != 0 && $discountType == 2){
                                        $discount = $discount/100 * $priceInUnit;
                                    } 
 
                                    $priceInUnitBeforeTax = $priceInUnit - $discount;

                                    if ($isPriceIncludeTax == true) { 
                                            $taxValue = ($taxPercentage/(100 + $taxPercentage)) * $priceInUnitBeforeTax;   
                                            $priceInUnitBeforeTax = $priceInUnitBeforeTax - $taxValue ;
                                    }  
 
                                    $profit = $priceInUnitBeforeTax - $cogs;

                                    $sql = 'update '.$salesOrder->tableNameDetail.' set costinbaseunit = '.$this->oDbCon->paramString($this->unFormatNumber($cogs)).', profit = '.$this->oDbCon->paramString($this->unFormatNumber($profit)).' where pkey = ' . $this->oDbCon->paramString($rsSalesOrderDetail[$k]['pkey']);
                                    $this->oDbCon->execute($sql);

                                }
                            
                                $subtotalProfit = 0;
                                 for ($k=0; $k<count($rsSalesOrderDetailAll); $k++) {  
                                    $subtotalProfit += $rsSalesOrderDetailAll[$k]['qty'] * $rsSalesOrderDetailAll[$k]['profit']; 
                                 }
                            
                                //update header
                                $finalDiscount  = $rsSalesOrderHeader[0]['finaldiscount'];
                                if ($finalDiscount != 0 && $rsSalesOrderHeader[0]['finaldiscounttype'] == 2){
                                        $finalDiscount = $finalDiscount/100 * $rsSalesOrderHeader[0]['subtotal'];
                                }

                                $profit = $subtotalProfit -  $finalDiscount - $rsSalesOrderHeader[0]['pointvalue'] ; 
                                $sql = 'update '.$salesOrder->tableName.' set profit = '.$this->oDbCon->paramString($this->unFormatNumber($profit)).' where pkey = ' . $this->oDbCon->paramString($rsSalesOrderHeader[0]['pkey']);
                              
                            
                                $this->oDbCon->execute($sql);
                        }
                        
                    }
 
                    }
                 
                    
                	$sql = 'update 
                                item 
                            set 
                                cogs = '.$item->getCOGS($rsItem[$i]['pkey']).'
                            where 
                                pkey = '.$this->oDbCon->paramString($rsItem[$i]['pkey']);

                    $this->oDbCon->execute($sql);

			}

			$this->oDbCon->endTrans();			
					 
		} catch(Exception $e){
			$this->oDbCon->rollback();  
		}		
	    
	 }
    
    function getItemAging($itemkey, $warehousekey = array(),$endDate = '', $basedOn = 1){
        
        //$basedOn : 1 => purchase, item in, and so on
        //           2 => warehouse transfer
        
        $dateMethod = $this->loadSetting('movementDateMethod');  
        $datefield = ($dateMethod == 2)  ? 'trdate' : 'createdon'; 

        
        $arrReturn =  array(
                                'trdate' => DEFAULT_EMPTY_DATE,
                                'movementdate' => DEFAULT_EMPTY_DATE,
                                'maxaging' => 0,
                        );
        
        
        // cari sampe qty terakhir////
        $qty = $this->sumItemMovement($itemkey,$warehousekey,$endDate);  
        
        // kalo kosong return 0 aj agingnya
        if($qty <= 0)  return $arrReturn;
         
        //$this->setLog($qty,true);
        
        // ambil semua barang masuk, dengan criteria yg sama, sort desc berdasarkan tgl transaksi / tgl dibuat
        $criteria = '';
        
        if (!empty($warehousekey)){   
            $criteria .= ' and warehousekey in ('.$this->oDbCon->paramString($warehousekey,',').')'; 
		}
        if (!empty($endDate)){ 
            $criteria .= ' and '.$datefield.' <= '.$this->oDbCon->paramDate($endDate,' / ', 'Y-m-d 23:59:59'); 
		}
        
       /* $arrExclude = array('warehouse_transfer_header');
        if($basedOn == 1 && !empty($arrExclude))
            $criteria .= ' and '.$this->tableName.'.reftable not in ('.$this->oDbCon->paramString($arrExclude,',').')'; 
       */ 
        $sql = 'select 
                    pkey,
                    '.$this->tableName.'.'.$datefield.',
                    coalesce(datediff(now(),'.$this->tableName.'.'.$datefield.'),0) as maxaging,
                    '.$this->tableName.'.qtyinbaseunit 
                from 
                    '.$this->tableName.' 
                where 
                    '.$this->tableName.'.qtyinbaseunit > 0 and
                    '.$this->tableName.'.statuskey = 1 and
                    '.$this->tableName.'.itemkey = '.$this->oDbCon->paramString($itemkey).'
                    '.$criteria.'     
                order by '.$this->tableName.'.'.$datefield.' desc ';
        
        //$this->setLog($sql,true);
        $rsMovement =  $this->oDbCon->doQuery($sql);
        
        foreach($rsMovement as $row){
            $qty -= $row['qtyinbaseunit'];
            
            if ($qty <= 0 ){ 
                $arrReturn = array(
                                'trdate' => $row['trdate'],
                                'movementdate' => $row[$datefield],
                                'maxaging' => $row['maxaging'],
                        );
                break;
            }
            
        }
        
        return $arrReturn;
    }
    
    function generateItemSNAgingReport($fieldname='',$searchkey='',$mustmatch=false,$searchCriteria='',$orderCriteria='', $limit=''){
         
        $dateMethod = $this->loadSetting('movementDateMethod');  
        $datefield = ($dateMethod == 2)  ? 'trdate' : 'createdon'; 
        
       $sql = 'select 
                   '.$this->tableSNMovement.'.*,
                   '.$this->tableItem.'.code as itemcode,
                   '.$this->tableItem.'.name as itemname,
                    coalesce(datediff(date(now()),'.$this->tableSNMovement.'.'.$datefield.'),0) as aging ,
                    concat_ws(\'\','.$this->tableCustomer.'.name, '.$this->tableSupplier.'.name) as refname,
                    concat_ws(\'\','.$this->tableWarranty.'.code, '.$this->tableWarrantyVendor.'.refcode) as refcode,
                    concat_ws(\'\','.$this->tableWarranty.'.trdate, '.$this->tableWarrantyVendor.'.trdate) as refdate
                from 
                    '.$this->tableSNMovement.'
                        left join '.$this->tableWarranty.' on '.$this->tableSNMovement.'.refheaderkey = '.$this->tableWarranty.'.pkey and '.$this->tableSNMovement.'.reftabletype = 269
                        left join '.$this->tableCustomer.' on '.$this->tableWarranty.'.customerkey = '.$this->tableCustomer.'.pkey           
                        left join '.$this->tableWarrantyVendor.' on '.$this->tableSNMovement.'.refheaderkey = '.$this->tableWarrantyVendor.'.pkey and '.$this->tableSNMovement.'.reftabletype = 317
                        left join '.$this->tableSupplier.' on '.$this->tableWarrantyVendor.'.supplierkey = '.$this->tableSupplier.'.pkey,
                     
                    '.$this->tableItem.' 
                where  
                    '.$this->tableSNMovement.'.statuskey = 1 and
                    '.$this->tableSNMovement.'.itemkey = '.$this->tableItem.'.pkey'; 
        
        $sql .= ' ' .$searchCriteria;
	   
        $sql .= '  group by '.$this->tableSNMovement.'.serialnumber
                   having sum('.$this->tableSNMovement.'.qtyinbaseunit) > 0  ';
        
	    $sql .= ' ' .$orderCriteria;  
			 
        //$this->setLog($sql,true);
        
        $rs =  $this->oDbCon->doQuery($sql);
         
        return $rs;
        
    }
    
 /*   function getItemSNAging($serialnumber, $endDate = '', $warehousekey, $basedOn = 1){
 
        
        // 269 => warranty
        // 317 => vendor_warranty_claim_header
        
        $dateMethod = $this->loadSetting('movementDateMethod');  
        $datefield = ($dateMethod == 2)  ? 'trdate' : 'createdon'; 
 
        $arrReturn =  array(
                                'trdate' => DEFAULT_EMPTY_DATE,
                                'movementdate' => DEFAULT_EMPTY_DATE,
                                'refname' => '', 
                                'maxaging' => 0,
                        );
          
        
        
        //cek qty per tgl itu 0 atau bukan
        $sql = 'select 
                    sum('.$this->tableSNMovement.'.qtyinbaseunit) as qtyonhand
                from 
                    '.$this->tableSNMovement.' 
                where 
                    '.$this->tableSNMovement.'.serialnumber = '.$this->oDbCon->paramString($serialnumber).' and 
                    '.$this->tableSNMovement.'.warehousekey = '.$this->oDbCon->paramString($warehousekey).' and 
                    '.$this->tableSNMovement.'.statuskey = 1';
         if (!empty($endDate)) 
            $sql .= ' and '.$this->tableSNMovement.'.'.$datefield.' <= '.$this->oDbCon->paramDate($endDate,' / ', 'Y-m-d 23:59:59'); 
        
        $rsQty =  $this->oDbCon->doQuery($sql);
        if(empty($rsQty) || $rsQty[0]['qtyonhand'] <=0 ) 
            return $arrReturn;
        
        
        // KALO gk kosong
        $sql = 'select 
                    '.$this->tableSNMovement.'.pkey,
                    '.$this->tableSNMovement.'.'.$datefield.',
                    coalesce(datediff('.$this->oDbCon->paramDate($endDate,' / ').','.$this->tableSNMovement.'.'.$datefield.'),0) as aging,
                    concat_ws(\'\','.$this->tableCustomer.'.name, '.$this->tableSupplier.'.name) as refname
                from 
                    '.$this->tableSNMovement.'
                        left join '.$this->tableWarranty.' on '.$this->tableSNMovement.'.refheaderkey = '.$this->tableWarranty.'.pkey and '.$this->tableSNMovement.'.reftabletype = 269
                        left join '.$this->tableCustomer.' on '.$this->tableWarranty.'.customerkey = '.$this->tableCustomer.'.pkey           
                        left join '.$this->tableWarrantyVendor.' on '.$this->tableSNMovement.'.refheaderkey = '.$this->tableWarrantyVendor.'.pkey and '.$this->tableSNMovement.'.reftabletype = 317
                        left join '.$this->tableSupplier.' on '.$this->tableWarrantyVendor.'.supplierkey = '.$this->tableSupplier.'.pkey,
                    '.$this->tableItem .' 
                where 
                    '.$this->tableSNMovement.'.itemkey = '.$this->tableItem.'.pkey and
                    '.$this->tableSNMovement.'.serialnumber = '.$this->oDbCon->paramString($serialnumber).' and
                    '.$this->tableSNMovement.'.qtyinbaseunit > 0 and
                    '.$this->tableSNMovement.'.warehousekey = '.$this->oDbCon->paramString($warehousekey).' and 
                    '.$this->tableSNMovement.'.statuskey = 1';
        
        if (!empty($endDate)) 
            $sql .= ' and '.$this->tableSNMovement.'.'.$datefield.' <= '.$this->oDbCon->paramDate($endDate,' / ', 'Y-m-d 23:59:59'); 
	  
        $sql .= ' order by '.$this->tableSNMovement.'.'.$datefield.' desc limit 1 ';
        //$this->setLog($sql,true);
        
        $rsMovement =  $this->oDbCon->doQuery($sql);
        
        
        if(!empty($rsMovement)){ 
            $row = $rsMovement[0];
            $arrReturn = array(
                                    'trdate' => $row['trdate'],
                                    'movementdate' => $row[$datefield],
                                    'refname' => $row['refname'],
                                    'maxaging' => $row['aging'],
                            );
        }
         
        return $arrReturn;
    }*/
    	
    // ** FOR MAINTENANCE ONLY ** //
    
    
    /*function getMovementType($tableName){ 
        
        $itemIn = new ItemIn();
        $itemOut = new ItemOut(); 
        
        $arr = array();
        
        
        
        switch ($tableName){ 
            case $itemIn->tableName : $arr = array('key' => 1,  
                                           'obj' => $itemIn 
                                          );
                              break; 
            case $itemOut->tableName : $arr = array('key' => 2,  
                                           'obj' => $itemOut 
                                          );
                              break; 

         
        }
        
        return $arr;
        
    }*/
     
    
}  

?>