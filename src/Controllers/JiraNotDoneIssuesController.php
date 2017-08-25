<?php

namespace OddStats\Controllers;

class JiraNotDoneIssuesController extends Controller
{
  /**
   * @param $req
   * @param $res
   * @param $issue_key
   * @return string
   */
  public function getNotDoneIssues($req, $res, $project_key) {
    return $res->withJson($this->jiraApi->getNotDoneIssues($project_key['id']));
  }
}