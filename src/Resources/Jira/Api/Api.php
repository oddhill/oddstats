<?php

namespace OddStats\Resources\Jira\Api;

use OddStats\Resources\Jira\Auth\Auth;

class Api
{
  const REQ_GET = 'GET';

  /**
   * Endpoint URL.
   *
   * @var string
   */
  protected $endpoint;

  /**
   * Client.
   *
   * @var JiraRequest
   */
  protected $client;

  /**
   * Authentication.
   *
   * @var Auth
   */
  protected $auth;

  /**
   * Create a JIRA API connection.
   * @param string    $endpoint   Endpoint URL
   * @param Auth $auth     Authentication
   * @param JiraRequest   $client
   */
  public function __construct($endpoint, Auth $auth, $client = null)
  {
    $this->endpoint = $this->setEndpoint($endpoint);
    $this->auth = $auth;
    $this->client = new JiraRequest();
  }

  /**
   * Set URL
   *
   * @param $url
   * @return string
   */
  public function setEndpoint($url)
  {
    // Remove the slash from the en of url
    $url = rtrim( $url, '/');

    return $this->endpoint = $url;
  }

  /**
   * Send request.
   * @param string $method
   * @param $url
   * @param array $data
   * @return string
   */
  public function api($method = self::REQ_GET, $url, $data = array())
  {
    $result = $this->client->jRequest( $this->getEndpoint(), $url, $this->auth, $method);
    return json_decode($result);
  }

  /**
   * Get a specific issue
   * @param string    $issue_key    Issue key.
   * @return string
   */
  public function getIssue($issue_key)
  {
    return $this->api( self::REQ_GET, sprintf('/rest/api/2/issue/%s', $issue_key));
  }

  /**
   * Listing all projects
   * @return string
   */
  public function getProjects()
  {
    return $this->api( self::REQ_GET, '/rest/api/2/project');
  }

  /**
   * Get a specific project
   *
   * @param string $project_key Project key
   * @return string
   */
  public function getProject($project_key)
  {
    return $this->api( self::REQ_GET, sprintf('/rest/api/2/project/%s', $project_key), array(), true);
  }

  /**
   * Get the Endpoint.
   *
   * @return string
   */
  public function getEndpoint()
  {
    return $this->endpoint;
  }
}