<?php

namespace OddStats\Resources;

/**
 * Interface ResourceInterface
 *
 * Interface for every resource class.
 */
interface ResourceInterface {

  /**
   * ResourceInterface constructor.
   *
   * @param \Slim\app $app
   */
  function __construct(\Slim\app $app);

  /**
   * Get the base path for the resource.
   *
   * @return string
   */
  public function getPath();

  /**
   * Setup the routes for the resource.
   *
   * @return null
   */
  public function routes();

}
