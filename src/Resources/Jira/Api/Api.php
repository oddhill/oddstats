<?php

namespace OddStats\Resources\Jira\Api;

use OddStats\Resources\Jira\Auth\Auth;

class Api
{
  const REQ_GET = 'GET';

  protected $endpoint;

  protected $client;

  protected $auth;

  public function __construct($endpoint, Auth $auth, $client = null)
  {
    $this->endpoint = $this->setEndpoint($endpoint);
    $this->auth = $auth;
    $this->client = new JiraRequest();
  }

  public function setEndpoint($url)
  {
    // Remove the slash from the en of url
    $url = rtrim( $url, '/');

    return $this->endpoint = $url;
  }

  public function getEnpoint()
  {
    return $this->endpoint;
  }

  public function api($method = self::REQ_GET, $url, $data = array())
  {
    $result = $this->client->jRequest( $this->getEnpoint(), $url, $this->auth, $method);
    return $json = json_decode($result, true);;
  }

  public function getIssue($issue_key)
  {
    return $this->api( self::REQ_GET, sprintf('/rest/api/2/issue/%s', $issue_key));
  }

  public function getProjects()
  {
    return $this->api( self::REQ_GET, '/rest/api/2/project');
  }

  public function getProject($project_key)
  {
    return $this->api( self::REQ_GET, sprintf('/rest/api/2/project/%s', $project_key), array(), true);
  }
}