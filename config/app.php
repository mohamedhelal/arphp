<?php
/**
 * -----------------------------------
 * File  : app.php
 * User  : Mohamed Helal
 * Email : mohamedhelal123456@gmail.com
 * Site  : {URL}
 * -----------------------------------
 */

return [

    /**
     * application url
     */
    'url' => '',
    /**
     * use Query String
     */
    'queryString' => false,
    /**
     * Secure Key For application
     */
    'SECURE_KEY' => 'pp8mxC10mKu:Tt!8{L5Hl.VT=yhZqgHylv2#a&Gs&;pq;y;+F}8% .r_#x{D8B]?',
    'cipher' => 'pp8mxC10mKu:Tt!8{L5Hl.VT=yhZqgHylv2#a&Gs&;pq;y;+F}8% .r_#x{D8B]?',
    /**
     * all services
     */
    'services' => [
        \ArPHP\Databases\DatabasesService::class,
        \ArPHP\Sessions\SessionServices::class,
        \ArPHP\Filesystem\FilesystemService::class,
        \ArPHP\Html\HtmlService::class,
        \App\Services\RouteServices::class,
        \App\Services\ModulesServices::class,
    ],
    /**
     * all namespaces
     */
    'namespaces' => [],
    /**
     * all classes aliases
     */
    'aliases' => [
        'Route' => \ArPHP\Routing\Route::class,
        'Arr' => \ArPHP\Support\Arr::class,
        'Curl' =>\ArPHP\Support\Curl::class,
        'Crypter' => \ArPHP\Encryption\Crypter::class,
        'Validation' => \ArPHP\Validation\Validation::class,
        'Pagination' => \ArPHP\Pagination\Pagination::class,
        'Config' => \ArPHP\Support\Statics\Config::class,
        'Lang' => \ArPHP\Support\Statics\Lang::class,
        'Session' => \ArPHP\Support\Statics\Session::class,
        'DB' => \ArPHP\Databases\DB::class,
    ],
];