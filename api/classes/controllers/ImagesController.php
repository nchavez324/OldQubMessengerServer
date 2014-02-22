<?php
  require_once dirname(__FILE__)."/../../extra/app_config.php";
  require_once dirname(__FILE__)."/../../extra/database_connection.php";
  class ImagesController {

    public $format;

    public function get($request, $user_id){

      if(count($request->url_elements) == 4){
        $prop = $request->url_elements[3];
        if($prop == 'cover_photo' ||  (is_ver_int($prop) && $prop > 0 && $prop <= 5))
          return $this->get_image_data($user_id, $request->url_elements[3], $request->parameters, $request->userID);
        else
          handle_bad_request("Error Code 1008: Image resource request invalid.");
      }else{
        handle_bad_request("Error Code 1009: Image resource request invalid.");
      }
    }

    private function get_image_data($user_id, $image_tag, $parameters, $requesting_user_id){
      connect_to_database();
      $column = $image_tag;
      if($column == 'cover_photo') $column .= "_path";
      else $column = "profile_pic_path_".$column;

      $result = mysql_query(
        sprintf("SELECT selected_image, image_collection_id FROM %s WHERE user_id=%d", USERS_TABLE_NAME, $user_id)) or
        handle_server_error("Error Code 1010: Data currently unavailable.");
      $row = mysql_fetch_array($result) or
        handle_bad_request("Error Code 1011: Invalid user ID.");
      $image_collection_id = $row['image_collection_id'];
      $selected_image = $row['selected_image'];
      if(!is_ver_int($image_collection_id) || $image_collection_id <= 0)
        handle_bad_request("Error Code 1012: No image data available.");

      if(is_ver_int($image_tag)){
        if($selected_image == null && $user_id != $requesting_user_id){
          handle_unauthorized_request("Error Code 1013: Access to profile picture denied.");
        }
        if($selected_image != $image_tag && $user_id != $requesting_user_id){
          handle_unauthorized_request("Error Code 1014: Access to profile picture denied.");
        }
      }

      $query = sprintf("SELECT %s FROM %s WHERE image_collection_id=%d",
        $column, IMAGE_COLLECTIONS_TABLE_NAME, $image_collection_id);
      $result = mysql_query($query) or
        handle_server_error("Error Code 1015: Image metadata currently unavailable.");
      $row = mysql_fetch_array($result);
      
      $thumb = false;
      if(isset($parameters['thumb']))
        $thumb = ($parameters['thumb'] == 'YES');
      $data = print_image($row[$column], $thumb);
      $this->format = 'image/jpeg';
      return $data;
    }
  }
?>