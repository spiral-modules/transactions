<?php

return [
    'currencies'      => [
        'usd' => [
            'code'       => 'usd',
            'char'       => '$',
            'multiplier' => 100,
            'format'     => '{char}{amount}',
            'sprintf'    => '%.2f'
        ]
    ],
    'defaultCurrency' => [
        'format'  => '{code}{amount}',
        'sprintf' => '%.2f'
    ]
];