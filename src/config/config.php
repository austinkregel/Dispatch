<?php

return [
    'route' => 'dispatch',
    'view-base' => 'spark::layouts.app',
    'models' => [
        'ticket' => Kregel\Dispatch\Models\Ticket::class,
        'jurisdiction' => Kregel\Dispatch\Models\Jurisdiction::class,
    ],
    'color' => '',
    'mail' => [
        'from' => [
            'address' => null,
            'name' => null
        ],
        'subject' => [
            'new' => [
                'user' => 'Thank you for joining!'
            ]
        ],
        'template' => [
            'new' => [
                'user' => 'dispatch::mail.new_user'
            ]
        ]
    ],
    'user' => [ // These are for overrides in case you have changed what
        'email' => 'email',
        'name' => 'name'
        /*
         You can also have a value like
        'name' => [
            'fname',
            'lname'
         ]
         */
    ]
];
