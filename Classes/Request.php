<?php

  class Body {
    private $__response;
    public function __construct (Response $res) {
      $this->__response = $res;
    }


    public function propExists ($propName) {
      $objectProps = get_object_vars($this);

      if (!array_key_exists($propName, $objectProps)) {
        $this->__response->setStatusCode(Response::BAD_REQUEST);
        $this->__response->error("$propName is required for this operation. " . $_SERVER["DOCUMENT_ROOT"]);
        return false;
      }

      return true;
    }

    public function __get($propName) {
      if ($this->propExists($propName)) {
        return $this->$propName;
      }
    }
    
    public function __set($propName, $value) {
      return $this->$propName = $value;
    }
  }


  class Request {
    static function POST ($url, array $post = NULL, array $options = []) {
      $defaults = array(
        CURLOPT_POST => 1,
        CURLOPT_HEADER => 0,
        CURLOPT_URL => $url,
        CURLOPT_FRESH_CONNECT => 1,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_FORBID_REUSE => 1,
        CURLOPT_TIMEOUT => 4,
        CURLOPT_POSTFIELDS => http_build_query($post)
      );
    
      $chandler = curl_init();
      curl_setopt_array($chandler, ($options + $defaults));
      if (!$result = curl_exec($chandler)) {
        trigger_error(curl_error($chandler));
      }
      curl_close($chandler);
      return $result;
    }

    static function GET ($url, array $get = NULL, array $options = array()) {   
      $defaults = array(
        CURLOPT_URL => $url . ((strpos($url, '?') === FALSE) ? '?' : '') . http_build_query($get),
        CURLOPT_HEADER => 0,
        CURLOPT_RETURNTRANSFER => TRUE,
        CURLOPT_TIMEOUT => 4
      );
       
      $chandler = curl_init();
      curl_setopt_array($chandler, ($options + $defaults));
      if (!$result = curl_exec($chandler)){
        trigger_error(curl_error($chandler));
      }
      curl_close($chandler);
      return $result;
    }



    public $body, $method, $res;
    function __construct (Response $res) {
      $this->res = $res;
      $this->body = new Body($res);
      $this->method = $_SERVER['REQUEST_METHOD'];

      $scriptPath = Path::breakdown($_SERVER['SCRIPT_FILENAME']);
      $script = end($scriptPath);
      $isIncorrectHTTPMethod = !(strpos(
        $script,
        "-" . $this->method . ".php"
      ) !== false);

      if ($isIncorrectHTTPMethod) {
        $this->res->setStatusCode(Response::BAD_REQUEST);
        $this->res->error("Used HTTP method: " . $this->method . " does not match disered method of script: $script");
      }

      $paramArrays = [&$_GET, &$_POST, &$_FILES];
      foreach ($paramArrays as $array) {
        foreach ($array as $name => $value) {
          $this->body->$name = $value;
        }
      }
    }
  }