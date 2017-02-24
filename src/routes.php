<?php
// Routes

use Harvest\Model\Range;

$app->get('/time', function ($request, $response, $args) {
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
    $users = $this->harvest->getUsers()->get('data');
    $projects = $this->harvest->getProjects()->get('data');
    $tasks = $this->harvest->getTasks()->get('data');

    $settings = $this->get('settings')['harvest'];

    foreach ($users as $user) {
        $active = $user->get('is-active') === 'true';
        $contractor = $user->get('is-contractor') === 'true';

        if ($active && !$contractor) {
            $entries = $this->harvest->getUserEntries($user->get('id'), $range)->get('data');
            foreach ($entries as $entry) {
                $project_id = $entry->get('project-id');
                $task_id = $entry->get('task-id');
                $client_id = $projects[$project_id]->get('client-id');
                $hours = (float) $entry->get('hours');
                $exclude = in_array($client_id, $settings['exclude']['clients']) || in_array($project_id, $settings['exclude']['projects']);
                $internal = in_array($client_id, $settings['internal']['clients']) || in_array($project_id, $settings['internal']['projects']);
                $billable = ($projects[$project_id]->get('billable') === 'true') && ($tasks[$task_id]->get('billable-by-default') === 'true');

                if (!$exclude) {
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
