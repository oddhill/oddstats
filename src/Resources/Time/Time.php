<?php

namespace OddStats\Resources\Time;

class Time {

  public $app;
  public $path = '/time';

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

  function __construct($app) {
    $this->app = $app;
    $this->account = getenv('HARVEST_ACCOUNT');
    $this->user = getenv('HARVEST_USER');
    $this->password = getenv('HARVEST_PASSWORD');
    $this->exclude['clients'] = explode(',', getenv('HARVEST_EXCLUDE_CLIENTS'));
    $this->exclude['projects'] = explode(',', getenv('HARVEST_EXCLUDE_PROJECTS'));
    $this->internal['clients'] = explode(',', getenv('HARVEST_INTERNAL_CLIENTS'));
    $this->internal['projects'] = explode(',', getenv('HARVEST_INTERNAL_PROJECTS'));
  }

  public function routes($group) {
    $group->get('/test', function($request, $response, $args) {
      return $response->withJson('Hello world');
    });
  }

}
