<?php
require_once(__DIR__ .'/getReviews.class.php');
require_once(__DIR__ .'/Facebook/autoload.php');

if(($config = include(__DIR__."/config.php")) === false) {
	print "Configuration file missing";
	exit;
}


// Example
$reviews = new getReviews($config);


// Print result
echo '<pre>'; print_r($reviews->get()); echo '</pre>';


/* Create XML file of the reviews
*  ======================================================================
*  $reviews->creatXML('file name');
*  =====================================================================
*/ 

//$reviews->creatXML('user_reviews');


?>