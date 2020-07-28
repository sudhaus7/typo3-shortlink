<?php


use SUDHAUS7\Shortcutlink\Middleware\NewApikeyMiddleware;
use SUDHAUS7\Shortcutlink\Middleware\RedirectMiddleware;

return [
    'frontend' => [

        'shortcutlink/frontend/redirect' => [
            'target' => RedirectMiddleware::class,
            'after' => [
                'typo3/cms-frontend/site',
            ],
            'before' => [
                'typo3/cms-frontend/backend-user-authentication',
            ]
        ]
    ],
];
