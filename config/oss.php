<?php

return array(
    
    //access_key_id
    'access_key_id'=>env('OSS_ACCESS_KEY_ID','access_key_id'),

    //access_key_secret
    'access_key_secret'=>env('OSS_ACCESS_KEY_SECRET','access_key_secret'),

    //endpoint
    'endpoint'=>env('OSS_ENDPOINT','endpoint'),

    //bucket
    'bucket'=>env('OSS_BUCKET','bucket'),

    //url前缀
    'bucket_prefix'=>env('OSS_BUCKET_PREFIX','bucket_prefix'),

);
