<?php 
require_once '_config.php';  
require_once '_include-fe-v2.php';
require_once '_global.php';  

if(!$security->isMemberLogin(false))  header('location:/logout'); 

includeClass(array('Customer.class.php','BusinessCategory.class.php'));
$customer = new Customer();
$city = new City();
$businessCategory = new BusinessCategory();

$rs = $customer->getDataRowById(USERKEY);
$rsCustBusiness = $customer->getCustomerBusinessDetail($rs[0]['pkey']);
$arrBusinessDetailKey =  array_column($rsCustBusiness,'refbusinesskey'); 
$rsCustomerFeatures = $customer->getFeaturesDetail($rs[0]['pkey']);


$rsMembership = array();
if($class->isActiveModule('MembershipLevel')){  
	includeClass(array('MembershipLevel.class.php'));
	$membershipLevel = new MembershipLevel();
	$rsMembership = $membershipLevel->getDataRowById($rs[0]['membershiplevel']);
	$rsMembership[0]['phpthumbhash'] =  getPHPThumbHash($rsMembership[0]['file']);	
}


$_POST['userCode'] = $rs[0]['code'];
$_POST['userName'] = $rs[0]['username'];
$_POST['membership'] = $rsMembership[0]['name'];
$_POST['name'] = $rs[0]['name'];  
$_POST['phone'] = $rs[0]['phone']; 
$_POST['mobile'] = $rs[0]['mobile']; 
$_POST['email'] = $rs[0]['email']; 
$_POST['address'] = $rs[0]['address'];
$_POST['zipCode'] = $rs[0]['zipcode']; 
$_POST['fax'] = $rs[0]['fax'];
$_POST['hidCityKey'] = $rs[0]['citykey']; 
$_POST['hidId'] = $rs[0]['pkey']; 
$_POST['hidLatLng'] = $rs[0]['latlng']; 
$_POST['expDate'] = $class->formatDBDate($rs[0]['expdate']); 

$_POST['sex'] = $rs[0]['sexkey']; 
$_POST['IDNumber'] = $rs[0]['idnumber'];
$_POST['hidPlaceOfBirthKey'] = $rs[0]['placeofbirth'];
$_POST['dob'] = (!empty($rs[0]['dateofbirth'])) ? $class->formatDBDate($rs[0]['dateofbirth'],'d / m / Y') : '';

$_POST['FBAccount'] = $rs[0]['fbaccount']; 
$_POST['IGAccount'] = $rs[0]['igaccount']; 
$_POST['mapAddress'] = $rs[0]['mapaddress']; 
$_POST['companyName'] = $rs[0]['companyname'];
$_POST['selBusiness'] = $rs[0]['mainbusinesskey'];
$_POST['selBusinessDetailKey[]'] = $arrBusinessDetailKey;

$arrBusinessDetail = $businessCategory->searchDataRow(array($businessCategory->tableName.'.pkey',$businessCategory->tableName.'.name'),
								   	' and '.$businessCategory->tableName.'.statuskey = 1'
                                                      
								  );

$arrBusinessDetail = $customer->generateComboboxOpt(array('data' => $arrBusinessDetail)); 

$arrTwigVar ['inputSelBusinessDetailKey']  =  $class->inputSelect('selBusinessDetailKey[]', $arrBusinessDetail, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));; 


$arrSex = $class->convertForCombobox($class->getSex(),'pkey','name');  
$arrCity = $class->convertForCombobox($city->searchData('','',true,' and '.$city->tableName.'.statuskey = 1','order by '.$city->tableName.'.name asc'),'pkey','name');
$rsCity = $city->searchData($city->tableName.'.pkey',$rs[0]['citykey'],true);
if (!empty($rsCity[0]['name'])) 
    $_POST['cityName'] = $rsCity[0]['citycategoryname'];
  
$arrTwigVar ['inputHidId'] =  $class->inputHidden('hidId');

$_POST['action'] ='edit';  
$arrTwigVar ['inputHidAction'] =  $class->inputHidden('action'); 
 
$arrTwigVar ['rs'] =  $rs[0];
$arrTwigVar ['rsMembership'] =  $rsMembership[0];
$arrTwigVar ['features'] = $rsCustomerFeatures;
$arrTwigVar ['inputCurrentPassword'] =  $class->inputPassword('currentPassword'); 
$arrTwigVar ['inputNewPassword'] =  $class->inputPassword('password'); 
$arrTwigVar ['inputPasswordConfirmation'] =  $class->inputPassword('passwordConfirmation'); 
$arrTwigVar ['inputUserCode'] =  $class->inputText('userCode', array('readonly' => true )); 
$arrTwigVar ['inputUserName'] =  $class->inputText('userName', array('readonly' => true )); 
$arrTwigVar ['inputMembership'] =  $class->inputText('membership',  array('readonly' => true )); 
$arrTwigVar ['inputName'] =  $class->inputText('name'); 
$arrTwigVar ['inputMapAddress'] =  $class->inputText('mapAddress', array('class' => 'form-control search-address', 'etc' => 'placeholder="'.$class->lang['searchLocation'].'"')); 
$arrTwigVar ['hidLatLng'] = $class->inputHidden('hidLatLng');    
$arrTwigVar ['inputMembershipExpDate'] =  $class->inputText('expDate', array('readonly' => true)); 
$arrTwigVar ['referralLink'] =  HTTP_HOST.'registration/ref='.$rs[0]['code']; 

//$arrTwigVar ['inputSelPOB'] =  $class->inputSelect('hidPlaceOfBirthKey',$arrCity); 
$autoCompleteCity =  $class->inputAutoComplete(array(  
                                                            'element' => array('value' => 'cityName',
                                                                               'key' => 'hidCityKey'),
                                                            'source' =>array(
                                                                                'url' => 'ajax-city.php',
                                                                                'data' => array(  'action' =>'searchData' )
                                                                            ) , 
                                                            'explodeScript' => true
    
                                                          )
                                                    );  

$arrTwigVar ['JSScript']  = str_replace(array('<script type="text/javascript">','</script>'),array('',''),$autoCompleteCity['script']); 
 
$arrTwigVar ['inputCity']  = $autoCompleteCity['input']; 
$arrTwigVar ['inputPhone'] =  $class->inputText('phone'); 
$arrTwigVar ['inputMobile'] =  $class->inputText('mobile');
$arrTwigVar ['inputEmail'] =  $class->inputText('email'); 
$arrTwigVar ['inputAddress'] =  $class->inputTextArea('address', array( 'etc' => 'style="height:10em"'));  
$arrTwigVar ['inputZipcode'] =  $class->inputText('zipCode');
$arrTwigVar ['inputFax'] =  $class->inputText('fax');
$arrTwigVar ['inputCompanyName'] =  $class->inputText('companyName'); 

if($class->isActiveModule('BusinessCategory')){
	includeClass('BusinessCategory.class.php');
	$businessCategory = new BusinessCategory();
	$arrBusiness = $businessCategory->searchDataRow(array($businessCategory->tableName.'.pkey',$businessCategory->tableName.'.name'),
								   	' and '.$businessCategory->tableName.'.statuskey = 1 ' .$editBusinessInactiveCriteria 
								  );
	$arrBusiness = $class->generateComboboxOpt(array('data' => $arrBusiness));
	$arrTwigVar ['inputSelBusiness'] =  $class->inputSelect('selBusiness',$arrBusiness); 
}


$arrTwigVar ['inputCurrentPasswordPlaceholder'] =  $class->inputPassword('currentPassword', array( 'etc' => 'placeholder="'.$class->lang['password'].'"')); 
$arrTwigVar ['inputNewPasswordPlaceholder'] =  $class->inputPassword('password', array( 'etc' => 'placeholder="'.$class->lang['newPassword'].'"')); 
$arrTwigVar ['inputPasswordConfirmationPlaceholder'] =  $class->inputPassword('passwordConfirmation', array( 'etc' => 'placeholder="'.$class->lang['passwordConfirmation'].'"')); 
$arrTwigVar ['inputUserNamePlaceholder'] =  $class->inputText('userName', array('readonly' => true ), array( 'etc' => 'placeholder="'.$class->lang['username'].'"')); 
$arrTwigVar ['inputNamePlaceholder'] =  $class->inputText('name', array( 'etc' => 'placeholder="'.$class->lang['name'].'"')); 
$arrTwigVar ['inputIDNumberPlaceholder'] =  $class->inputText('IDNumber', array( 'etc' => 'placeholder="'.$class->lang['IDNumber'].'"')); 
$arrTwigVar ['inputBirthDatePlaceholder'] =  $class->inputDate('dob', array( 'etc' => 'placeholder="'.$class->lang['dateOfBirth'].'"','add-class'=>'label-style')); 
$arrTwigVar ['inputGenderPlaceholder'] =  $class->inputSelect('sex',$arrSex); 
$arrTwigVar ['inputPhonePlaceholder'] =  $class->inputText('phone', array( 'etc' => 'placeholder="'.$class->lang['phone'].'"')); 
$arrTwigVar ['inputMobilePlaceholder'] =  $class->inputText('mobile', array( 'etc' => 'placeholder="'.$class->lang['mobilePhone'].'"')); 
$arrTwigVar ['inputEmailPlaceholder'] =  $class->inputText('email', array( 'etc' => 'placeholder="'.$class->lang['email'].'"')); 
$arrTwigVar ['inputAddressPlaceholder'] =  $class->inputTextArea('address', array( 'etc' => 'style="height:10em" placeholder="'.$class->lang['address'].'"')); 
$arrTwigVar ['inputAddressRowPlaceholder'] =  $class->inputText('address', array( 'etc' => 'placeholder="'.$class->lang['address'].'"')); 
$arrTwigVar ['inputZipcodePlaceholder'] =  $class->inputText('zipCode', array( 'etc' => 'placeholder="'.$class->lang['zipCode'].'"')); 
$arrTwigVar ['inputFaxPlaceholder'] =  $class->inputText('fax', array( 'etc' => 'placeholder="'.$class->lang['fax'].'"')); 


/*
$arrTwigVar ['inputFBPlaceholder'] =  $class->inputText('FBAccount', array( 'etc' => 'placeholder="'.$class->lang['fbAccount'].'"', 'add-class'=>'medsos-account')); 
$arrTwigVar ['inputIGPlaceholder'] =  $class->inputText('IGAccount', array( 'etc' => 'placeholder="'.$class->lang['igAccount'].'"', 'add-class'=>'medsos-account')); 
*/ 


$arrTwigVar ['btnAddRows'] =  $class->inputLinkButton('btnAddItemRows' , '<i class="fas fa-plus-circle"></i>', array('class' => 'btn btn-link add-row-button','etc' => 'attr-template="detail-row-template"'));
$arrTwigVar ['btnDeleteRows'] =  $class->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' =>  'tabIndex="-1" style="padding:6px 0;"'));

$arrTwigVar ['ssoTypeKey'] =  $rs[0]['ssotypekey']; 

$_POST['hidModifiedOn'] =  $rs[0]['modifiedon']; 
$arrTwigVar['hidModifiedOn'] = $class->inputHidden('hidModifiedOn'); 

$arrTwigVar ['btnSubmit'] =   $class->inputSubmit('btnSave',$class->lang['save']); 
 
echo $twig->render('profile.html', $arrTwigVar);

?>