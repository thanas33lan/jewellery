<?php
$env = getenv('APP_ENV') ?: 'production';

if($env == 'development'){
    $local['db'] = [
        'username' => 'root',
        'password' => 'zaq12345',
    ];
}

if($env == 'testing'){
    $local['db'] = [
        'username' => 'root',
        'password' => 'zaq12345',
    ];
}

if($env == 'production'){
    $local['db'] = [
        'username' => 'root',
        'password' => 'zaq12345',
    ];
}

return $local;