<?php
require_once "app_config.php";

function connect_to_database(){
 mysql_connect(DATABASE_HOST, DATABASE_USERNAME, DATABASE_PASSWORD) or
 handle_server_error("There was a problem connecting to the database we needed for your info: ".mysql_error());

 mysql_select_db(DATABASE_NAME)  or
 handle_server_error("There is a configuration problem with our database: ". mysql_error());
};


?>