<?php
if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $url  = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) {
        return false;
    }
}

require __DIR__ . '/../vendor/autoload.php';

session_start();

// Load dotenv variables.
$dotenv = new Dotenv\Dotenv('../');
$dotenv->load();

// Instantiate the app
$settings = require __DIR__ . '/../src/settings.php';
$app = new \Slim\App($settings);

// Set up resources
$resources = [];
foreach (new DirectoryIterator(__DIR__ . '/../src/Resources') as $resource) {
  if ($resource->isDir() && !$resource->isDot()) {
    $resource_name = $resource->getFilename();
    $class_file = $resource->getPathname() . "/$resource_name.php";

    if (file_exists($class_file)) {
      require $class_file;
      $class = "\OddStats\Resources\\$resource_name\\$resource_name";
      $resources[$resource_name] = new $class($app);
    }
  }
}

// Set up dependencies
require __DIR__ . '/../src/dependencies.php';

// Register middleware
require __DIR__ . '/../src/middleware.php';

// Register routes
require __DIR__ . '/../src/routes.php';

// Run app
$app->run();
