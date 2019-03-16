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
        'CHECKOUT',
        'BUILDING',
        'FAILED',
        'OK',
        'NOT_FOUND',
    ],
];
