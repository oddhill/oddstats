<?php

// Get all projects
$app->get('/jira/projects', 'JiraProjectsController:getAllProjects');

// Get all done issues in one week
$app->get('/jira/done-issues', 'JiraDoneIssuesThisWeekController:getDoneThisWeek');

// Get specific project
/** @var id  the variable id is the project key */
$app->get('/jira/project/{id}', 'JiraProjectController:getProject');

// Get specific issue
/** @var id  the variable id is the issue key */
$app->get('/jira/issue/{id}', 'JiraIssueController:getIssue');

// Get all issues
$app->get('/jira/issues', 'JiraIssuesController:getAllIssue');

// Get issues of project
/** @var id  the variable id is the project key */
$app->get('/jira/issues-of/{id}', 'JiraIssuesOfController:getProjctIssue');

// Get done issues
/** @var id  the variable id is the project key */
$app->get('/jira/done-issues/{id}', 'JiraDoneIssuesController:getDoneIssues');

// Get to be done issues
/** @var id  the variable id is the project key */
$app->get('/jira/waiting-issues/{id}', 'JiraNotDoneIssuesController:getNotDoneIssues');