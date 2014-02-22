<?php
  require_once dirname(__FILE__)."/../../extra/app_config.php";
  require_once dirname(__FILE__)."/../../extra/database_connection.php";

  class AttributesController {

    public $format = 'application/json';

    public function get($request, $user_id){
      if(count($request->url_elements) == 3){
        return $this->get_whole_attribute($user_id, $request->url_elements[2],
          $request->parameters, $request->userID);
      }else if(count($request->url_elements) == 4){
        return $this->get_attribute_detail($user_id, $request->url_elements[2],
          $request->url_elements[3], $request->parameters, $request->userID);
      }else{
        handle_bad_request("Error Code 1000: Invalid attribute resource request.");
      }
    }

    private function get_whole_attribute($user_id, $attribute, $parameters, $requesting_user_id){
      connect_to_database();
      if($attribute == "selected_image" || $attribute == "image_collection_id" || $attribute == "num_profile_pics")
        $query = sprintf("SELECT %s FROM %s WHERE user_id=%d",
          $attribute, USERS_TABLE_NAME, $user_id);
      else
        $query = sprintf("SELECT %s, %s FROM %s WHERE user_id=%d",
          $attribute, $attribute."_visible", USERS_TABLE_NAME, $user_id);
      
      $result = mysql_query($query) or
        handle_server_error("Error Code 1001: Data currently unavailable.");
      $row = mysql_fetch_array($result) or
        handle_bad_request("Error Code 1002: Invalid user ID.");

      if($attribute == "selected_image" || $attribute == "image_collection_id" || $attribute == "num_profile_pics"){
        $attr = $row[$attribute];
      }else{
        $attr[$attribute]['visible'] = $row[$attribute.'_visible'];
        if($attr[$attribute]['visible'] || $requesting_user_id == $user_id || is_contact($user_id, $requesting_user_id))
          $attr[$attribute]['value'] = $row[$attribute];
      }
      return $attr;
    }

    private function get_attribute_detail($user_id, $attribute, $detail, $parameters, $requesting_user_id){
      if($attribute == "selected_image" || $attribute == "image_collection_id" || $attribute == "num_profile_pics")
        handle_bad_request("Error Code 1003: Invalid request.");
      if($detail != "value" && $detail != "visible")
        handle_bad_request("Error Code 1004: Invalid request.");

      connect_to_database();
      $query = sprintf("SELECT %s, %s FROM %s WHERE user_id=%d",
        $attribute, $attribute.'_visible', USERS_TABLE_NAME, $user_id);
      $result = mysql_query($query) or
        handle_server_error("Error Code 1005: Database currently unavailable.");
      $row = mysql_fetch_array($result) or
        handle_bad_request("Error Code 1006: Invalid user ID.");
      $ans = array();
      if($detail == 'visible')
        $ans[0] = $row[$attribute.'_visible'];
      else if($detail == 'value' && ($row[$attribute.'_visible'] || $requesting_user_id == $user_id || is_contact($user_id, $requesting_user_id)))
        $ans[0] = $row[$attribute];
      else
        handle_unauthorized_request("Error Code 1007: Access denied.");
      return $ans;
    }
  }
?>