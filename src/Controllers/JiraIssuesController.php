<?php

namespace OddStats\Controllers;

class JiraIssuesController extends Controller
{
  /**
   * @param $req
   * @param $res
   * @param $issue_key
   * @return string
   */
  public function getAllIssue($req, $res) {
    return $res->withJson($this->jiraApi->getAllIssue());
  }
}