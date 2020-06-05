<?php


return [
    'frontend' => [

        'shortlink/frontend/redirect' => [
            'target' => \SUDHAUS7\Shortlink\Middleware\RedirectMiddleware::class,
            'before' => [
                'typo3/cms-frontend/preprocessing',
            ],
        ]
    ]
];
/**
 * 'after' => [
'typo3/cms-frontend/authentication',
],
 */
