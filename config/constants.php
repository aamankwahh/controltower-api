<?php

return [
   
    'allowable_actions'=>array(
        'TAKEOFF'=>[
           
            'previous_state'=>'PARKED',
            'next_state'=>'AIRBORNE'
        ],
        'APPROACH'=>[
            //'previous_action'=>'TAKEOFF',
            //'next_action'=>'CREW',
            'previous_state'=>'AIRBORNE',
            'next_state'=>'LANDED'
        ],
        
        'PARKED'=>[
            //'previous_state'=>'LANDED',
            //'next_state'=>'AIRBORNE',
            'previous_state'=>'CREW',
            'next_state'=>'TAKEOFF'
        ],
        'LANDED'=>[
            //'previous_state'=>'AIRBORNE',
            //'next_state'=>'PARKED',
            'previous_state'=>'APPROACH',
            'next_state'=>'CREW'
        ],
        'AIRBORNE'=>[
            'previous_state'=>'PARKED',
            'next_state'=>'APPROACH',
            //'previous_action'=>'TAKEOFF',
            //'next_action'=>'APPROACH'
        ],

        'CREW'=>[
            //'previous_action'=>'APPROACH',
            //'next_action'=>'TAKEOFF',
            'previous_state'=>'LANDED',
            'next_state'=>'PARKED'
        ]
        ),

   
];