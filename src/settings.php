<?php
return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header

        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ . '/../templates/',
        ],

        // Monolog settings
        'logger' => [
            'name' => 'slim-app',
            'path' => __DIR__ . '/../logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],

        // Harvest settings
        'harvest' => [
            'account' => getenv('HARVEST_ACCOUNT'),
            'user' => getenv('HARVEST_USER'),
            'password' => getenv('HARVEST_PASSWORD'),
            'exclude' => [
                'clients' => explode(',', getenv('HARVEST_EXCLUDE_CLIENTS')),
                'projects' => explode(',', getenv('HARVEST_EXCLUDE_PROJECTS')),
            ],
            'internal' => [
                'clients' => explode(',', getenv('HARVEST_INTERNAL_CLIENTS')),
                'projects' => explode(',', getenv('HARVEST_INTERNAL_PROJECTS')),
            ],
        ],

        // Slack integration
        'slack' => [
          'webhook_url' => getenv('SLACK_WEBHOOK_URL'),
          'channel' => getenv('SLACK_WEBHOOK_CHANNEL'),
          'username' => getenv('SLACK_WEBHOOK_USERNAME'),
          'icon_url' => getenv('SLACK_WEBHOOK_ICON_URL'),
          'icon_emoji' => getenv('SLACK_WEBHOOK_ICON_EMOJI'),
        ],
    ],
];
