<?php

// get google reviews


  // $ch = curl_init();
  // curl_setopt($ch, CURLOPT_URL,'https://maps.googleapis.com/maps/api/place/details/json?placeid=ChIJN1t_tDeuEmsRUsoyG83frY4&key=AIzaSyDpsUgS81eQQzpFytkp_DAXvT_i8zvvZcg');
  // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  // //curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
  // $response = curl_exec($ch);
  // $results = json_decode($response, true);
  // echo '<pre>';
  // print_r($results['result']['reviews']);

$users_array = array(
    "total_users" => 3,
    "users" => array(
        array(
            "id" => 1,
            "name" => "Smith",
            "address" => array(
                "country" => "United Kingdom",
                "city" => "London",
                "zip" => 56789,
            )
        ),
        array(
            "id" => 2,
            "name" => "John",
            "address" => array(
                "country" => "USA",
                "city" => "Newyork",
                "zip" => "NY1234",
            ) 
        ),
        array(
            "id" => 3,
            "name" => "Viktor",
            "address" => array(
                "country" => "Australia",
                "city" => "Sydney",
                "zip" => 123456,
            ) 
        ),
    )
);

//function defination to convert array to xml
function array_to_xml($array, &$xml_user_info) {
    foreach($array as $key => $value) {
        if(is_array($value)) {
            if(!is_numeric($key)){
                $subnode = $xml_user_info->addChild("$key");
                array_to_xml($value, $subnode);
            }else{
                $subnode = $xml_user_info->addChild("item$key");
                array_to_xml($value, $subnode);
            }
        }else {
            $xml_user_info->addChild("$key",htmlspecialchars("$value"));
        }
    }
}

//creating object of SimpleXMLElement
$xml_user_info = new SimpleXMLElement("<?xml version=\"1.0\"?><user_info></user_info>");

//function call to convert array to xml
array_to_xml($users_array,$xml_user_info);

//saving generated xml file


$xml_file = $xml_user_info->asXML('users.xml');

//success and error message based on xml creation
if($xml_file){
    
    echo 'XML file have been generated successfully.';
}else{
    echo 'XML file generation error.';
}

file_put_contents("cache_file", serialize($myArray));

  