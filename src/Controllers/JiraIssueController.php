<?php

namespace OddStats\Controllers;

class JiraIssueController extends Controller
{
  /**
   * @param $req
   * @param $res
   * @param $issue_key
   * @return string
   */
  public function getIssue($req, $res, $issue_key) {
    return $res->withJson($this->jiraApi->getIssue($issue_key['id']));
  }
}