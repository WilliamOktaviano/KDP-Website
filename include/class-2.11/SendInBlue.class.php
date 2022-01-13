<?php
class SendInBlue extends BaseClass{
 
    function __construct(){ 
       $this->secretKey = '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';
	}

	function sendMail($sender,$subject,$to,$content){
        
        $payload = array(); 
        $payload['sender'] = $sender;
        $payload['to'] = $to; 
        $payload['subject'] = $subject;
        $payload['htmlContent'] = $content;
        $payload['textContent'] = $content;
        
        
        $this->execute('smtp/email', 'POST', $payload);
        //$this->execute('emailCampaigns', 'POST', $payload);
    }
    
    function createContact($folderId,$email,$name=array()){
    
        $payload = array();
        $payload['email'] = $email;
        $payload['listIds'] = array(intval($folderId)); // nanti harus diupdate kalo ad yg kirim dlm bentuk array
        
        if(!empty($name))
            $payload['attributes'] = array('FNAME' => $name['firstName'], 'LNAME' =>   $name['lastName']);
        
        
        $this->execute('contacts', 'POST', $payload); 
    }
    
    function createLists($name,$folderId){
        $payload = array();
        $payload['name'] = $name;
        $payload['folderId'] = $folderId;
        
        $this->execute('contacts/lists', 'POST', $payload); 
    }
    
     function execute($url,$action='GET',$payload = ''){
         
        $payload = json_encode($payload); 
        
        $baseurl = 'https://api.sendinblue.com/v3/';
        $url = $baseurl.$url;
        
        $header = array(
            'content-Type: application/json',  
            'accept: application/json',
            'api-key: '. $this->secretKey
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
        //$this->setLog($response,true);
        curl_close($connection);   
        return json_decode($response,true);

    }

}
		
?>