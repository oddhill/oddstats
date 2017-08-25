<?php

// Get all projects
$app->get('/jira/projects', 'JiraProjectsController:getAllProjects');

// Get all done issues in one week
$app->get('/jira/done-issues', 'JiraDoneIssuesThisWeekController:getDoneThisWeek');

// Get specific project
$app->get('/jira/project/{id}', 'JiraProjectController:getProject');

// Get specific issue
$app->get('/jira/issue/{id}', 'JiraIssueController:getIssue');

// Get all issues
$app->get('/jira/issues', 'JiraIssuesController:getAllIssue');

// Get issues of project
$app->get('/jira/issues-of/{id}', 'JiraIssuesOfController:getProjctIssue');

// Get done issues
$app->get('/jira/done-issues/{id}', 'JiraDoneIssuesController:getDoneIssues');

// Get to be done issues
$app->get('/jira/waiting-issues/{id}', 'JiraNotDoneIssuesController:getNotDoneIssues');