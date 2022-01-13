<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';
 
includeClass(array("Warehouse.class.php"));
$warehouse = new Warehouse();

$rsWarehouse = $warehouse->searchDataRow(array($warehouse->tableName.'.pkey',$warehouse->tableName.'.code',$warehouse->tableName.'.name',$warehouse->tableName.'.address',$warehouse->tableName.'.phone',$warehouse->tableName.'.location'),
                                      ' and '.$warehouse->tableName.'.statuskey = 1 and '.$warehouse->tableName.'.location <> \'\'');

foreach($rsWarehouse as $key => $data)
    $rsWarehouse[$key]['address'] = str_replace(chr(13),'<br>',$rsWarehouse[$key]['address']);

$arrLocation = array();
$arrLocation['type'] = 'FeatureCollection';
$arrLocation['features'] = array();
    
foreach($rsWarehouse as $key=>$row){
    $location = explode(',',$row['location']);
    
    $temp = array(
        'geometry' => array(
                            'type' => 'Point',
                            'coordinates' => array(floatval($location[1]),floatval($location[0])), // ntah kenapa dibalik lat lng nya
                        ),
        'type' => 'Feature',
        'properties' => array(   
               'pkey' =>  $row['pkey'],
               'name' =>  $row['name'], 
               'address' => $row['address'],
               'phone' => (!empty($row['phone'])) ? $row['phone'] : '',
               'storeid'=> $row['code']
        ) 
    );
    
    array_push($arrLocation['features'],$temp);
}

//$class->setLog(json_encode($arrLocation),true);
$arrTwigVar['rsStores'] = $rsWarehouse; 
$arrTwigVar['location'] = json_encode($arrLocation);

echo $twig->render('stores.html', $arrTwigVar);

?>