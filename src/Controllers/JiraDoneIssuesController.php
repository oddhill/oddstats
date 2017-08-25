<?php

namespace OddStats\Controllers;

class JiraDoneIssuesController extends Controller
{
  /**
   * @param $req
   * @param $res
   * @param $issue_key
   * @return string
   */
  public function getDoneIssues($req, $res, $project_key) {
    return $res->withJson($this->jiraApi->getDoneIssues($project_key['id']));
  }
}