<?php

require_once __DIR__ . '/autoload.php'; // change path as needed

$fb = new \Facebook\Facebook([
  'app_id' => '879436418898130',
  'app_secret' => 'aff92d87c192cb5e4654d6cd6b6419f3',
  'default_graph_version' => 'v2.10',
  'default_access_token' => 'EAAMf179ZA2NIBAGUZBPN0C6c8EeZCFVjaXy3eAAc2IMUEKD6eJUvRVB8oRAZAjfpY9ZAP5OfpOmAjCTIZA6IrwoEeTU2fY5AJSDi0wXzyAiEWxg6qlHuvHYyvkWKec7frwcMfzl5YNVJEWhc3U0t4cj6vS3Hsm1xOpSeFvglU2KmCFyksmySoZCP3Bv4xV3pVve0kWlWWZB2qb8gJaBawOKb', // optional
]);
 
  $accessToken = 'EAAMf179ZA2NIBAGUZBPN0C6c8EeZCFVjaXy3eAAc2IMUEKD6eJUvRVB8oRAZAjfpY9ZAP5OfpOmAjCTIZA6IrwoEeTU2fY5AJSDi0wXzyAiEWxg6qlHuvHYyvkWKec7frwcMfzl5YNVJEWhc3U0t4cj6vS3Hsm1xOpSeFvglU2KmCFyksmySoZCP3Bv4xV3pVve0kWlWWZB2qb8gJaBawOKb';

try {
  // Returns a `FacebookFacebookResponse` object
  $response = $fb->get(
    '/320024731367272/ratings'
  );
} catch(FacebookExceptionsFacebookResponseException $e) {
  echo 'Graph returned an error: ' . $e->getMessage();
  exit;
} catch(FacebookExceptionsFacebookSDKException $e) {
  echo 'Facebook SDK returned an error: ' . $e->getMessage();
  exit;
}
//$graphNode = $response->getGraphEdge(); //getGraphEdge
$graphEdge = $response->getGraphEdge();
foreach ($graphEdge as $graphNode) {
  echo '<pre>';
  //print_r($graphNode);
  echo '<hr>';
  print_r($graphNode['reviewer']['name']); echo '<br>';
  print_r($graphNode['rating']);echo '<br>';
  if(isset($graphNode['review_text'])){
     print_r($graphNode['review_text']);echo '<br>';
  } 
  echo '</pre>';
 
}


?>

