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

  protected $done;

  protected $term = ["Done", "Resolved"];

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
    $issue =  $this->api( self::REQ_GET, sprintf('/rest/api/2/issue/%s', $issue_key));
    return $this->issueDetails($issue);
  }

  /**
   * Returning all issues
   *
   * @return array
   */
  public function getAllIssue()
  {
   foreach ($this->getProjectIds() as $key) {
     $issues[] = $this->issueStatus($key);
   }
   return $issues;
  }

  /**
   * Returning all issues of a specific project
   *
   * @param $project_key
   * @return string
   */
  public function getProjctIssue($project_key)
  {
    return $this->issueStatus($project_key);
  }

  /**
   * Returning all done issues of a specific project
   *
   * @param $project_key
   * @return string
   */
  public function getDoneIssues($project_key)
  {
    return $this->doneIssue($project_key);
  }

  /**
   * Returning all issues that are not dene
   * of a specific project
   *
   * @param $project_key
   * @return string
   */
  public function getNotDoneIssues($project_key)
  {
    return $this->NotDoneIssue($project_key);
  }

  /**
   * Listing all projects
   *
   * @return string
   */
  public function getProjects()
  {
    return $this->api( self::REQ_GET, '/rest/api/2/project');
  }

  public function getDoneThisWeek()
  {
    foreach ($this->getProjectIds() as $key) {
      foreach ($this->issueStatus($key) as $issue) {
        if (in_array($issue['status'], $this->term) && $issue["updatedAtweekNo"] == (date( 'W') -1)) {
          $doen[] = $issue;
        }
      }
    }
    return $doen;
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

  /**
   * Returning array of projects id
   *
   * @return array
   */
  protected function getProjectIds()
  {
    foreach ($this->getProjects() as  $project_key) {
      $keys[] = $project_key->key;
    }
    return $keys;
  }

  /**
   * Returning all issue of a project
   *
   * @param $project_key
   * @return array
   */
  protected function issueStatus($project_key)
  {
    $issues = $this->api( self::REQ_GET, sprintf('/rest/api/2/search?jql=project="%s"', $project_key), array(), true)->issues;
    foreach ($issues as $issue) {
      $status[] = $this->issueDetails($issue);
    }
    return $status;
  }

  /**
   * Returning done issues
   *
   * @param $project_key
   * @return array
   */
  protected function doneIssue($project_key)
  {
    foreach ($this->issueStatus($project_key) as $issue) {
      if (in_array($issue['status'], $this->term)) {
        $done[] = $issue;
      }
    }
    return $done;
  }

  /**
   * Returning to be done issues
   *
   * @param $project_key
   * @return array
   */
  protected function notDoneIssue($project_key)
  {
    foreach ($this->issueStatus($project_key) as $issue) {
      if (!in_array($issue['status'], $this->term)) {
        $to_bo_done[] = $issue;
      }
    }
    return $to_bo_done;
  }

  /**
   * @param $issue
   *
   * @return array
   */
  protected function issueDetails($issue)
  {
    return $issue = [
      "project_name" => $issue->fields->project->name,
      "projectKey" => $issue->fields->project->key,
      "self" => $issue->self,
      "description" => $issue->fields->issuetype->description,
      "issue_key" => $issue->key,
      "issueId" => $issue->id,
      "priority" => $issue->fields->priority->name,
      "issueType" => $issue->fields->issuetype->name,
      "status" => $issue->fields->status->name,
      "creator" => $issue->fields->creator->displayName,
      "created" => $issue->fields->created,
      "updated" => $issue->fields->updated,
      "assigneeName" => $issue->fields->assignee->displayName,
      "assigneeEmail" => $issue->fields->assignee->emailAddress,
      "updatedAtweekNo" => date( 'W', strtotime( $issue->fields->updated )),
    ];
  }
}