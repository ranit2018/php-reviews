<?php

/*
* ===============================================================
* Class name: Get Reviews;
* Description: To get reviews from google and facebook of a page;
* ================================================================
*/

class get_reviews {


	/*
	* ===========================
	* DECLARING GLOBAL PROPERTIES
	* ===========================
	*/ 
	
	var $fb;
	var $app_id;
	var $app_secret;
	var $default_graph_version;
	var $default_access_token;
	var $profileAccessToken;
	var $fbResponse;
	var $fb_reviews;
	var $googlePlaceID;
	var $googleAPI;
	var $allFBreviews = array();
	var $allGOOGLEreviews = array();
	var $getAllReviews = array();
	var $shuffleEnable = false;


	function __construct($config) {
		
		date_default_timezone_set($config['default_timezone']);	
		
		// Facebook config
		if($config['fb_enable']) {
			$this->setFacebookAccess(	$config['fb_enable'],
										$config['fb_app_id'],
										$config['fb_app_secret'],
										$config['fb_page_id'],
										$config['fb_api_version'],
										$config['fb_page_accesstoken'],
										$config['fb_profile_accesstoken']
									);
		}
		// Google config
		if($config['google_enable']) {
			$this->setGoogle(	$config['google_enable'],
								$config['google_placeid'],
								$config['google_apikey']
							);
		}	
		// Shuffle
		$this->shuffle($config['review_shuffle']);
	}



	/*
	* =========================
	* Setting the autoload file
	* =========================
	*/ 

	public function setAutoloadLink($linkPath){

		if(file_exists($linkPath))
			require_once $linkPath ; 
		else
			die('Autoload file not found!');
		
	}

	/*
	* ===================
	* FOR FACEBOOK REVIEWS
	* ====================
	*/ 


	public function setFacebookAccess($isEnabled, $app_id , $app_secret, $pageID, $default_graph_version, $default_access_token = null, $profile_access_token) {
		$this->app_id = $app_id;
		$this->app_secret = $app_secret;
		$this->pageID = $pageID;
		$this->default_graph_version = $default_graph_version;
		$this->profileAccessToken = $profile_access_token;
		if($default_access_token == null){
			$this->default_access_token = $this->createToken();			
		}else{
			$this->default_access_token = $default_access_token;
		}
			
			if($isEnabled){
				$this->setFb();	
				$this->facebookEnable = $isEnabled;	
			}
	}

	/*
	* ====================================================
	* THIS IS FOR CREATE PAGE TOKEN BASED ON PROFILE TOKEN
	* ====================================================
	*/ 	

	private function createToken(){
		$fb = new \Facebook\Facebook([
		  'app_id' => $this->app_id,
		  'app_secret' => $this->app_secret,
		  'default_graph_version' => $this->default_graph_version,
		  'default_access_token' => $this->profileAccessToken
		]);
		try {

		  $response = $fb->get(
		    '/'.$this->pageID.'?fields=access_token'
		  );
		  $graphEdge = $response->getGraphNode();

		 return $graphEdge['access_token'];

		} catch(FacebookExceptionsFacebookResponseException $e) {
		  echo 'Graph returned an error: ' . $e->getMessage();
		  exit;
		} catch(FacebookExceptionsFacebookSDKException $e) {
		  echo 'Facebook SDK returned an error: ' . $e->getMessage();
		  exit;
		}
	}

	private function setFb(){

		// setting the facebook

		$fb = new \Facebook\Facebook([
		  'app_id' => $this->app_id,
		  'app_secret' => $this->app_secret,
		  'default_graph_version' => $this->default_graph_version,
		  'default_access_token' => $this->default_access_token
		]);

		$this->getFacebookResponse($fb);

	}

	private  function getFacebookResponse($fb){
		 
		try {
		  // Returns a `FacebookFacebookResponse` object
		  $fbResponse = $fb->get('/'.$this->pageID.'/ratings' );

		  

		} catch(FacebookExceptionsFacebookResponseException $e) {
		  return 'Graph returned an error: ' . $e->getMessage();
		  exit;
		} catch(FacebookExceptionsFacebookSDKException $e) {
		  return 'Facebook SDK returned an error: ' . $e->getMessage();
		  exit;
		}
		$this->fb_reviews = $fbResponse->getGraphEdge();

		foreach ($this->fb_reviews as $graphNode) {

			if(isset($graphNode['review_text'])) { $rev_txt = $graphNode['review_text']; }else{ $rev_txt =''; };
			
			 
			 $this->allFBreviews[] =  array(
			 							'name'=>$graphNode['reviewer']['name'],
			 							'id'=>'fb-'.$graphNode['reviewer']['id'],
			 							'rating'=>$graphNode['rating'],
			 							'review_text'=> $rev_txt,
			 							'imgprofilebase64'=> $this->makeBase64('http://graph.facebook.com/'.$graphNode['reviewer']['id'].'/picture')
			 						 );
			 $this->getAllReviews[] =  array(
			 							'name'=>$graphNode['reviewer']['name'],
			 							'id'=>'fb-'.$graphNode['reviewer']['id'],
			 							'rating'=>$graphNode['rating'],
			 							'review_text'=> $rev_txt,
			 							'imgprofilebase64'=> $this->makeBase64('http://graph.facebook.com/'.$graphNode['reviewer']['id'].'/picture')
			 						 );		
			
		 
		}
		
		
	}

	

	public function getAllFbReviews(){

		if($this->allFBreviews)
			return $this->allFBreviews;
		else
			return 'Facebook Review disabled!';
		
	}

	/*
	* ===================
	* FOR GOOGLE REVIEWS
	* ====================
	*/ 


	public function setGoogle($isEnabled, $placeID, $apiKey){
		if($isEnabled){				
			$this->googleEnable = $isEnabled;	
			$this->googlePlaceID = $placeID;
			$this->googleAPI = $apiKey;			
			$this->googleInit();
		}
	}

	private function googleInit(){
		$ch = curl_init();
		$url = 'https://maps.googleapis.com/maps/api/place/details/json?placeid='. $this->googlePlaceID .'&key='.$this->googleAPI;		
	  	curl_setopt($ch, CURLOPT_URL,$url);
	 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);	  
	  	$response = curl_exec($ch);
	  	$results = json_decode($response, true);	  	  	
	  	if(isset($results['result'])) {	
		  	foreach ($results['result']['reviews'] as $result) {		
				 
				 $this->allGOOGLEreviews[] =  array(
				 							'name'=>$result['author_name'],
				 							'auth_URI'=>$result['author_url'],
				 							'rating'=>$result['rating'],
				 							'review_text'=> $result['text'],
				 							'imgprofilebase64'=> $this->makeBase64($result['profile_photo_url'])
				 						 );	
				$this->getAllReviews[] =  array(
				 							'name'=>$result['author_name'],
				 							'auth_URI'=>$result['author_url'],
				 							'rating'=>$result['rating'],
				 							'review_text'=> $result['text'],
				 							'imgprofilebase64'=> $this->makeBase64($result['profile_photo_url'])
				 						 );	 
			}			
		 
		}
	}

	private function makeBase64($url){
		$data = file_get_contents($url);
		$type = pathinfo($url, PATHINFO_EXTENSION);
		$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
		return $base64;
	}

	public function getAllGOOGLEReviews(){

		if($this->allGOOGLEreviews)
			return $this->allGOOGLEreviews;
		else
			return 'Google review disabled /  Api limit exceded!';
		
	}


	/*
	* ===================
	* TO SHOW ALL REVIEWS
	* ====================
	*/ 

	public function showAllReviews(){

		 

		//$this->getAllReviews['Total_reviews'] = count($this->getAllReviews) ;

		if($this->getAllReviews){

			if($this->shuffleEnable){

				shuffle($this->getAllReviews);	
							
			}		

			$this->getAllReviews = array('Total_reviews'=> count($this->getAllReviews))+$this->getAllReviews;	

			return $this->getAllReviews;			
			
		}
		else{
			return 'No reviews!';
		}
	}


	/*
	* ===================
	* TO CREATE THE XML FILE
	* ====================
	*/ 
	

	public function creatXML($fileName){	

		
		//creating object of SimpleXMLElement
		$xml_user_info = new SimpleXMLElement("<?xml version=\"1.0\"?><user_reviews></user_reviews>");
		$this->array_to_xml($this->getAllReviews,$xml_user_info);

		//function call to convert array to xml
		
		$xml_file = $xml_user_info->asXML($fileName.'.xml');
	}	

	private function array_to_xml($array, $xml_user_info) {
	    foreach($array as $key => $value) {
	    	
	        if(is_array($value)) {
	            if(!is_numeric($key)){
	                $subnode = $xml_user_info->addChild("$key");
	                $this->array_to_xml($value, $subnode);
	            }else{
	                $subnode = $xml_user_info->addChild("item$key");
	                $this->array_to_xml($value, $subnode);
	            }
	        }	        
	        else {
	            $xml_user_info->addChild("$key",htmlspecialchars("$value"));
	        }
	    }
	} 

	/*
	* ===================
	* Enable or disable shuffle
	* ====================
	*/


	public function shuffle($s){
		if($s){
			$this->shuffleEnable = true;
		}else{
			$this->shuffleEnable = false;
		}		 

	}	



} 
