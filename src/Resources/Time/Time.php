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
    $this->internal['clients'] = explode(',', getenv('HARVEST_INTERNAL_CLIENTS'));
    $this->internal['projects'] = explode(',', getenv('HARVEST_INTERNAL_PROJECTS'));
  }

  /**
   * {@inheritdoc}
   */
  public function routes() {
    $resource = $this;

    $this->app->get('[/{department}]', function (Request $request, Response $response, $args) use($resource) {
      $time = array(
        'hours' => 0,
        'client' => array('hours' => 0, 'percentage' => 0),
        'internal' => array('hours' => 0, 'percentage' => 0),
        'billable' => array('hours' => 0, 'percentage' => 0),
        'nonBillable' => array('hours' => 0, 'percentage' => 0),
      );
      $from = $request->getQueryParam('from');
      $to = $request->getQueryParam('to', date('Y-m-d'));
      $range = new Range($from, $to);
      $users = $resource->harvest->getUsers()->get('data');
      $projects = $resource->harvest->getProjects()->get('data');
      $tasks = $resource->harvest->getTasks()->get('data');
      $requested_department = isset($args['department']) ? $args['department'] : FALSE;

      /** @var \Harvest\Model\User $user */
      foreach ($users as $user) {
        $active = $user->get('is-active') === 'true';
        $contractor = $user->get('is-contractor') === 'true';
        $in_requested_department = $requested_department ? (strtolower($requested_department) == strtolower(str_replace(' ', '-', $user->get('department')))) : TRUE;

        if ($active && !$contractor && $in_requested_department) {
          $entries = $resource->harvest->getUserEntries($user->get('id'), $range)->get('data');

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
              $internal = in_array($client_id, $resource->internal['clients']) || in_array($project_id, $resource->internal['projects']);
              $billable = ($project->get('billable') === 'true') && ($task->get('billable-by-default') === 'true');

              $time['hours'] += $hours;
              $internal ? ($time['internal']['hours'] += $hours) : ($time['client']['hours'] += $hours);
              $billable ? ($time['billable']['hours'] += $hours) : ($time['nonBillable']['hours'] += $hours);
            }
          }
        }
      }

      if ($time['hours']) {
        $time['client']['percentage'] = round(($time['client']['hours'] / $time['hours']) * 100);
        $time['internal']['percentage'] = round(($time['internal']['hours'] / $time['hours']) * 100);
        $time['billable']['percentage'] = round(($time['billable']['hours'] / $time['hours']) * 100);
        $time['nonBillable']['percentage'] = round(($time['nonBillable']['hours'] / $time['hours']) * 100);
      }

      return $response->withJson($time);
    });
  }

}
