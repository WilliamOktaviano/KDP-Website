<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';
 
includeClass('NewsletterSubscription.class.php');
$subscribe = createObjAndAddToCol( new NewsletterSubscription()); 

$obj = $subscribe;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 

$formAction = 'subscribeList';
$rs = prepareOnLoadData($obj);  

if (!empty($_GET['id'])){ 

}


$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');    

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 
 
<script type="text/javascript">  
   
	jQuery(document).ready(function(){  
        var tabID = selectedTab.newPanel[0].id;

        subscribe = new NewsletterSubscription(tabID);
        prepareHandler(subscribe); 
        
        var fieldValidation =  {
                                 code: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.code[1]
                                            }, 
                                        }
                                    }, 

                                   email: { 
                                        validators: {
                                            notEmpty: {
                                                message:  phpErrorMsg.email[1]
                                            },
                                            emailAddress: {
                                                message: phpErrorMsg.email[3]
                                            },
                                            
                                        } 
                                    },

                                } ; 
        
        setFormValidation(getTabObj(), $('#defaultForm-' + tabID), fieldValidation, <?php echo json_encode($obj->validationFormSubmitParam()); ?>  );
    });
    
</script>

</head> 

<body>                    
<div style="width:100%; margin:auto; " class="tab-panel-form">   
  <div class="notification-msg"></div>
  
  <form id="defaultForm" method="post" class="form-horizontal" action="<?php echo $formAction; ?>" > 
    <?php prepareOnLoadDataForm($obj); ?>   
     
       <div class="div-table main-tab-table-2">
                <div class="div-table-row">
                    <div class="div-table-col"> 
      						 <div class="div-tab-panel"> 
                                   <div class="div-table-caption border-orange"><?php echo ucwords($obj->lang['generalInformation']); ?></div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['status']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputSelect('selStatus', $arrStatus, array('disabled' => true)); ?>
                                        </div> 
                                    </div>  
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['code']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputAutoCode('code'); ?>
                                        </div> 
                                    </div>    
                                   <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['email']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('email'); ?> 
                                        </div> 
                                    </div>
                                   <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['phone']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('phone'); ?> 
                                        </div> 
                                    </div>
                                 
                             </div>
                         
                    </div>
                     <div class="div-table-col">   
                        <div class="div-tab-panel"> 
                            <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['note']); ?></div>
                            <div class="form-group">
                                        <div class="col-xs-12"> 
                                            <?php echo  $obj->inputTextArea('trDesc', array('etc' => 'style="height:10em;"')); ?>                                         </div> 
                            </div>
                            
                         </div>
                    </div>
                    
           </div>
      </div>          
      
         <div class="form-button-margin"></div>
         <div class="form-button-panel" >  
         <?php  echo $obj->generateSaveButton();?>

        </div> 
        
    </form>  

     <?php echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>
