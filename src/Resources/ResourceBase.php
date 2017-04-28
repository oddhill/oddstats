<?php

namespace OddStats\Resources;

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
  function __construct(\Slim\app $app) {
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

    $this->app->get('', function($request, $response, $args) use($path) {
      /** @var \Slim\Http\Response $response */

      $body = $response->getBody();
      $body->write("$path has been setup. The next step is to override the routes() method in order to setup your routes.");
      return $response;
    });
  }

}
