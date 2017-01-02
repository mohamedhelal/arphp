<?php
/**
 * -----------------------------------
 * File  : HttpMiddleware.php
 * User  : Mohamed Helal
 * Email : mohamedhelal123456@gmail.com
 * Site  : {URL}
 * -----------------------------------
 */

namespace App\Http;


use App\Http\Middleware\MyAuth;

class HttpMiddleware extends \ArPHP\Routing\HttpMiddleware
{
    protected $middleware = [
        'MyAuth' => [MyAuth::class,'handler'],
    ];
}