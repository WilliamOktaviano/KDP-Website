<?php 
require_once '_config.php'; 
require_once '_include-min.php'; 

$order = 'order by '.$city->tableCategory.'.name asc, '.$city->tableName.'.name asc';
$criteria = '';
 
$rsCity = $city->searchDataForAutoComplete($city->tableName.'.name',$_GET['term'],false,$criteria,$order );
for($i=0;$i<count($rsCity);$i++){
		$rsCity[$i]['value'] = htmlspecialchars_decode($rsCity[$i]['value']); 
}
 
echo json_encode($rsCity); 
die;
  
?>