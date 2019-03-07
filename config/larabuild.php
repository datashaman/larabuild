<?php

return [
    'docker' => [
        'enabled' => (bool) env('LARABUILD_DOCKER', false),
    ],
    'roles' => [
        'ADMIN',
        'TEAM_ADMIN',
    ],
    'statuses' => [
        'NEW',
        'STARTED',
        'FAIL',
        'SUCCESS',
        'NOT_FOUND',
    ],
];
