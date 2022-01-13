<?php
require_once '_config.php';  
require_once '_include-fe-v2.php';
require_once '_global.php';  

if(!empty($_GET['ref'])) $_POST['referralCode'] = $_GET['ref'];

if($class->isActiveModule('membershipLevel')){ 

	includeClass(array('MembershipLevel.class.php'));
	$membershipLevel = new MembershipLevel();


	$rsLevel = $membershipLevel->searchDataRow(array($membershipLevel->tableName.'.pkey',  $membershipLevel->tableName.'.name', 'lower('.$membershipLevel->tableName.'.name) as lowername', $membershipLevel->tableName.'.sellingprice'),
													 ' and '.$membershipLevel->tableName.'.statuskey = 1');
	$arrMembership = $class->generateComboboxOpt(array('data' =>$rsLevel )); 
 
	$arrType = array_column($rsLevel,null,'lowername');

	// ambil default membership level kalo blm ada / tdk dikirim
	$selectedType = (!empty($_GET['type']) && isset($arrType[$_GET['type']])) ? $arrType[$_GET['type']] : $membershipLevel->getDefaultLevel()[0];
	
	$_POST['selMembership']  = $selectedType['pkey'];
	
	$arrTwigVar ['inputSelMembership']  = $class->inputSelect('selMembership',$arrMembership);  
	$arrTwigVar ['inputHidSelMembership']  = $class->inputHidden('selMembership');  
	$arrTwigVar ['packageName'] = $selectedType['name'];
	$arrTwigVar ['packagePrice'] = $selectedType['sellingprice'];
}

$arrTwigVar ['inputPassword'] =  $class->inputPassword('password'); 
$arrTwigVar ['inputPasswordConfirmation'] =  $class->inputPassword('passwordConfirmation'); 
$arrTwigVar ['inputUserName'] =  $class->inputText('userName'); 
$arrTwigVar ['inputName'] =  $class->inputText('name'); 
$arrTwigVar ['inputPhone'] =  $class->inputText('phone'); 
$arrTwigVar ['inputEmail'] =  $class->inputText('email'); 

$arrTwigVar['inputUserNamePlaceholder'] = $class->inputText('userName', array( 'etc' => 'placeholder="'.$class->lang['username'].'"')); 
$arrTwigVar['inputPasswordPlaceholder'] = $class->inputPassword('password', array( 'etc' => 'placeholder="'.$class->lang['password'].'"')); 
$arrTwigVar['inputPasswordConfirmationPlaceholder'] = $class->inputPassword('passwordConfirmation', array( 'etc' => 'placeholder="'.$class->lang['passwordConfirmation'].'"')); 
$arrTwigVar['inputNamePlaceholder'] = $class->inputText('name', array( 'etc' => 'placeholder="'.$class->lang['name'].'"')); 
$arrTwigVar['inputPhonePlaceholder'] = $class->inputText('phone', array( 'etc' => 'placeholder="'.$class->lang['phone'].'"')); 
$arrTwigVar['inputEmailPlaceholder'] = $class->inputText('email', array( 'etc' => 'placeholder="'.$class->lang['email'].'"'));

$arrTwigVar ['inputChkAgreement'] = $class->inputCheckBox('chkAgree');
$arrTwigVar ['btnSubmit'] =   $class->inputSubmit('btnSave',$class->lang['register']); 


$_POST['action'] ='add';  
$arrTwigVar ['inputHidAction'] =  $class->inputHidden('action'); 
$arrTwigVar ['inputHidReferral'] =  $class->inputHidden('referralCode'); 
 
echo $twig->render('registration.html', $arrTwigVar);
?>