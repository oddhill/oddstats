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

// Wrapper for the Jira API.
$container['jira'] = function ($c) {
  $settings = $c->get('settings')['jira'];


  $api = new\JiraRestApi\Issue\IssueService(new \JiraRestApi\Configuration\ArrayConfiguration([
    'jiraHost' => $settings['jira_host'],
    'jiraUser' => $settings['user'],
    'jiraPassword' => $settings['password'],
  ]));
  //return $api->get('12000');
  return $api;
};
