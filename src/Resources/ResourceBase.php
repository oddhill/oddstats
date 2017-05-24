<?php

namespace OddStats\Resources;

use Slim\app;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class ResourceBase
 *
 * This class must be extended when implementing resources.
 */
class ResourceBase implements ResourceInterface {

  /** @var \Slim\app $app */
  public $app;

  /**
   * {@inheritdoc}
   */
  function __construct(app $app) {
    $this->app = $app;
  }

  /**
   * {@inheritdoc}
   */
  public function getPath() {
    // Generate a path based on the class name.
    $class_name = explode('\\', get_class($this));
    return '/' . strtolower(end($class_name));
  }

  /**
   * {@inheritdoc}
   */
  public function routes() {
    $path = $this->getPath();

    $this->app->get('', function(Request $request, Response $response, $args) use($path) {
      $body = $response->getBody();
      $body->write("$path has been setup. The next step is to implement the routes() method in order to setup your routes.");
      return $response;
    });
  }

}
