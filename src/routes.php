<?php

// Get all projects
$app->get('/jira/projects', 'JiraProjectsController:getAllProjects');

// Get specific project
$app->get('/jira/project/{id}', 'JiraProjectController:getProject');

// Get specific issue
$app->get('/jira/issue/{id}', 'JiraIssueController:getIssue');