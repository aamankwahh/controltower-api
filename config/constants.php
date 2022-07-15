<?php
// return [
//     'intents' => [
//        'APPROACH',
//        'TAKEOFF',
//        'LAND'
//     ]
// ];


return [
    'state_actions' => [
        'TAKEOFF' => 'AIRBORNE',
        'APPROACH' => 'LANDED',
        'CREW' => 'PARKED'
       
    ],
    'allowable_actions'=>[
        'APPROACH',
        'TAKEOFF',
        'LAND'
    ],
    'allowable_states'=>[
        'AIRBORNE',
        'LANDED',
        'PARKED'
    ]
];