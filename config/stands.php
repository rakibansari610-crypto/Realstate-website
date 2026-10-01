<?php

return [
    [
        'id' => 1,
        'name' => 'Energizer Floor Stand',
        'template' => public_path('stands/stand1.jpeg'),   // base stand image
        'mask' => public_path('stands/stand1_mask.png'),  // transparent area where logo should appear
        'hint' => 'Place the uploaded logo on the front-left vertical panel area.',
    ],
    [
        'id' => 2,
        'name' => 'Wire Rack Stand',
        'template' => public_path('stands/stand2.png'),
        'mask' => public_path('stands/stand2_mask.png'),
        'hint' => 'Place the uploaded logo on the top header sign area.',
    ],
];
