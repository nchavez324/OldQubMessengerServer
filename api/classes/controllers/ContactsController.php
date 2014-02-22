<?php
  require_once dirname(__FILE__)."/../../extra/app_config.php";
  require_once dirname(__FILE__)."/../../extra/database_connection.php";

  class ContactsController {

    public $format = 'application/json';

    public function get($request, $user_id){
      if(count($request->url_elements) == 3)
        return $this->get_contacts($user_id, $request->parameters, $request->userID);
      else
        handle_bad_request("Error Code 1016: Invalid contact resource request.");
    }

    private function get_contacts($user_id, $parameters, $requesting_user_id){
      if($user_id != $requesting_user_id)
        handle_unauthorized_request("Error Code 1017: Access to contact lsit denied.");
      connect_to_database();
      $query = sprintf("SELECT first_user_id,second_user_id FROM %s
        WHERE (first_user_id=%d OR second_user_id=%d) AND status='CONF'",
        CONTACTS_TABLE_NAME, $user_id, $user_id);
      $result = mysql_query($query) or
        handle_server_error("Error 1018: Contact list data currently unavailable.");
      $count = 0;
      $list_of_contact_ids = array();
      while($row = mysql_fetch_array($result)){
        $other_id = ($row['first_user_id']==$user_id)?$row['second_user_id']:$row['first_user_id'];
        $list_of_contact_ids[$count] = $other_id;
        $count++;
      }
      if($count <= 0)
        return array();
      $vals = array('user_id', 'username', 'name', 'selected_image', 'num_profile_pics');
      $query = sprintf("SELECT %s FROM %s WHERE user_id IN (%s)", implode(',', $vals), USERS_TABLE_NAME, implode(',', $list_of_contact_ids));
      $result = mysql_query($query) or handle_server_error("Error Code 1018: Contact list data currently unavailable.");
      $list_of_contacts = array();
      $count = 0;
      while($row = mysql_fetch_array($result)){
        foreach ($vals as $key => $value) {
          $list_of_contacts[$count][$value] = $row[$value];
        }
        $count++;
      }
      return $list_of_contacts;
    }
  }
?>