<?php

session_start();
require __DIR__ . '/../vendor/autoload.php';


use Slim\App;
use Dotenv\Dotenv;
use OddStats\Controllers\JiraIssueController;
use OddStats\Controllers\JiraProjectsController;
use OddStats\Controllers\JiraProjectController;


$dotenv = new Dotenv('../');

$dotenv->load();

$app = new App([
  'settings' => [
    'displayErrorDetails' => true, // set to false in production
    'addContentLengthHeader' => false, // Allow the web server to send the content-length header

    // Renderer settings
    'renderer' => [
      'template_path' => __DIR__ . '/../templates/',
    ],

    // Monolog settings
    'logger' => [
      'name' => 'slim-app',
      'path' => __DIR__ . '/../logs/app.log',
      'level' => \Monolog\Logger::DEBUG,
    ],
  ],
]);

$container = $app->getContainer();

$container['jiraApi'] = function ($container) {
  $auth = new \OddStats\Resources\Jira\Auth\AuthBasic(getenv('JIRA_USER'), getenv('JIRA_PASSWORD'));
  $projects =  new \OddStats\Resources\Jira\Api\Api(getenv('JIRA_HOST'), $auth);
  return $projects;
};

$container['JiraProjectsController'] = function ($container) {
  return new JiraProjectsController($container);
};

$container['JiraIssueController'] = function ($container) {
  return new JiraIssueController($container);
};

$container['JiraProjectController'] = function ($container) {
  return new JiraProjectController($container);
};

require __DIR__ . '/../src/routes.php';