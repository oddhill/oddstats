<?php

namespace OddStats\Controllers;

class JiraDoneIssuesThisWeekController extends Controller
{
  /**
   * @param $req
   * @param $res
   * @param $issue_key
   * @return string
   */
  public function getDoneThisWeek($req, $res, $project_key) {
    return $res->withJson($this->jiraApi->getDoneThisWeek($project_key['id']));
  }
}