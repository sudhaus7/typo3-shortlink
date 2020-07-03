<?php


use SUDHAUS7\Shortcutlink\Middleware\NewApikeyMiddleware;
use SUDHAUS7\Shortcutlink\Middleware\RedirectMiddleware;

return [
    'frontend' => [

        'shortcutlink/frontend/redirect' => [
            'target' => RedirectMiddleware::class,
            'before' => [
                'typo3/cms-frontend/preprocessing',
            ],
        ]
    ],
    'backend' => [
    
        'shortcutlink/backend/newapikey' => [
            'target' => NewApikeyMiddleware::class,
            'after' => [
                'typo3/cms-backend/authentication',
            ],
        ]
    ]
];
