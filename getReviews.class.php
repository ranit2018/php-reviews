<?php

class review {
	public $id;
	public $source;	// google/facebook
	
	public $profile_id;
	public $profile_name;
	public $profile_url;
	public $profile_imgbase64;
	
	public $review_text;
	public $review_url;
	public $review_rating;
	public $review_timestamp;
	
	public $data;
	
	public function set($key, $value) {
		switch($key) {
			case "review_date":
			$this->review_timestamp = strtotime($value);
			break;

			case "review_timestamp":
			$this->review_timestamp = intval($value);
			break;			
		}
	}
}

/*
* ===============================================================
* Class name: Get Reviews;
* Description: To get reviews from google and facebook of a page;
* ================================================================
*/

class getReviews {
	
	var $fb_enable = false;
	var $google_enable = false;
	
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
	
	var $shuffle = false;
	var $reviews = array();
	


	function __construct($config) {
		
		date_default_timezone_set($config['default_timezone']);	
		
		// Facebook config
		$this->fb_enable = (isset($config['fb_enable']) and $config['fb_enable']) ? true : false;
		if($this->fb_enable) {
			$this->setFacebookAccess(	$config['fb_app_id'],
										$config['fb_app_secret'],
										$config['fb_page_id'],
										$config['fb_api_version'],
										$config['fb_page_accesstoken'],
										$config['fb_profile_accesstoken']
									);
		}
		// Google config
		$this->google_enable = (isset($config['google_enable']) and $config['google_enable']) ? true : false;
		if($this->google_enable) {
			$this->setGoogle(	$config['google_placeid'],
								$config['google_apikey']
							);
		}	
		// Shuffle
		$this->shuffle = (isset($config['review_shuffle']) and $config['review_shuffle'] === true) ? true : false;
	}

	

	
	/*
	* ===================
	* FOR FACEBOOK REVIEWS
	* ====================
	*/ 
	
	public function setFacebookAccess($app_id , $app_secret, $pageID, $default_graph_version, $default_access_token = null, $profile_access_token = null) {
		$this->app_id = $app_id;
		$this->app_secret = $app_secret;
		$this->pageID = $pageID;
		$this->default_graph_version = $default_graph_version;
		$this->profileAccessToken = $profile_access_token;
		if($default_access_token == null){
			if(($this->default_access_token = $this->facebook_createToken()) === false) {
				trigger_error("Error creating facebook token");
				return false;
			}
		}else{
			$this->default_access_token = $default_access_token;
		}
			
	}

	/*
	* ====================================================
	* THIS IS FOR CREATE PAGE TOKEN BASED ON PROFILE TOKEN
	* ====================================================
	*/ 	
	private function facebook_createToken(){
		$fb = new \Facebook\Facebook([
		  'app_id' => $this->app_id,
		  'app_secret' => $this->app_secret,
		  'default_graph_version' => $this->default_graph_version,
		  'default_access_token' => $this->profileAccessToken
		]);
		try {

			$response = $fb->get('/'.$this->pageID.'?fields=access_token');
			$graphEdge = $response->getGraphNode();
			return $graphEdge['access_token'];

		} catch(FacebookExceptionsFacebookResponseException $e) {
		  trigger_error('Graph returned an error: ' . $e->getMessage());
		  return false;
		} catch(FacebookExceptionsFacebookSDKException $e) {
		  trigger_error('Facebook SDK returned an error: ' . $e->getMessage());
		  return false;
		}
	}

	private function fbLoad(){

		// setting the facebook
		$fb = new \Facebook\Facebook([
			'app_id' => $this->app_id,
			'app_secret' => $this->app_secret,
			'default_graph_version' => $this->default_graph_version,
			'default_access_token' => $this->default_access_token
		]);

		try {
			// Returns a `FacebookFacebookResponse` object
			$fbResponse = $fb->get('/'.$this->pageID.'/ratings' );
		} catch(FacebookExceptionsFacebookResponseException $e) {
			trigger_error('Graph returned an error: ' . $e->getMessage());
			return false;
		} catch(FacebookExceptionsFacebookSDKException $e) {
			trigger_error('Facebook SDK returned an error: ' . $e->getMessage());
			return false;
		}

		foreach($fbResponse->getGraphEdge() as $item) {
			
			$review = new review();
			$review->source = "facebook";
			$review->profile_id = $item['reviewer']['id'];
			$review->profile_name = $item['reviewer']['name'];
			$review->profile_imgbase64 = $this->makeBase64('http://graph.facebook.com/'.$item['reviewer']['id'].'/picture');
			$review->review_rating = $item['rating'];
			$review->review_text = isset($item['review_text']) ? $item['review_text'] : null;
			//$review->set("review_date", $item->created_time->date);
			$review->data = $item;	// for debug 
			
			$this->reviews[] = $review;
			unset($review);

		}

	}


	/*
	* ===================
	* FOR GOOGLE REVIEWS
	* ====================
	*/ 
	public function setGoogle($placeID, $apiKey){	
		$this->googlePlaceID = $placeID;
		$this->googleAPI = $apiKey;			
	}

	private function googleLoad(){
				
		$url = 'https://maps.googleapis.com/maps/api/place/details/json?placeid='. $this->googlePlaceID .'&key='.$this->googleAPI;	

		$ch = curl_init();		
	  	curl_setopt($ch, CURLOPT_URL,$url);
	 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);	  
	  	$response = curl_exec($ch);
		
	  	$res = json_decode($response, true);	

	  	if(!isset($res['result'])) {
			trigger_error("Errore getting result from Google: ".$res['status']);
			return false;
		}
		
		foreach((array) $res['result']['reviews'] as $item) {		
			
			$review = new review();
			$review->source = "google";
			$review->profile_name = $item['author_name'];
			$review->profile_url = $item['author_url'];
			$review->profile_imgbase64 = $this->makeBase64($item['profile_photo_url']);
			$review->review_rating = $item['rating'];
			$review->review_text = $item['text'];
			//$review->set("review_timestamp", $item['time']);
			$review->data = $item;	// for debug 
			
			$this->reviews[] = $review;
			unset($review);
		}			
		 

	}

	private function makeBase64($url){
		// NB Check if url exists
		$data = file_get_contents($url);
		$type = pathinfo($url, PATHINFO_EXTENSION);
		$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
		return $base64;
	}


	/*
	* ===================
	* TO SHOW ALL REVIEWS
	* ====================
	*/ 

	public function get(){
		if($this->google_enable) $this->googleLoad();
		if($this->fb_enable) $this->fbLoad();
		return $this->reviews;
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





} 
