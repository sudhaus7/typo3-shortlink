<?php


use SUDHAUS7\Shortcutlink\Middleware\RedirectMiddleware;

return [
    'frontend' => [

        'shortcutlink/frontend/redirect' => [
            'target' => RedirectMiddleware::class,
            'after' => [
                'typo3/cms-core/normalized-params-attribute',
            ]
        ]
    ],
];
