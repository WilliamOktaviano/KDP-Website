<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass(array("Contact.class.php","ContactCategory.class.php"));
$contact = new Contact();
$contactCategory = new ContactCategory();
$obj= $contact;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
     
$formAction = 'contactUsList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;
$editCategoryInactiveCriteria = '';

$_POST['postdate'] = date('d / m / Y');

$rs = prepareOnLoadData($obj);  

if (!empty($_GET['id'])){ 
	$id = $_GET['id']; 
    $editCategoryInactiveCriteria = ' or '.$contactCategory->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['categorykey']);
} 

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');   
$arrCategory = $class->convertForCombobox($contactCategory->searchData('','',true, ' and ('.$contactCategory->tableName.'.statuskey = 1'. $editCategoryInactiveCriteria.')'),'pkey','name');  
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 


<script type="text/javascript"> 
 
 	
	jQuery(document).ready(function(){  
		 
        var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
        setOnDocumentReady(tabID); 
         
        
		 $('#defaultForm-' + tabID.newPanel[0].id )
			.bootstrapValidator({ 
				feedbackIcons: {
					valid: 'glyphicon glyphicon-ok',
					invalid: 'glyphicon glyphicon-remove',
					validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                name: { 
                    validators: {
                        notEmpty: {
                            message: 'Nama harus diisi.'
                        }, 
                    }
                },  
				
				phone: { 
                    validators: {
                        notEmpty: {
                            message: 'Telepon harus diisi.'
                        }, 
                    }
				}, 
				
				email: { 
                    validators: {
                        notEmpty: {
                            message: 'Email harus diisi.'
                        },  
						emailAddress: {
							message: 'Alamat email tidak valid.'
						}
                    }
				}, 
				
				message: { 
                    validators: {
                        notEmpty: {
                            message: 'Pesan harus diisi.'
                        }, 
                    }
				}, 
				
			 
            }
        })
        .on('success.form.bv', function(e) { 
              <?php echo $obj->submitFormScript(); ?>
        });
		  
		
	});
			
</script>

</head> 

<body> 
<div style="width:100%; margin:auto; " class="tab-panel-form">   
  <div class="notification-msg"></div>
  
  <form id="defaultForm" method="post" class="form-horizontal" action="<?php echo $formAction; ?>">
        <?php prepareOnLoadDataForm($obj); ?> 
      
        <div class="div-table main-tab-table-2">
              <div class="div-table-row">
                    <div class="div-table-col">  
                  		   	<div class="div-tab-panel">    
                              	    <div class="div-table-caption border-orange">Informasi Pengirim</div>
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label">Status</label> 
                                        <div class="col-xs-9"> 
                                             <?php echo  $obj->inputSelect('selStatus', $arrStatus, array('readonly' => true)); ?>
                                        </div> 
                                    </div>    
                                     <div class="form-group">
                                        <label class="col-xs-3 control-label">Kode</label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputAutoCode('code', array('readonly' => true)); ?>
                                        </div> 
                                     </div>
                                     <div class="form-group">
    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['category']); ?></label> 
                                            <div class="col-xs-9"> 
                                                <?php echo $obj->inputSelect('selCategory',$arrCategory, array('readonly' => true)); ?>
                                            </div> 
                                        </div>  
                                     <div class="form-group">
                                        <label class="col-xs-3 control-label">Nama</label> 
                                        <div class="col-xs-9"> 
                                              <?php echo $obj->inputText('name', array('readonly' => true)); ?>
                                        </div> 
                                     </div> 
                                     <div class="form-group">
                                        <label class="col-xs-3 control-label">Tgl. Kirim</label> 
                                        <div class="col-xs-9"> 
                                              <?php echo $obj->inputText('postDate', array('readonly' => true)); ?> 
                                        </div> 
                                     </div> 
                                     <div class="form-group">
                                        <label class="col-xs-3 control-label">Telepon</label> 
                                        <div class="col-xs-9"> 
                                              <?php echo $obj->inputText('phone', array('readonly' => true)); ?> 
                                        </div> 
                                     </div> 
                                     <div class="form-group">
                                        <label class="col-xs-3 control-label">Email</label> 
                                        <div class="col-xs-9"> 
                                              <?php echo $obj->inputText('email', array('readonly' => true)); ?>  
                                        </div> 
                                     </div> 
                            </div>   
                  </div>  
                    <div class="div-table-col">  
                        <div class="div-tab-panel">    
                            <div class="div-table-caption border-orange">Isi Pesan</div>
                            <div class="form-group"> 
                                <div class="col-xs-12"> 
                                     <?php echo  $obj->inputTextArea('message', array('etc' => 'style="height:10em;"',  'readonly' => true)); ?> 
                                </div> 
                            </div> 
                        </div>
                    </div>    
             </div>
        </div>        
       
    </form>   
     <?php echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>
