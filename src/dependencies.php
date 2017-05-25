<?php
// DIC configuration

$container = $app->getContainer();

// view renderer
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};

// Wrapper for the Harvest API.
$container['harvest'] = function ($c) {
    $settings = $c->get('settings')['harvest'];
    $api = new Harvest\HarvestReports();
    $api->setAccount($settings['account']);
    $api->setUser($settings['user']);
    $api->setPassword($settings['password']);
    return $api;
};

$container['jira'] = function ($c) {
  $settings = $c->get('settings');
  $jira_host = $settings['jira']['jira_host'];
  $jira_user_id = $settings['jira']['user'];
  $jira_pass = $settings['jira']['password'];

  $basic_auth = new \OddStats\Resources\Jira\Auth\AuthBasic( $jira_user_id, $jira_pass);
  $jira_api = new \OddStats\Resources\Jira\Api\Api( $jira_host, $basic_auth);


  //return $jira_api->api('GET', '/rest/api/2/project');
  return $jira_api->getIssue('BPS-145');
};