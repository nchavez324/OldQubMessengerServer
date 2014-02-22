<?php
  class Request {
    //@var array
    public $url_elements = array();

    //@var string
    public $method;
    
    //@var array
    public $parameters;

    public $userID;

    public function get_data(){
      $a = array();
      $a['method']=$this->method;
      $a['userID']=$this->userID;
      $a['parameters']=$this->parameters;
      $a['urlElements']=$this->url_elements;
      return $a;
    }
  }
?>