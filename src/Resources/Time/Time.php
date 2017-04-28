<?php

namespace OddStats\Resources\Time;

use OddStats\Resources\ResourceBase;

/**
 * Time resource class.
 */
class Time extends ResourceBase {

  private $account;
  private $user;
  private $password;
  private $exclude = [
    'clients' => [],
    'projects' => [],
  ];
  private $internal = [
    'clients' => [],
    'projects' => [],
  ];

  /**
   * {@inheritdoc}
   */
  function __construct(\Slim\app $app) {
    parent::__construct($app);

    $this->account = getenv('HARVEST_ACCOUNT');
    $this->user = getenv('HARVEST_USER');
    $this->password = getenv('HARVEST_PASSWORD');
    $this->exclude['clients'] = explode(',', getenv('HARVEST_EXCLUDE_CLIENTS'));
    $this->exclude['projects'] = explode(',', getenv('HARVEST_EXCLUDE_PROJECTS'));
    $this->internal['clients'] = explode(',', getenv('HARVEST_INTERNAL_CLIENTS'));
    $this->internal['projects'] = explode(',', getenv('HARVEST_INTERNAL_PROJECTS'));
  }

}
