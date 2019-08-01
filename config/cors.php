<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Laravel CORS
    |--------------------------------------------------------------------------
    |
    | allowedOrigins, allowedHeaders and allowedMethods can be set to array('*')
    | to accept any value.
    |
    */
    'supportsCredentials' => false,
    'allowedOrigins' => ['*'],
    'allowedHeaders' => ['*', 'XMLHttpRequest', 'authorization'],
    'allowedMethods' => ['*'], // ex: ['GET', 'POST', 'PUT',  'DELETE']
    'exposedHeaders' => ['Authorization', 'XMLHttpRequest'],
    'maxAge' => 0,

];
