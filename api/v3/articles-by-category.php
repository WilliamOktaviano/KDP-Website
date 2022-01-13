<?php
require_once '../../_config.php';  
require_once '_include.php';

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Article.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ArticleCategory.class.php';

function getNewObj(){
    return new Article();
}

$OBJ = getNewObj();

$articleCategory = new ArticleCategory();

$imageUrl = array( 
    'pkey' => array('paramName' => 'key'),   
    'url' => array('paramName' => 'url'),
);

$categoryDetail = array(
    'pkey' => array('paramName' => 'key'),
    'categoryname' => array('paramName' => 'name', 'mandatory' => true, 'ref' => array('obj' => $articleCategory)),
);

$API_FIELDS = array_merge($API_FIELDS, array(
    'code' => array('paramName' => 'code'),
    'title' => array('paramName' => 'title', 'mandatory' => true),
    'category_detail' => array('paramName' => 'category_detail', 'updatable' => false, 'dataset' => $OBJ->arrDataDetail, 'tableName' => $OBJ->tableNameDetail, 'detail' =>  $categoryDetail),
    'shortdesc' => array('paramName' => 'short_description'),
    'publishdate' => array('paramName' => "publish_date", 'mandatory' => true, 'return' => array('format' => 'mktime')),
    'detail' => array('paramName' => 'detail'),
    'featured' => array('paramName' => 'is_featured'),
    'statuskey'  =>  array('paramName' => 'status_key'),
    'statusname'  =>  array('paramName' 
                            => 'status_name', 'updatable' => false),
    'image_url' => array('paramName' => 'image_url',  'updatable' => false, 'detail' =>  $imageUrl, 'return' => array('format' => 'file', 'paramName' => 'file', 'path' => 'article/'))
));


require_once '_global.php';

switch($ACTION){ 
   
    case 'GET' :  
            
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
            /*$searchDataSet = array_column($API_FIELDS,null,'paramName');
            foreach($_GET as $key => $searchBy){
                // cari ke structure, fieldny ap.. 
                // kalo gk bisa searchable
                if(in_array($key, $arrKeywords) || !isset($searchDataSet[$key]['search']['field'])) continue;  
                 
                array_push($criteria, $searchDataSet[$key]['search']['field'] . ' = ' .$OBJ->oDbCon->paramString($searchBy) );  
            }*/
        
            if(!empty($_GET['category_id'])){
                $cat = $_GET['category_id'];
                $cat = explode(',',$cat);  
                
                $rsItemCat = $articleCategory->searchDataRow(array($articleCategory->tableName.'.pkey'),
                                                             ' and '.$articleCategory->tableName.'.code in ('.$OBJ->oDbCon->paramString($cat,',').') '
                                                            );
                
                $cat = array_column($rsItemCat,'pkey');
                array_push( $criteria,$OBJ->tableNameDetail.'.categorykey in ('.$OBJ->oDbCon->paramString($cat,',').')');
            }
                
            array_push( $criteria, $OBJ->tableName.'.publishdate <= now()' );
        
            //$OBJ->setLog($criteria,true);
        
            $criteria  =  implode(' AND ', $criteria);
            if (!empty($criteria)) $criteria = ' AND ' . $criteria;
         
            // LIMIT   
            $rowsPerPage = isset($_GET['rows_per_page']) ? $_GET['rows_per_page']: $OBJ->loadSetting('adminTotalRowsPerPage');
            $offset = isset($_GET['offset']) ? $_GET['offset'] : 1; 
            if($offset <= 0) $offset = 1;
            $limitFrom = ($offset - 1) * $rowsPerPage;  
            $limit = ' limit '.$limitFrom.','.$rowsPerPage; 
             
            //$OBJ->setLog($criteria,true);
        
            $rs = $OBJ->searchDataWithCategory('','',true,$criteria,'order by '.$OBJ->tableName.'.publishdate desc, '.$OBJ->tableName.'.title asc',$limit);

            //$rs =  $OBJ->oDbCon->doQuery( $query . $order . $limit  );

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
          
            $rs = $OBJ->compileAPIField($rs,$API_FIELDS,$showDetail);
         
            //$rs = array_column($rs,null,'code');

            // compile array for return 
            if(empty($rs)){
                $RETURN_VALUE['response_code'] = 400;
                $RETURN_VALUE['message'] = $class->errorMsg[213];
            }else{ 
                //$rsCountedTotalRows = $OBJ->countTotalRows($criteria);
                //$totalDataRows = $OBJ->getCountedTotalRows($rsCountedTotalRows); 
                //$totalPages = ceil($totalDataRows/$rowsPerPage);
                $totalPages = ceil( $OBJ->getTotalRowsWithCategory($criteria) / $rowsPerPage); 
 
                $RETURN_VALUE['response_code'] =  200;
                $RETURN_VALUE['data'] = $rs;  
                $RETURN_VALUE['offset'] = $offset;  
                $RETURN_VALUE['rows_per_page'] = $rowsPerPage;  
                $RETURN_VALUE['total_pages'] = $totalPages;    
                $RETURN_VALUE['total_rows'] = $totalDataRows;  
                $RETURN_VALUE['message'] = '';  
            }

            break;
          
     default : break;    
}

http_response_code($RETURN_VALUE['response_code'] );
echo json_encode($RETURN_VALUE); 
  
?>