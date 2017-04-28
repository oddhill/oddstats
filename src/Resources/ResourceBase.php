<?php

namespace OddStats\Resources;

class ResourceBase implements ResourceInterface {

  /** @var \Slim\app $app */
  public $app;

  function __construct(\Slim\app $app) {
    $this->app = $app;
  }

  public function getPath() {
    $class_name = explode('\\', get_class($this));
    return '/' . strtolower(end($class_name));
  }

  public function routes() {
    $path = $this->getPath();

    $this->app->get('', function($request, $response, $args) use($path) {
      /** @var \Slim\Http\Response $response */

      $body = $response->getBody();
      $body->write("$path has been setup. The next step is to override the routes() method in order to setup your routes.");
      return $response;
    });
  }

}
