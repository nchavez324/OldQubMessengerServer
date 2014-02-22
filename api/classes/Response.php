<?php
  
  require_once "ResponseJson.php";
  require_once "ResponsePng.php";
  require_once "ResponseJpeg.php";
  
  class Response {

    public static function create($data, $format) {
      switch ($format) {
        case 'image/jpeg':
          $obj = new ResponseJpeg($data);
          break;
        case 'image/png':
          $obj = new ResponsePng($data);
        break;
        case 'application/json':
        default:
          $obj = new ResponseJson($data);
          break;
      }
      return $obj;
    }
  }
?>