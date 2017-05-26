<?php

namespace OddStats\Controllers;


class JiraProjectController extends Controller
{
  public function getProject($req, $res, $project_key)
  {
    return $res->withJson($this->jiraApi->getProject($project_key['id']));
  }
}