<?php
//require_once "extra/force_secure_connection.php";
require_once dirname(__FILE__)."/extra/app_config.php";

//TODO: Eventuall migrate to mysqli or whatever
error_reporting( E_ALL & ~E_DEPRECATED);

function autoload_class($class_name) {
  $directories = array(
    'classes/',
    'classes/controllers/'
    );
  foreach ($directories as $directory) {
    $filename = $directory . $class_name . '.php';
    if (is_file($filename)) {
      require($filename);
      break;
    }
  }
}
spl_autoload_register('autoload_class');
  /*
   * Parse the incoming request.
   */
  $request = new Request();
  if (isset($_SERVER['PATH_INFO'])) {
    //URL Request path put into array
    $request->url_elements = explode('/', trim($_SERVER['PATH_INFO'], '/'));
  }else if(isset($_SERVER['ORIG_PATH_INFO'])){
    $request->url_elements = explode('/', trim($_SERVER['ORIG_PATH_INFO'], '/'));
  }
  $request->method = strtoupper($_SERVER['REQUEST_METHOD']);

  switch ($request->method) {
    case 'GET':
    $request->parameters = $_GET;
    break;
    case 'POST':
    $request->parameters = $_POST;
    break;
    case 'PUT':
    parse_str(file_get_contents('php://input'), $request->parameters);
    break;
    case 'DELETE':
    $request->parameters = $_GET;
    break;
  }

  /*
   * Route the request.
   */
  $content_type = 'plain/text';
  if (!empty($request->url_elements)) {
    $controller_name = ucfirst($request->url_elements[0]) . 'Controller';
    if (class_exists($controller_name)) {
      $controller = new $controller_name;
      $action_name = strtolower($request->method);

      $authenticator = new Authenticator();
      if($authenticator->is_authentic($request)){
        $request = $authenticator->remove_auth_credentials($request);
        $response_str = call_user_func_array(array($controller, $action_name), array($request));
        $content_type = $controller->get_content_type();
      }else{
        handle_unauthorized_request("Could not authenticate.");
      }
    }else{
      handle_bad_request('"'.$request->url_elements[0].'" is not a valid resource.');
    }
  }else {
    handle_bad_request('Not a valid resource.');
  }

  /*
   * Send the response to the client.
   */
  $response_obj = Response::create($response_str, $content_type);
  header('HTTP/1.1 200 OK');
  echo $response_obj->render();