<?php

namespace OddStats\Controllers;


class JiraProjectsController extends Controller
{
  public function getAllProjects($req, $res)
  {
    return $res->withJson($this->jiraApi->getProjects());
  }
}