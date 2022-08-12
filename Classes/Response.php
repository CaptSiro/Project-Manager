<?php

class Response {
  
  // Informational 
    const CONTINUE = 100;
    const SWITCHING_PROTOCOLS = 101;

  // Successful 
    const OK = 200;
    const CREATED = 201;
    const ACCEPTED = 202;
    const NON_AUTHORITATIVE_INFORMATION = 203;
    const NO_CONTENT = 204;
    const RESET_CONTENT = 205;
    const PARTIAL_CONTENT = 206;

  // Redirection 
    const MULTIPLE_CHOICES = 300;
    const MOVED_PERMANENTLY = 301;
    const FOUND = 302;
    const SEE_OTHER = 303;
    const NOT_MODIFIED = 304;
    const USE_PROXY = 305;

  // Client Error
    const BAD_REQUEST = 400;
    const UNAUTHORIZED = 401;
    const PAYMENT_REQUIRED = 402;
    const FORBIDDEN = 403;
    const NOT_FOUND = 404;
    const METHOD_NOT_ALLOWED = 405;
    const NOT_ACCEPTABLE = 406;
    const PROXY_AUTHENTICATION_REQUIRED = 407;
    const REQUEST_TIMEOUT = 408;
    const CONFLICT = 409;
    const GONE = 410;
    const LENGTH_REQUIRED = 411;
    const PRECONDITION_FAILED = 412;
    const PAYLOAD_TOO_LARGE = 413;
    const URI_TOO_LONG = 414;
    const UNSUPPORTED_MEDIA_TYPE = 415;

  // Server Error
    const INTERNAL_SERVER_ERROR = 500;
    const NOT_IMPLEMENTED = 501;
    const BAD_GATEWAY = 502;
    const SERVICE_UNAVAILABLE = 503;
    const GATEWAY_TIMEOUT = 504;
    const HTTP_VERSION_NOT_SUPPORTED = 505;

  
  function __construct ($headers = []) {
    $this->headers = $headers;
  }
  private $headers = [];



  function setHeader ($hName, $hValue) {
    $this->headers[$hName] = $hValue;
  }

  function setHeaders ($headers) {
    foreach ($headers as $header) {
      $this->headers[$header[0]] = $header[1];
    }
  }

  function hasHeader ($hName) {
    return isset($this->headers[$hName]);
  }

  function removeHeader ($hName) {
    unset($this->headers[$hName]);
  }

  function setStatusCode ($code) {
    http_response_code($code);
  }

  private function generateHeaders () {
    foreach ($this->headers as $name => $value) {
      header("$name: $value");
    }
  }



  function send ($text) {
    $this->generateHeaders();
    exit("" . $text);
  }
  
  function json ($jsonEncodeAble) {
    $this->generateHeaders();
    exit(json_encode($jsonEncodeAble));
  }

  function download ($file) {
    if (count($this->headers) == 0) {
      $this->setStatusCode(self::INTERNAL_SERVER_ERROR);
      $this->error("Not enough headers were set for file transfer: $file.");
    }

    if (!file_exists($file)) {
      $this->setStatusCode(self::NOT_FOUND);
      $this->error("File not found: $file");
    }

    $this->generateHeaders();
    readfile($file);
    exit();
  }

  function error (string $errorMessage) {
    $this->send($errorMessage);
  }


  
  static function propNotFound () {
    return function ($HTTPMethod, $propName) {
      $response = new Response();
      $response->setStatusCode(Response::NOT_FOUND);
      $response->error("$propName is required for this operation. (method: $HTTPMethod)");
    };
  }
}