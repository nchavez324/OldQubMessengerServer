<?php
  class ResponseJson {

    //@var string
    protected $data;
    
    //@param string $data
    public function __construct($data) {
      $this->data = $data;
      return $this;
    }
    
    //@return string
    public function render() {
      header('Content-Type: application/json');
      return json_encode($this->data);
    }
  }
?>