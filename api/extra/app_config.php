<?php

define("DATABASE_NAME", "nchavez_qubmessenger");
define("DATABASE_HOST", "localhost");
define("DATABASE_USERNAME", "qub_admin");
define("DATABASE_PASSWORD", "Hum@nT0rch");

define("USERS_TABLE_NAME", "users");
define("CONTACTS_TABLE_NAME", "contacts");
define("MESSAGES_TABLE_NAME", "messages");
define("IMAGE_COLLECTIONS_TABLE_NAME", "image_collections");
define("API_KEYS_TABLE_NAME", "api_keys");
define("DEBUG_MODE", true);

define ("BLOWFISH_SALT", '$2y$07$zbST1Hc51Vu1DBnoeraUYS');
define ("STRING_SPLIT_FACTOR", 50);
  //raw private api key: 21y0urMom5Chezt1HaYYrr8

define("SITE_ROOT", "http://localhost/qub_messenger/api/");
define("SITE_ROOT_SECURE", "https://localhost/qub_messenger/api/");
define("HOST_WWW_ROOT", "C:/Users/Nick/Dropbox/Servers/QubMessenger/");

define("HOST_OS", /*"LINUX"*/"WINDOWS");

function debug_print($message){
  if(DEBUG_MODE){
    echo $message;
  }
}

function spit_json($obj){
   echo json_encode($obj);
   exit();
}

function handle_bad_request($error_message){
  header('HTTP/1.1 400 Bad Request');
  header('Content-Type: application/json');
  echo json_encode(array("Bad Request: ".$error_message));
  exit();
}

function handle_server_error($error_message){
  header('HTTP/1.1 500 Internal Server Error');
  header('Content-Type: application/json');
  echo json_encode(array("Server Error: ".$error_message));
  exit();
}

function handle_unauthorized_request($error_message, $other_data=""){
  header('HTTP/1.1 401 Unauthorized');
  header('Content-Type: application/json');
  echo json_encode(array("Unauthorized: ".$error_message, $other_data));
  exit();
}

function handle_not_found($error_message){
  header('HTTP/1.1 404 Not Found');
  header('Content-Type: application/json');
  echo json_encode(array("Not Found: ".$error_message));
  exit();
}

function is_ver_int($v){
  return ctype_digit($v) || is_int($v);
}

function is_contact($user_id, $contact_id){
  connect_to_database();
  $query = sprintf("SELECT status FROM %s WHERE (first_user_id=%d AND second_user_id=%d) OR (first_user_id=%d AND second_user_id=%d)",
    CONTACTS_TABLE_NAME, $user_id, $contact_id, $contact_id, $user_id);
  $result = mysql_query($query) or
    handle_server_error("Database currently unavailable.");
  return (mysql_num_rows($result) > 0);
}

function print_image($path, $thumb){
  if($path == null || strlen($path) == 0)
    handle_bad_request("This image does not exist.");
  if($thumb){
    $pos = strrpos($path, '/');
    $first_part = substr($path, 0, $pos);
    $last_part = substr($path, $pos);
    $path = $first_part.'/thumbs'.$last_part;
  }
  @$ans = file_get_contents(HOST_WWW_ROOT.$path);
  if($ans === false)
    handle_not_found("Cannot find requested image: <".HOST_WWW_ROOT.$path.">");
  return $ans;
}

?>