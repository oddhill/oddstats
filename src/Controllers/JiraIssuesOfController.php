<?php

namespace OddStats\Controllers;

class JiraIssuesOfController extends Controller
{
  /**
   * @param $req
   * @param $res
   * @param $issue_key
   * @return string
   */
  public function getProjctIssue($req, $res, $project_key) {
    return $res->withJson($this->jiraApi->getProjctIssue($project_key['id']));
  }
}