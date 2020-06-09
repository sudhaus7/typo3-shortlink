<?php


use SUDHAUS7\Shortcutlink\Middleware\RedirectMiddleware;

return [
    'frontend' => [

        'shortcutlink/frontend/redirect' => [
            'target' => RedirectMiddleware::class,
            'before' => [
                'typo3/cms-frontend/preprocessing',
            ],
        ]
    ]
];
