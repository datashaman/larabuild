<?php

return [
    'docker' => [
        'enabled' => (bool) env('LARABUILD_DOCKER', false),
    ],
    'roles' => [
        'admin',
        'team-admin',
    ],
    'statuses' => [
        'new',
        'started',
        'fail',
        'success',
        'not-found',
    ],
];
