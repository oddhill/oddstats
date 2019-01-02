<?php

namespace OddStats\Resources\Time;

use Harvest\HarvestReports;
use OddStats\Resources\ResourceBase;
use Slim\app;
use Slim\Http\Request;
use Slim\Http\Response;
use Harvest\Model\Range;

/**
 * Time resource class.
 */
class Time extends ResourceBase {

  private $harvest;
  private $exclude = [
    'clients' => [],
    'projects' => [],
  ];
  private $internal = [
    'clients' => [],
    'projects' => [],
  ];

  /**
   * {@inheritdoc}
   */
  function __construct(app $app) {
    parent::__construct($app);

    $this->harvest = new HarvestReports();
    $this->harvest->setAccount(getenv('HARVEST_ACCOUNT'));
    $this->harvest->setUser(getenv('HARVEST_USER'));
    $this->harvest->setPassword(getenv('HARVEST_PASSWORD'));
    $this->exclude['clients'] = explode(',', getenv('HARVEST_EXCLUDE_CLIENTS'));
    $this->exclude['projects'] = explode(',', getenv('HARVEST_EXCLUDE_PROJECTS'));
    $this->exclude['roles'] = explode(',', getenv('HARVEST_EXCLUDE_ROLES'));
    $this->internal['clients'] = explode(',', getenv('HARVEST_INTERNAL_CLIENTS'));
    $this->internal['projects'] = explode(',', getenv('HARVEST_INTERNAL_PROJECTS'));
    $this->misc['clients'] = explode(',', getenv('HARVEST_MISC_CLIENTS'));
    $this->misc['projects'] = explode(',', getenv('HARVEST_MISC_PROJECTS'));
  }

  /**
   * {@inheritdoc}
   */
  public function routes() {
    $resource = $this;

    $this->app->get('[/{role}]', function (Request $request, Response $response, $args) use($resource) {
      $time = array(
        'hours' => 0,
        'projects' => array(
          'total' => array('hours' => 0, 'percentage' => 0),
          'client' => array('hours' => 0, 'percentage' => 0),
          'internal' => array('hours' => 0, 'percentage' => 0),
        ),
        'misc' => array('hours' => 0, 'percentage' => 0),
        'billable' => array('hours' => 0, 'percentage' => 0),
        'nonBillable' => array('hours' => 0, 'percentage' => 0),
      );
      $from = $request->getQueryParam('from');
      $to = $request->getQueryParam('to', date('Y-m-d'));
      $range = new Range($from, $to);
      $detailed = (bool) $request->getQueryParam('detailed');
      $users = $resource->harvest->getUsers()->get('data');
      $projects = $resource->harvest->getProjects()->get('data');
      $tasks = $resource->harvest->getTasks()->get('data');
      $requested_role = isset($args['role']) ? $args['role'] : FALSE;

      if ($detailed) {
        $time['detailed'] = array();
      }

      /** @var \Harvest\Model\User $user */
      foreach ($users as $user) {
        $active = $user->get('is-active') === 'true';
        $contractor = $user->get('is-contractor') === 'true';
        $roles = preg_split("/\r\n|\n|\r/", $user->get('roles'));

        $exclude_user = FALSE;
        foreach ($resource->exclude['roles'] as $exclude_role) {
          foreach ($roles as $role) {
            if (strtolower(str_replace(' ', '-', trim($role))) === $exclude_role) {
              $exclude_user = TRUE;
              break 2;
            }
          }
        }

        $in_requested_role = FALSE;
        if ($requested_role) {
          foreach ($roles as $role) {
            if ($requested_role === strtolower(str_replace(' ', '-', trim($role)))) {
              $in_requested_role = TRUE;
              break;
            }
          }
        }
        else {
          $in_requested_role = TRUE;
        }

        if ($active && !$contractor && $in_requested_role && !$exclude_user) {
          $entries = $resource->harvest->getUserEntries($user->get('id'), $range)->get('data');

          if ($detailed) {
            $name = $user->get('first-name') . ' ' . $user->get('last-name');
            $time['detailed'][$name] = array(
              'hours' => 0,
              'projects' => array(
                'total' => array('hours' => 0, 'percentage' => 0),
                'client' => array('hours' => 0, 'percentage' => 0),
                'internal' => array('hours' => 0, 'percentage' => 0),
              ),
              'misc' => array('hours' => 0, 'percentage' => 0),
              'billable' => array('hours' => 0, 'percentage' => 0),
              'nonBillable' => array('hours' => 0, 'percentage' => 0),
            );
          }

          /** @var \Harvest\Model\DayEntry $entry */
          foreach ($entries as $entry) {
            /** @var \Harvest\Model\Project $project */
            $project_id = $entry->get('project-id');
            $project = $projects[$project_id];

            /** @var \Harvest\Model\Task $project */
            $task_id = $entry->get('task-id');
            $task = $tasks[$task_id];

            $client_id = $project->get('client-id');
            $exclude = in_array($client_id, $resource->exclude['clients']) || in_array($project_id, $resource->exclude['projects']);

            if (!$exclude) {
              $hours = (float) $entry->get('hours');
              $is_project = !in_array($client_id, $resource->misc['clients']) && !in_array($project_id, $resource->misc['projects']);
              $is_internal = in_array($client_id, $resource->internal['clients']) || in_array($project_id, $resource->internal['projects']);
              $is_billable = !$is_internal && ($project->get('billable') === 'true') && ($task->get('billable-by-default') === 'true');

              $time['hours'] += $hours;
              $is_project ? ($time['projects']['total']['hours'] += $hours) : ($time['misc']['hours'] += $hours);
              $is_billable ? ($time['billable']['hours'] += $hours) : ($time['nonBillable']['hours'] += $hours);
              if ($is_project) {
                $is_internal ? ($time['projects']['internal']['hours'] += $hours) : ($time['projects']['client']['hours'] += $hours);
              }

              if ($detailed) {
                $time['detailed'][$name]['hours'] += $hours;
                $is_project ? ($time['detailed'][$name]['projects']['total']['hours'] += $hours) : ($time['detailed'][$name]['misc']['hours'] += $hours);
                $is_billable ? ($time['detailed'][$name]['billable']['hours'] += $hours) : ($time['detailed'][$name]['nonBillable']['hours'] += $hours);
                if ($is_project) {
                  $is_internal ? ($time['detailed'][$name]['projects']['internal']['hours'] += $hours) : ($time['detailed'][$name]['projects']['client']['hours'] += $hours);
                }
              }
            }
          }
        }
      }

      if ($time['hours']) {
        if ($time['projects']['total']['hours']) {
          $time['projects']['total']['percentage'] = round(($time['projects']['total']['hours'] / $time['hours']) * 100);
          $time['projects']['client']['percentage'] = round(($time['projects']['client']['hours'] / $time['hours']) * 100);
          $time['projects']['internal']['percentage'] = round(($time['projects']['internal']['hours'] / $time['hours']) * 100);
        }
        $time['misc']['percentage'] = round(($time['misc']['hours'] / $time['hours']) * 100);
        $time['billable']['percentage'] = round(($time['billable']['hours'] / $time['hours']) * 100);
        $time['nonBillable']['percentage'] = round(($time['nonBillable']['hours'] / $time['hours']) * 100);

        if (isset($time['detailed'])) {
          foreach ($time['detailed'] as $user => &$data) {
            if ($data['hours']) {
              if ($data['projects']['total']['hours']) {
                $data['projects']['total']['percentage'] = round(($data['projects']['total']['hours'] / $data['hours']) * 100);
                $data['projects']['client']['percentage'] = round(($data['projects']['client']['hours'] / $data['hours']) * 100);
                $data['projects']['internal']['percentage'] = round(($data['projects']['internal']['hours'] / $data['hours']) * 100);
              }
              $data['misc']['percentage'] = round(($data['misc']['hours'] / $data['hours']) * 100);
              $data['billable']['percentage'] = round(($data['billable']['hours'] / $data['hours']) * 100);
              $data['nonBillable']['percentage'] = round(($data['nonBillable']['hours'] / $data['hours']) * 100);
            }
          }
        }
      }

      return $response->withJson($time);
    });
  }

}
