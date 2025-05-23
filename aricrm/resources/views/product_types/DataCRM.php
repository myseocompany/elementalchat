<?php

namespace App\MySEO;

	class DataCRM{
	public $url_login = "https://develop.datacrm.la/datacrm/api/webservice.php?operation=getchallenge&username=username";
	public $token = "";
	public $user = "diseno@lamilagrosa.com";
	public $password = "DISEÑO";

	public function doLogin(){
		$data = array(
		    'user' => $this->user,
		    'password' => $this->password
		    );
		$this->token = $this->makeRequest("POST", $this->url_login, $data);
	}


	function makeRequest($method, $url, $data){
		/*
		$method = 'GET'; //change to 'POST' for post method
		$url = 'http://localhost/browse/';
		$data = array(
		    'manufacturer' => 'kraft',
		    'packaging_type' => 'bag'
		    );
		*/
		if ($method == 'POST'){
		//Make POST request
		    $data = http_build_query($data);
		    $context = stream_context_create(array(
		        'http' => array(
		            'method' => "$method",
		            'header' => 'Content-Type: application/x-www-form-urlencoded',
		            'content' => $data)
		        )
		    );
		    $response = file_get_contents($url, false, $context);
		}
		else {
		// Make GET request
		    $data = http_build_query($data, '', '&');
		    $response = file_get_contents($url."?".$data, false);
		}
		return $response;
	}
}
?>