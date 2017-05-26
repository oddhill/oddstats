<?php
// Application middleware

// HTTP Basic Authentication.
if (($username = getenv('BASIC_AUTH_USERNAME')) && ($password = getenv('BASIC_AUTH_PASSWORD'))) {
  $app->add(new \Slim\Middleware\HttpBasicAuthentication([
    'users' => [
      $username => $password,
    ]
  ]));
}
