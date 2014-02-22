<?php
  class ResponseJpeg {

    //@var string
    protected $data;
    
    //@param string $data
    public function __construct($data) {
      $this->data = $data;
      return $this;
    }
    
    //@return string
    public function render() {
      header('Content-Type: image/jpeg');
      return $this->data;
    }
  }
?>