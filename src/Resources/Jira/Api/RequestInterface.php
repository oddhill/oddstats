<?php

namespace OddStats\Resources\Jira\Api;

use OddStats\Resources\Jira\Auth\Auth;

interface RequestInterface
{
  public function jRequest($endpoint, $url, Auth $auth, $method, $data = array());
}