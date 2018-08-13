<?php
require_once('class-get-reviews.php');

$reviews = new get_reviews();

/* SET autoload file for facebook client
*  ======================================================================
*  $reviews->setAutoloadLink('Path of the autoload.php file');
*  =====================================================================
*/ 

$reviews->setAutoloadLink(__DIR__ .'/Facebook/autoload.php');


/* 
*	Need to set the credentials for facebook
*	============================================================================================================================
*	$reviews->setFacebookAccess( 
*			enable/disable reviews, 
*			'app id', 
*			'app secret', 
*			'page id', 
*			'api version', 
*			'page access token ', 
*			'profile access token'); 
*	============================================================================================================================
* 	Note:: If you dont have page access token, leave the parameter blank. And put Your profile Access token. If you are adding both,
* 	the Profile token will be 	ignored!
*/
$reviews->setFacebookAccess(
	true, // enable facebook reviews
	'', //app id
	'', //app secret
	'', //page id
	'v2.10', // api version,	
	'', // page access token  ,
	'' // profile Access token
);

/* To get all Facebook Reviews
*  ======================================================================
*  $reviews->getAllFbReviews();
*  =====================================================================
*/ 
echo '<pre>';
//print_r($reviews->getAllFbReviews() );
echo '</pre>';


/* SET google credentials
*  ======================================================================
*  $reviews->setGoogle( 
*			enable/disable reviews, 
*			'PLACE ID', 
*			'API KEY' );
*  =====================================================================
*/ 

$reviews->setGoogle( true, '', '' );

/* To get all Google Reviews
*  ======================================================================
*  $reviews->getAllGOOGLEReviews();
*  =====================================================================
*/ 

echo '<pre>';
//print_r($reviews->getAllGOOGLEReviews());
echo '</pre>';

/* To get all Reviews 
*  ======================================================================
*  $reviews->showAllReviews();
*  =====================================================================
*/ 

echo '<pre>';
print_r($reviews->showAllReviews());
echo '</pre>';

/* Create XML file of the reviews
*  ======================================================================
*  $reviews->creatXML('file name');
*  =====================================================================
*/ 

$reviews->creatXML('user_reviews');


?>