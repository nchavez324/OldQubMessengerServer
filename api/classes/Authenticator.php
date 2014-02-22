<?php

require_once dirname(__FILE__)."/../extra/app_config.php";
require_once dirname(__FILE__)."/../extra/database_connection.php";

class Authenticator {
  public function is_authentic($request){
    if(isset($request->parameters['hash'])) $client_hash = $request->parameters['hash'];
    else return false;
    $prefix = substr(BLOWFISH_SALT, 0, strlen(BLOWFISH_SALT)-1);
    $server_hash = get_server_hash($request);
    //return true;
    if(!$server_hash) return false;
    return ($server_hash == $client_hash);
  }

  public function remove_auth_credentials($request){
    $new_data = array();
    $auth_credentials = array('public_api_key','username','timestamp','hash');
    foreach ($request->parameters as $key => $value) {
      if(!in_array($key, $auth_credentials))
        $new_data[$key] = $value;
    }
    $request->parameters = $new_data;
    return $request;
  }
}


function collapse_dict($dict){
  $string = "";
  foreach ($dict as $key => $value) {
    $string.=($key.":".$value.",");
  }
  return $string;
}

function split_string($string, $factor){
  $ans = array();
  $i = 0;
  while(strlen($string) > 0){
    $piece = substr($string, 0, $factor);
    $string = substr($string, $factor);
    $ans[$i] = $piece;
    $i++;
  }
  return $ans;
}

function get_server_hash($request){
  if(isset($request->method)) $dict['HTTP_method'] = $request->method;
  else return false;
  if(!isset($request->parameters['username']) && !isset($request->parameters['public_api_key']) && !isset($request->parameters['timestamp']))
      return false;
  $dict['username'] = $request->parameters['username'];
  $dict['public_api_key'] = $request->parameters['public_api_key'];
  $dict['timestamp'] = $request->parameters['timestamp'];
  
  foreach ($request->parameters as $key => $value) {
    if($key != 'hash')
      $dict[$key] = $value;
  }
 foreach ($request->url_elements as $key => $value) {
    $dict[$key.''] = $value.'';
  }

  connect_to_database();
  $query = sprintf("SELECT user_id, password_hash FROM %s WHERE username='%s'", USERS_TABLE_NAME, $dict['username']);
  $result = mysql_query($query);

  if(!$result) return false;
  $row = mysql_fetch_array($result);
  if(!$row) return false;
  if(!isset($row['password_hash'])) return false;
  $dict['password_hash'] = $row['password_hash'];

  $request->userID = $row['user_id'];

  $query = sprintf("SELECT private_api_key_hash FROM %s WHERE public_api_key='%s'", API_KEYS_TABLE_NAME, $dict['public_api_key']);
  $result = mysql_query($query);
  if(!$result) return false;
  $row = mysql_fetch_array($result);
  if(!$row) return false;
  if(!isset($row['private_api_key_hash'])) return false;
  $dict['private_api_key_hash'] = $row['private_api_key_hash'];

  //var_dump($dict);

  $collapsed = collapse_dict($dict);
  $to_hash = split_string($collapsed, STRING_SPLIT_FACTOR);

  $hashed_arr = array();
  foreach ($to_hash as $key => $value) {
    $c = crypt($value, BLOWFISH_SALT);
    $hashed_arr[$key] = substr($c, strlen(BLOWFISH_SALT)-1);
  }
  $server_hash = implode("", $hashed_arr);

  return $server_hash;
}
?>