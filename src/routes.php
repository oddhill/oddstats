<?php
// Routes

use Harvest\Model\Range;

$app->get('/time[/{department}]', function ($request, $response, $args) {
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
    $requested_department = isset($args['department']) ? $args['department'] : FALSE;
    $settings = $this->get('settings')['harvest'];

    if (!is_array($users)) {
      return $response->withJson($time);
    }

    foreach ($users as $user) {
        $active = $user->get('is-active') === 'true';
        $contractor = $user->get('is-contractor') === 'true';
        $in_requested_department = $requested_department ? (strtolower($requested_department) == strtolower(str_replace(' ', '-', $user->get('department')))) : TRUE;

        if ($active && !$contractor && $in_requested_department) {
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

    if ($slack_settings = $this->get('settings')['slack']) {
      $department = $requested_department ? str_replace('-', ' ', $requested_department) . 's' : 'the entire company';
      $start = date('j/n', strtotime($from));
      $end = date('j/n', strtotime($to));
      $text = "Metrics for $department between $start - $end:\n";
      $text .= "Client occupancy: {$time['client']['percentage']}%\n";
      $text .= "Billable rate: {$time['billable']['percentage']}%\n";

      $data = array('text' => $text);
      if ($slack_settings['channel']) {
        $data['channel'] = $slack_settings['channel'];
      }
      if ($slack_settings['username']) {
        $data['username'] = $slack_settings['username'];
      }
      if ($slack_settings['icon_url']) {
        $data['icon_url'] = $slack_settings['icon_url'];
      }
      elseif ($slack_settings['icon_emoji']) {
        $data['icon_emoji'] = $slack_settings['icon_emoji'];
      }

      // $ch = curl_init($slack_settings['webhook_url']);
      // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
      // curl_setopt($ch, CURLOPT_POSTFIELDS, 'payload=' . json_encode($data));
      // curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
      // $result = curl_exec($ch);
      // curl_close($ch);
    }

    return $response->withJson($time);
});

$app->get('/jira-api', function($req, $res) {
  var_dump($this->jira->get('BPS-145'));
});