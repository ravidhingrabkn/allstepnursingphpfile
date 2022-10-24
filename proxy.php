<?php

// Allow CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");

// Fetch .env variables
$env = file(__DIR__.'/.env.sample', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

foreach($env as $value)
{
  $value = explode('=', $value);  
  define($value[0], $value[1]);
}

// Fetch POST values from an ajax request
if (file_get_contents('php://input'))
{
    $_REQUEST = array_merge($_REQUEST, json_decode(file_get_contents('php://input'),true));
}

// Manually set additional request values
$_REQUEST['oid'] = OID;
$_REQUEST['retURL'] = 'https://merry-cucurucho-9b5501.netlify.app/Contact';

// Convert request variables to a string
$fields_string = '';

foreach( $_REQUEST as $key=>$value ){
  if( isset($_REQUEST[$key]) and !empty($_REQUEST[$key]))
  {
    $fields_string .= $key.'='.$value.'&'; 
  }
}

rtrim( $fields_string, '&' );

// Post data to the WebToLead
$url = 'https://webto.salesforce.com/servlet/servlet.WebToLead?encoding=UTF-8';

$curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST,count($_REQUEST));
    curl_setopt($curl, CURLOPT_POSTFIELDS, $fields_string);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

$response = curl_exec($curl);
curl_close($curl);

// print_r($response);

echo json_encode(array('status' => 'success'));