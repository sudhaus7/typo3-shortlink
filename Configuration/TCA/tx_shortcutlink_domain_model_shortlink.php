<?php

return [
    'ctrl' => [
        'label' => 'url',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'delete' => 'deleted',
        'adminOnly' => true,
        'hideTable' => true,
        'rootLevel' => 0,
        'title' => 'LLL:EXT:shortcutlink/Resources/Private/Language/locallang.xlf:tca.title',
        'searchFields' => 'title',
        'iconfile' => 'EXT:shortcutlink/Resources/Public/Icons/Extension.svg'
    ],
    'interface' => [
        'showRecordFieldList' => 'shortlink,url'
    ],
    'palettes' => [
    ],
    'types' => [
        1 => [
            'showitem' => 'shortlink,checksum,url'
        ]
    ],
    'columns' => [

        'shortlink' => [
            'label' => 'LLL:EXT:shortcutlink/Resources/Private/Language/locallang.xlf:tca.shortlink',
            'config' => [
                'type' => 'input'
            ]
        ],
        'checksum' => [
            'label' => 'LLL:EXT:shortcutlink/Resources/Private/Language/locallang.xlf:tca.checksum',
            'config' => [
                'type' => 'input'
            ]
        ],
        'url' => [
            'label' => 'LLL:EXT:shortcutlink/Resources/Private/Language/locallang.xlf:tca.url',
            'config' => [
                'type' => 'input'
            ]
        ],
        'feuser' => [
            'label' => 'LLL:EXT:shortcutlink/Resources/Private/Language/locallang.xlf:tca.feuser',
            'config' => [
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => 'fe_users',
                'foreign_table'=> 'fe_users'
            ]
        ],

    ]
];
