<?php
  class ResponsePng {

    //@var string
    protected $data;
    
    //@param string $data
    public function __construct($data) {
      $this->data = $data;
      return $this;
    }
    
    //@return string
    public function render() {
      header('Content-Type: image/png');
      return $this->data;
    }
  }
?>