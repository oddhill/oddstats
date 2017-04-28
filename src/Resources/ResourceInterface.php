<?php

namespace OddStats\Resources;

interface ResourceInterface {

  function __construct(\Slim\app $app);

  public function getPath();

  public function routes();

}
