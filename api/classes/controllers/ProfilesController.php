<?php

require_once dirname(__FILE__)."/../../extra/app_config.php";
require_once dirname(__FILE__)."/../../extra/database_connection.php";
require_once "ImagesController.php";
require_once "MessagesController.php";
require_once "ContactsController.php";
require_once "AttributesController.php";

class ProfilesController {

  protected $format = 'application/json';

  private function get_user_id($request){
    $user_id = $request->url_elements[1];
      if(is_ver_int($user_id) && $user_id > 0)
        return (int)$user_id;
      else
        handle_bad_request("Error Code 1030: Invalid user ID.");
  }

  public function post($request){
    return array("Hello!", $request->get_data());
  }

  public function get($request) {
    if(count($request->url_elements) <= 1) handle_bad_request("Error Code 1031: Invalid resource request.");

    if(count($request->url_elements) == 2)
      if(is_ver_int($request->url_elements[1]))
        return $this->read_profile_data_by_user_id($this->get_user_id($request), $request->parameters, $request->userID);
      else
        return $this->read_profile_data_by_username($request->url_elements[1], $request->parameters, $request->userID);

    $controllerType = $request->url_elements[2];
    $attributes = array("username", "name", "age", "sex", "seeking", "location", "image_collection_id", "selected_image", "num_profile_pics");
    if($controllerType == "images"){
      $imagesController = new ImagesController();
      $res = $imagesController->get($request, $this->get_user_id($request));
      $this->format = $imagesController->format;
      return $res; 
    }else if($controllerType == "messages"){
      $messagesController = new MessagesController();
      $res = $messagesController->get($request, $this->get_user_id($request));
      $this->format = $messagesController->format;
      return $res;
    }else if($controllerType == "contacts"){
      $contactsController = new ContactsController();
      $res = $contactsController->get($request, $this->get_user_id($request));
      $this->format = $contactsController->format;
      return $res;
    }else if(in_array($controllerType, $attributes)){
      $attributesController = new AttributesController();
      $res = $attributesController->get($request, $this->get_user_id($request));
      $this->format = $attributesController->format;
      return $res;
    }else{
      handle_bad_request("Error Code 1032: Resource does not exist -- invalid request.");
    }
  }
  
  private function read_profile_data_by_user_id($user_id, $parameters, $requesting_user_id){
    connect_to_database();
    $profile_query = sprintf("SELECT * FROM %s WHERE user_id=%d",
      USERS_TABLE_NAME, $user_id);
    $result = mysql_query($profile_query) or
      handle_server_error("Error Code 1033: Data currently unavailable.");
    $row = mysql_fetch_array($result) or
      handle_bad_request("Error Code 1034: Invalid user ID.");
    $vars = array('name','username','age','sex','seeking', 'location');
    foreach ($vars as $v) {
      $new_data[$v]['visible'] = $row[$v."_visible"];
      if($new_data[$v]['visible'] || $user_id == $requesting_user_id || is_contact($user_id, $requesting_user_id))
        $new_data[$v]['value'] = $row[$v];
    }
    $new_data['user_id'] = $row['user_id'];
    $new_data['num_profile_pics'] = $row['num_profile_pics'];
    $new_data['selected_image'] = $row['selected_image'];
    $new_data['image_collection_id'] = $row['image_collection_id'];

    return $new_data;
  }

  private function read_profile_data_by_username($username, $parameters, $requesting_user_id){
    connect_to_database();
    $profile_query = sprintf("SELECT * FROM %s WHERE username='%s'",
      USERS_TABLE_NAME, $username);
    $result = mysql_query($profile_query) or
      handle_server_error("Error Code 1035: Data currently unavailable.");
    $row = mysql_fetch_array($result) or
      handle_bad_request("Error Code 1036: Invalid user ID.");
    $vars = array('name','username','age','sex','seeking', 'location');
    foreach ($vars as $v) {
      $new_data[$v]['visible'] = $row[$v."_visible"];
      if($new_data[$v]['visible'] || $row['user_id'] == $requesting_user_id || is_contact($row['user_id'], $requesting_user_id))
        $new_data[$v]['value'] = $row[$v];
    }
    $new_data['user_id'] = $row['user_id'];
    $new_data['num_profile_pics'] = $row['num_profile_pics'];
    $new_data['selected_image'] = $row['selected_image'];
    $new_data['image_collection_id'] = $row['image_collection_id'];
    return $new_data;
  }

  public function get_content_type(){
    return $this->format;
  }
}
?>
