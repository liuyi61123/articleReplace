<?php

return [
    '5118'=>[
        'base_url'=>env('COM5118_BASE_URL','http://apis.5118.com/'),
        'api'     =>env('COM5118_API','/wyc/akey'),
        'key'     =>env('COM5118_KEY',''),
    ],
    'naipan'=>[
        'base_url'  =>env('NAIPAN_BASE_URL','http://www.naipan.com/'),
        'api'       =>env('NAIPAN_API','/open/weiyuanchuang/towei.html'),
        'regname'   =>env('MAIPAN_REGNAME',''),
        'regsn'     =>env('NAIPAN_REGSN',''),
    ]
];