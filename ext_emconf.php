<?php

$EM_CONF[$_EXTKEY] = [
    'title' => '(Sudhaus7) URL/Link Shortener',
    'description' => 'A URL/Link Shortener for the TYPO3 frontend',
    'category' => 'plugin',
    'version' => '0.0.1',
    'state' => 'stable',
    'uploadfolder' => 0,
    'clearcacheonload' => 0,
    'author' => 'Frank Berger',
    'author_email' => 'fberger@sudhaus7.de',
    'author_company' => 'Sudhaus7, ein Label der B-Factor GmbH',
    'constraints' => [
        'depends' => [
            'typo3' => '9.5.0-10.4.99'
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ],
    ],
    'autoload' => [
        'psr-4' => [
            'SUDHAUS7\\Shortlink\\' => 'Classes'
        ]
    ],
];
