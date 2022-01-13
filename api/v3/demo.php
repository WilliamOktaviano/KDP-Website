<?php

//define('SECRET_KEY', '91f5b2e8375fb417e8efb5546b38299b');
define('SECRET_KEY', '123456');
//define('API_URL', 'https://cobain.info/api/v3/');
define('API_URL', 'https://minerva.local/api/v3/');

getItems();
// updateItems();
//addItems();
//getItemCategories();
// updateItemCategories();
// addItemCategories();
// getCustomers();
//updateCustomers();

function getItems(){
    $url = API_URL.'items/category_id=ICAT00021&show_detail=1'; 
    $payload = array();
    $action = 'GET';
    execute($url, $payload, $action);
}

function updateItems(){
    $url = API_URL.'items'; 
    $payload = array();
    $action = 'PUT';

    array_push($payload, array(
        'code' => "ITM06174",
        'name' => "Test Item #1 - EDITED",
        'condition' => "Baru",
        'category_id' => "ICAT00040",
        'weight_unit' => "KG",    
        'base_unit' => "PCS",
        'brand' => "Bebelac",
        'selling_price' => 100000,
        'max_stock_qty' => 1,
        'weight' => 2,
        'short_description' => '12345678901234567890123456789012345678901234567890123456789012345678901234567890',
        'image_url' => array( 
                                array(  
                                    'url' => 'https://kenaridjaja.biz/wp-content/uploads/2013/06/CISA-46240-25-US14.jpg' 
                                  ),
                                array( 
                                    'url' => 'https://kenaridjaja.biz/wp-content/uploads/Cisa-46420-50.jpg' 
                                  ) 
                            ),
    ));

    execute($url, $payload, $action);
}

function addItems(){
    $url = API_URL.'items'; 
    $payload = array();
    $action = 'POST';

    array_push($payload, array( 
        'name' => "Test Item #1".time(),
        'condition' => "Pernah Digunakan",
        'category_id' => "ICAT00040",
        'weight_unit' => "GRAM",    
        'base_unit' => "PCS",
        'brand' => "Good Smile Company",
        'selling_price' => 999999,
        'max_stock_qty' => 9,
        'weight' => 2000,
        'short_description' => '12345678901234567890123456789012345678901234567890123456789012345678901234567890',
        'image_url' => array( 
                                array( 
                                    'is_primary' => 1,
                                    'url' => 'https://kenaridjaja.biz/wp-content/uploads/2013/06/CISA-46240-25-US14.jpg' 
                                  ),
                                array( 
                                    'url' => 'https://kenaridjaja.biz/wp-content/uploads/Cisa-46420-50.jpg' 
                                  ) 
                            ),
    ));

    execute($url, $payload, $action);
}

function getItemCategories(){
    $url = API_URL.'item-categories/code=ICAT00003,ICAT00011'; 
    $payload = array();
    $action = 'GET';
    execute($url, $payload, $action);
}

function updateItemCategories(){
    $url = API_URL.'item-categories';
    $payload = array();
    $action = 'PUT';

    array_push($payload, array(
        'code' => "ICAT00011",
        'name' => "Test Category #1",
        'short_description' => "Returned to previous state for the same purpose still..... hahaha",
        'parent_key' => 0,
        'order_list' => 0,
        'is_leaf' => 1,
    ));

    execute($url, $payload, $action);
}

function addItemCategories(){
    $url = API_URL.'item-categories'; 
    $payload = array();
    $action = 'POST';

    array_push($payload, array(
        'name' => "Test Category #2",
        'short_description' => "123456789012345678901234567890123456789012345678901234567890",
        'parent_key' => 0,
        'order_list' => 0,
        'is_leaf' => 1,
    ));

    execute($url, $payload, $action);
}

function getCustomers(){
    $url = API_URL.'customers/code=CO00011,CO00010'; 
    $payload = array();
    $action = 'GET';
    execute($url, $payload, $action);
}

function updateCustomers(){
    $url = API_URL.'customers/';
    $payload = array();
    $action = 'PUT';

    array_push($payload,
        array(
            'code' => "CO00012",
            'name' => 'dummy',
            'category_name' => 'End User',
            'city_name' => "Jakarta",
            'address' => "EDITED",
            'zip_code' => "14240",
            'phone' => "021456789",
            'mobile' => "0812345678",
            'email' => "test_cust1EDITEd@mail.com",
            'tax_id' => '9877645',
        ) 
    );

    execute($url, $payload, $action);
}
function execute($url, $payload, $action, $pretty = true){ 
    
    $payload  = json_encode($payload);
        
    $auth = hash_hmac('sha256', $url, SECRET_KEY);

    $header = array(
        'Content-Type: application/json',  
        'Authorization: '.$auth
    );

    $connection = curl_init(); 
 
    if ($action <> 'GET') 
        curl_setopt($connection, CURLOPT_POSTFIELDS, $payload);
    
    curl_setopt($connection, CURLOPT_URL, $url); 
    curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0);  
    curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($connection, CURLOPT_HTTPHEADER,$header); 
    curl_setopt($connection, CURLOPT_CUSTOMREQUEST, $action);

    $response = curl_exec($connection);
    
    curl_close($connection);
    
    if($pretty){ 
        $response = json_decode($response,true);
        echo '<pre>';
        print_r($response);
        echo '</pre>';
    }else{ 
         echo $response; 
    }

    return $response;
}

?>