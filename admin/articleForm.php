<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass(array('Article.class.php','ArticleCategory.class.php'));
$article = createObjAndAddToCol( new Article()); 
$articleCategory = createObjAndAddToCol( new ArticleCategory()); 
    
$obj= $article;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
     
$formAction = 'articleList';
$rsDetail = array();
 
$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;
  
$_POST['publishDate'] = date('d / m / Y'); 

$rs = prepareOnLoadData($obj); 

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	
    $rsDetail = $obj->getDetailWithRelatedInformation($id);
	   
    if (!empty($rs[0]['categorykey'])){
		$rsCategory = $articleCategory->getDataRowById($rs[0]['categorykey']);
        $categoryName =  $articleCategory->getPath($rsCategory[0]['pkey']);
		$_POST['categoryName'] = $categoryName[0]['path'];
	}
	  
 	$rsItemImage = array();  
	if( !empty($rs[0]['file'])){
		$rsItemImage[0]['file'] =  $rs[0]['file'];
        $rsItemImage[0]['phpthumbhash'] = getPHPThumbHash($rsItemImage[0]['file']);
	
		$sourcePath = $obj->defaultDocUploadPath.$obj->uploadFolder.$id;
		$destinationPath = $obj->uploadTempDoc.$obj->uploadFolder.$id; 
		$obj->deleteAll($destinationPath); 
	
		if(!is_dir($destinationPath)) 
			mkdir ($destinationPath,  0755, true);
				
		$obj->fullCopy($sourcePath,$destinationPath); 
	}
	 
} 

$arrStatus = $obj->generateComboboxOpt(array('data' => $obj->getAllStatus(),'label' => 'status'));  


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 


<script type="text/javascript"> 
 jQuery(document).ready(function(){  
        var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
              
        var article = new Article(tabID,"<?php echo $obj->uploadFolder; ?>",<?php echo json_encode($rsItemImage); ?>);
    
        prepareHandler(article);   
        
         var fieldValidation =  {
                                    code: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.code[1]
                                            }, 
                                        }
                                    }, 

                                    title: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.title[1]
                                            }, 
                                        }
                                    },
                                } ; 
 
        setFormValidation(getTabObj(tabID), $('#defaultForm-' + tabID), fieldValidation, <?php echo json_encode($obj->validationFormSubmitParam()); ?>  );
  
        
    });
</script>

</head> 

<body> 
<div style="width:100%; margin:auto; " class="tab-panel-form">   
  <div class="notification-msg"></div>
  
  <form id="defaultForm" method="post" class="form-horizontal" action="<?php echo $formAction; ?>">
         <?php prepareOnLoadDataForm($obj); ?>    
        <?php echo $obj->generateLangOptions(); ?> 
      
        <div class="div-table main-tab-table-2">
              <div class="div-table-row">
                    <div class="div-table-col">  
                  		   	<div class="div-tab-panel">  
                                    <div class="div-table-caption border-orange"><?php echo ucwords($obj->lang['generalInformation']); ?></div>
                                 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['status']); ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php echo  $obj->inputSelect('selStatus', $arrStatus); ?>
                                        </div> 
                                    </div> 
                              
                                     <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['code']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputAutoCode('code'); ?>
                                        </div> 
                                     </div>
                                
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['title']); ?></label> 
                                        <div class="col-xs-9"> 
                                              <?php echo $obj->inputText('title',array('multilang' => true )); ?>
                                        </div> 
                                    </div>  
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['category']); ?></label> 
                                        <div class="col-xs-9"> 
  
                                        <div class="div-table mnv-transaction transaction-detail" style="width:100%">
                                            <?php 
                                                $totalRows = count($rsDetail);
                                                for ($i=0;$i<=$totalRows; $i++){ 

                                                    $class =  'transaction-detail-row';
                                                    $overwrite = true;
                                                    $readonly = false;
                                                    $disabled = false; 
    //                                                $style = '';

                                                    if ($i == $totalRows ){
                                                        $class = 'detail-row-template row-template';
                                                        $overwrite = false;
                                                        $disabled = true; 
                                                        $isLocked = false;
    //                                                    $style = 'style="display:none"';
                                                    } else{ 
                                                        $_POST['hidDetailKey[]'] =  $rsDetail[$i]['pkey'];
                                                        $_POST['categoryName[]'] =  $rsDetail[$i]['categoryname'];
                                                        $_POST['hidCategoryKey[]'] =  $rsDetail[$i]['categorykey'];


                                                    }
                                                    $hideDeleteIcon = '';  
                                                ?>
                                                <div class="div-table-row odd-style-adjustment <?php echo $class; ?> "  > 
                                                    <div class="div-table-col" style="padding-left:0;"> 
                                                        <div class="flex">     
                                                            <div class="consume" style="width:270px">
                                                                <?php echo $obj->inputText('categoryName[]',array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                                                                <?php echo $obj->inputHidden('hidCategoryKey[]',array('overwritePost' => $overwrite,'disabled' => $disabled)); ?>
                                                                <?php echo $obj->inputHidden('hidDetailKey[]',array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                                                            </div>
                                                            <div class="icon-col <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputLinkButton('btnAddCategory' , '<i class="fas fa-plus-circle"></i>', array('class' => 'btn btn-link add-row-button','etc' => 'attr-template="detail-row-template"')); ?></div>
                                                            <div class="icon-col <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' =>  'tabIndex="-1" style="padding:6px 0; '.$hideDeleteIcon.'"')); ?></div>

                                                        </div> 
                                                    </div> 
                                                </div>   
                                            <?php }	 ?>  

                                        </div>
                                    </div> 
                                    </div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['publishDate']); ?></label> 
                                        <div class="col-xs-9"> 
                                              <?php echo $obj->inputDate('publishDate'); ?>  
                                        </div> 
                                    </div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['featuredArticle']); ?></label> 
                                        <div class="col-xs-9"> 
                                              <?php echo $obj->inputCheckBox('isFeatured'); ?>   
                                        </div> 
                                    </div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['tag']); ?></label> 
                                        <div class="col-xs-9"> 
                                              <?php echo $obj->inputText('tag'); ?>
                                        </div> 
                                    </div>  
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['image']); ?></label> 
                                        <div class="col-xs-9"> 
                                             
                                             <!-- image uploader --> 
                                            <div class="item-image-uploader">
                                                <ul class="image-list" ></ul>
                                                <div style="clear:both; height:1em; "></div>
                                                <div class="file-uploader">	
                                                    <noscript><p>Please enable JavaScript to use file uploader.</p></noscript> 
                                                </div>
                                              </div>  
                                            <!-- image uploader --> 
                                            
                                        </div> 
                                    </div>   
                        </div>            
                  </div> 
                  <div class="div-table-col">  
                  		   	<div class="div-tab-panel"> 
                                <div class="div-table-caption border-green"><?php echo ucwords($obj->lang['shortDescription']); ?></div>
                                <div class="form-group">
                                 <div class="col-xs-12"> 
                                    <?php echo  $obj->inputTextArea('txtShortDescription', array('multilang' => true , 'etc' => 'style="height:10em;"')); ?> 
                                 </div>
                                </div>
                            </div>
                  </div>    
            </div>
       </div> 
      
      
        <div class="div-table main-tab-table-1" style="width:100%;">
              <div class="div-table-row">
                    <div class="div-table-col">  
                         <div class="div-tab-panel">  
                             <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['articleContent']); ?></div>
                            <div class="form-group">
                                 <div class="col-xs-12">  
                                     <?php echo  $obj->inputEditor('txtDetail',array('multilang' => true )); ?> 
                                 </div>
                            </div>
                        </div>
                  </div>
            </div>
      </div>     
      
        <div class="form-button-panel" > 
       	 <?php  echo $obj->generateSaveButton(); ?> 
        </div> 
        
    </form>   
   
     <?php  echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>
