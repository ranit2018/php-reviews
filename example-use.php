<?php
require_once(__DIR__ .'/class-get-reviews.php');
require_once(__DIR__ .'/Facebook/autoload.php');

if(($config = include(__DIR__."/config.php")) === false) {
	print "Configuration file missing";
	exit;
}



$reviews = new get_reviews($config);







/* To get all Facebook Reviews
*  ======================================================================
*  $reviews->getAllFbReviews();
*  =====================================================================
*/ 
echo '<pre>';
//print_r($reviews->getAllFbReviews() );
echo '</pre>';



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