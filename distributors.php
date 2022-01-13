<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';
 
includeClass(array("Partners.class.php"));
$partners = new Partners();

$rsPartners = $partners->searchDataRow(array($partners->tableName.'.pkey',$partners->tableName.'.code',$partners->tableName.'.name',$partners->tableName.'.phone',$partners->tableName.'.address',$partners->tableName.'.location'),
                                      ' and '.$partners->tableName.'.statuskey = 1');


foreach($rsPartners as $key => $data)
    $rsPartners[$key]['address'] = str_replace(chr(13),'<br>',$rsPartners[$key]['address']);

$arrLocation = array();
$arrLocation['type'] = 'FeatureCollection';
$arrLocation['features'] = array();
    
foreach($rsPartners as $key=>$row){
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
               'phone' => $row['phone'],
               'address' => $row['address'],
               'storeid'=> $row['code']
        ) 
    );
    
    array_push($arrLocation['features'],$temp);
}

//$class->setLog(json_encode($arrLocation),true);
$arrTwigVar['rsPartners'] = $rsPartners; 
$arrTwigVar['location'] = json_encode($arrLocation);

echo $twig->render('distributors.html', $arrTwigVar);

?>