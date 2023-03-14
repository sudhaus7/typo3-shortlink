<?php

$EM_CONF[$_EXTKEY] = [
    'title' => '(Sudhaus7) URL/Link Shortener',
    'description' => 'A URL/Link Shortener for the TYPO3 frontend',
    'category' => 'plugin',
    'version' => '3.0.0',
    'state' => 'stable',
    'uploadfolder' => 0,
    'clearcacheonload' => 0,
    'author' => 'Frank Berger',
    'author_email' => 'fberger@sudhaus7.de',
    'author_company' => 'Sudhaus7, ein Label der B-Factor GmbH',
    'constraints' => [
        'depends' => [
            'typo3' => '11.5.0-11.5.99'
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ],
    ],
    'autoload' => [
        'psr-4' => [
            'SUDHAUS7\\Shortcutlink\\' => 'Classes',
            'Tuupola\\' => 'vendor/tuupola/base62/src',
        ]
    ],
];
