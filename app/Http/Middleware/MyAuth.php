<?php
/**
 * -----------------------------------
 * File  : MyAuth.php
 * User  : Mohamed Helal
 * Email : mohamedhelal123456@gmail.com
 * Site  : {URL}
 * -----------------------------------
 */

namespace App\Http\Middleware;


use ArPHP\Http\Request;

class MyAuth
{
    public function handler(Request $request,\Closure $closure){
        return 'FFFF';
    }
}