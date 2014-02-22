<?php
  require_once dirname(__FILE__)."/../../extra/app_config.php";
  require_once dirname(__FILE__)."/../../extra/database_connection.php";

  class MessagesController{

    public $format = 'application/json';

    public function get($request, $user_id){
      $last_received_time = -1;
      if(isset($request->parameters['last_received_time']))
        $last_received_time = $request->parameters['last_received_time'];
      if(!is_numeric($last_received_time)) handle_bad_request("Error Code 1019: Invalid message resource request parameter.");
      
      $last_sent_time = -1;
      if(isset($request->parameters['last_sent_time']))
        $last_sent_time = $request->parameters['last_sent_time'];
      if(!is_numeric($last_sent_time)) handle_bad_request("Error Code 1020: Invalid message resource request parameter.");

      if(count($request->url_elements) == 3){
        $r = $this->get_messages_with_filter($user_id, $last_received_time, $last_sent_time, $request->parameters, $request->userID);
        return $r;

      }else if(count($request->url_elements) == 4){
        $contact_id = $request->url_elements[3];
        if(is_ver_int($contact_id) && $contact_id > 0 && $contact_id != $user_id)
          return $this->get_messages($user_id, $contact_id, $request->parameters, $request->userID);
        else
          handle_bad_request("Error Code 1021: Invalid message resource request.");
      }else if(count($request->url_elements) == 6){
        $contact_id = $request->url_elements[3];
        if(is_ver_int($contact_id) && $contact_id > 0 && $contact_id != $user_id && $request->url_elements[4]=='images' && is_numeric($request->url_elements[5]))
          return $this->get_message_image($user_id, $contact_id, $request->url_elements[5], $request->parameters, $request->userID);
        else
          handle_bad_request("Error Code 1022: Invalid message resource request.");
      }else{
        handle_bad_request("Error Code 1023: Invalid message resource request.");
      }
    }

    //profiles/1/messages?last_received_time=45&last_sent_time=50
    private function get_messages_with_filter($user_id, $last_received_time, $last_sent_time, $parameters, $requesting_user_id){
      if($user_id != $requesting_user_id)
        handle_unauthorized_request("Error Code 1024: Access to messages denied.");
      connect_to_database();
      $vals = array("from_user_id", "to_user_id", "timestamp", "content", "has_image", "status");
      $format_str = <<<EOD
SELECT %s FROM %s WHERE
(from_user_id=%d AND timestamp > %f) OR (to_user_id=%d AND timestamp > %f) OR (to_user_id=%d AND status='S') ORDER BY timestamp DESC
EOD;
      $query = sprintf($format_str, implode(',', $vals), MESSAGES_TABLE_NAME, $user_id, $last_sent_time, $user_id, $last_received_time, $user_id);
      $result = mysql_query($query) or
        handle_server_error("Error Code 1025: Data currently unavailable.".mysql_error());
      $count = 0;
      while ($row = mysql_fetch_array($result)) {
        foreach ($vals as $value) {
          $messages[$count][$value] = $row[$value];
        }
        $count++;
      }
      if($count == 0) $messages = array();
      return $messages;
    }

    //profiles/1/messages/2?last_received_time=45&last_sent_time=50
    private function get_messages_for_contact_with_filter($user_id, $contact_id, $last_received_time, $last_sent_time, $parameters, $requesting_user_id){
      if($user_id != $requesting_user_id && $contact_id != $requesting_user_id)
        handle_unauthorized_request("Error Code 1025.1: Access to messages denied.");
      connect_to_database();
      $vals = array("from_user_id", "to_user_id", "timestamp", "content", "has_image", "status");
      $format_str = <<<EOD
SELECT %s FROM %s WHERE
(from_user_id=%d AND timestamp > %f AND to_user_id=%d) OR
(to_user_id=%d AND timestamp > %f AND from_user_id=%d) OR
(to_user_id=%d AND status='S')
ORDER BY timestamp DESC
EOD;
      $query = sprintf($format_str, implode(',', $vals), MESSAGES_TABLE_NAME,
        $user_id, $last_sent_time, $contact_id,
        $user_id, $last_received_time, $contact_id,
        $user_id);
      $result = mysql_query($query) or
        handle_server_error("Error Code 1025.2: Data currently unavailable.".mysql_error());
      $count = 0;
      while ($row = mysql_fetch_array($result)) {
        foreach ($vals as $value) {
          $messages[$count][$value] = $row[$value];
        }
        $count++;
      }
      if($count == 0) $messages = array();
      return $messages;
    }

    private function get_messages($user_id, $contact_id, $parameters, $requesting_user_id){
      if($user_id != $requesting_user_id && $contact_id != $requesting_user_id)
        handle_unauthorized_request("Error Code 1026: Access to messages denied.");
      connect_to_database();
      $vals = array("from_user_id", "to_user_id", "timestamp", "content", "has_image", "status");
      $format_str = <<<EOD
SELECT %s FROM %s WHERE
(from_user_id=%d AND to_user_id=%d) OR (from_user_id=%d AND to_user_id=%d) ORDER BY timestamp DESC
EOD;
      $is_start_at = isset($parameters['start_at']) && is_ver_int($parameters['start_at']);
      $is_max_count = isset($parameters['max_count']) && is_ver_int($parameters['max_count']);
      if($is_start_at && $is_max_count)
        $format_str .= " LIMIT ".$parameters['start_at'].",".$parameters['max_count'];
      if(!$is_start_at && $is_max_count)
        $format_str .= " LIMIT ".$parameters['max_count'];

      $query = sprintf($format_str, implode(',', $vals), MESSAGES_TABLE_NAME, $user_id, $contact_id, $contact_id, $user_id);
      $result = mysql_query($query) or
        handle_server_error("Error Code 1027: Data currently unavailable.");
      $count = 0;
      $vals = array("from_user_id","to_user_id","timestamp","content", "has_image", "status");
      while($row = mysql_fetch_array($result)){
        foreach ($vals as $value) {
          $messages[$count][$value] = $row[$value];
        }
        $count++;
      }
      if($count == 0) $messages = array();
      return $messages;
    }
    //profiles/1/messages/2/images/1376060650/
    private function get_message_image($user_id, $contact_id, $timestamp, $parameters, $requesting_user_id){
      if($user_id != $requesting_user_id && $contact_id != $requesting_user_id)
        handle_unauthorized_request("Error Code 1028: Access to message image content denied.");
      connect_to_database();
      $format_str = <<<EOD
SELECT timestamp,from_user_id,to_user_id,has_image FROM %s WHERE from_user_id=%d AND to_user_id=%d AND timestamp=%f
EOD;
      $query = sprintf($format_str, MESSAGES_TABLE_NAME, $user_id, $contact_id, $timestamp);
      $result = mysql_query($query) or
        handle_server_error("Error Code 1029: Data currently unavailable.");
      $row = mysql_fetch_array($result) or
        handle_not_found("Error Code 1028: Image not found.");
      if($row['has_image'] === false)
        handle_not_found("Error Code 1029: Image not found.");
      $thumb = false;
      if(isset($parameters['thumb']))
        $thumb = $thumb = ($parameters['thumb'] == 'YES');
      $path = "uploads/message_images/".$row['from_user_id']."_".$row['to_user_id']."_".$row['timestamp'].".jpg";
      $data = print_image($path, $thumb);
      $this->format = 'image/jpeg';
      return $data;
    }
  }
?>