<?php

class GalleryCategory extends Category{
 
   function __construct($catType = 1){
		
		parent::__construct();
       
		$this->tableName = 'gallery_category';   
        $this->securityObject = ($catType == 1) ? 'GalleryCategory' : 'GalleryHumanResourceCategory';  
		$this->uploadFolder = 'gallery-category/'; 
        $this->categoryType = $catType; 
              
        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['code'] = array('code'); 
        $this->arrData['name'] = array('name');
        $this->arrData['orderlist'] = array('orderList', 'number');
        $this->arrData['parentkey'] = array('selCategory');
        $this->arrData['isleaf'] = array('isLeaf'); 
        $this->arrData['file'] = array('fileName');
        $this->arrData['typekey'] = array('typekey');
        $this->arrData['statuskey'] = array('selStatus');
        $this->arrData['shortdescription'] = array('trShortDesc'); 
        $this->arrData['description'] = array('txtDescription','raw'); 

	          
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'category','title' => 'category','dbfield' => 'name','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'parent','title' => 'parent','dbfield' => 'parentname','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'description','title' => 'note','dbfield' => 'shortdescription',  'width' => 250));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
       
        $this->includeClassDependencies(array( 
              'Gallery.class.php',  
        ));

        $this->overwriteConfig();
   }
    
    function getQuery(){
	   
	   $sql= '
			select
					'.$this->tableName. '.*,
					'.$this->tableStatus.'.status as statusname,
					parentcat.name as parentname
				from 
					'.$this->tableName . ' left join '.$this->tableName . ' parentcat on 	parentcat.pkey = '.$this->tableName . '.parentkey ,'.$this->tableStatus.' 
				where  		
					'.$this->tableName . '.typekey = '.$this->categoryType.' and
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey 
 		' .$this->criteria ; 
		 
       return $sql; 
    }
 
    function validateDelete($id){
		    
		$arrayToJs = array();
		$rs = $this->getDataRowById($id);
		
		if ($rs[0]['systemVariable'] == 1)  {
			$this->addErrorList($arrayToJs,false,'<strong>'.$rs[0]['name'].'</strong>. ' . $this->errorMsg[211]);
			return $arrayToJs;
		}
        
        
        $gallerytype = ($this->categoryType<>1) ? 2 : 1;
        
		$gallery = new Gallery($gallerytype);
		$rsItem = $gallery->searchData($gallery->tableName.'.categorykey', $id,true);   
        if(!empty($rsItem)){
			$rsCategory = $this->getDataRowById($id);
			$this->addErrorList($arrayToJs,false,'<strong>'.$rsCategory[0]['name']. '</strong>. '. $this->errorMsg[900] .' <strong>(' . $rsItem[0]['code'] . ' - ' . $rsItem[0]['name'] . ')</strong>.'); 
		}
	 
		return $arrayToJs;
	  
	 }
    
      function normalizeParameter($arrParam, $trim = false){  
        $arrParam['typekey']  = $this->categoryType; 
          
        $arrParam = parent::normalizeParameter($arrParam,true); 
        return $arrParam; 
    }   
    
	    
}

?>
