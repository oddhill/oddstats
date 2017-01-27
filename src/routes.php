<?php
// Routes

use Harvest\Model\Range;

$app->get('/', function ($request, $response, $args) {
    $time = array();
    $users = $this->harvest->getUsers()->get('data');
    $projects = $this->harvest->getProjects()->get('data');
    $last_week = Range::lastWeek('Europe/Copenhagen', Range::MONDAY);
    $settings = $this->get('settings')['harvest'];

    foreach ($users as $user) {
        $active = $user->get('is-active') === 'true';
        $contractor = $user->get('is-contractor') === 'true';

        if ($active && !$contractor) {
            $entries = $this->harvest->getUserEntries($user->get('id'), $last_week)->get('data');
            $department = $user->get('department');

            if (!isset($time[$department])) {
                $time[$department] = array(
                  'total_hours' => 0,
                  'internal_hours' => 0,
                  'client_hours' => 0,
                  'client_percentage' => 0,
                );
            }

            foreach ($entries as $entry) {
                $project_id = $entry->get('project-id');
                $client_id = $projects[$project_id]->get('client-id');
                $hours = (float) $entry->get('hours');
                $exclude = in_array($client_id, $settings['exclude']['clients']) || in_array($project_id, $settings['exclude']['projects']);
                $internal = in_array($client_id, $settings['internal']['clients']) || in_array($project_id, $settings['internal']['projects']);

                if (!$exclude) {
                    $time[$department]['total_hours'] += $hours;
                    $internal ? ($time[$department]['internal_hours'] += $hours) : ($time[$department]['client_hours'] += $hours);
                    $time[$department]['client_percentage'] = round(($time[$department]['client_hours'] / $time[$department]['total_hours']) * 100);
                }
            }
        }
    }

    return $response->withJson($time);
});
