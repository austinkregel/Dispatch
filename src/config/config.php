<?php

return [
    'route' => 'dispatch',
    'view-base' => 'spark::layouts.app',
    'models' => [
        'ticket' => Kregel\Dispatch\Models\Ticket::class,
        'jurisdiction' => Kregel\Dispatch\Models\Jurisdiction::class,
    ],
];
