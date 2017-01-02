<?php
/**
 * -----------------------------------
 * File  : HomeController.php
 * User  : Mohamed Helal
 * Email : mohamedhelal123456@gmail.com
 * Site  : {URL}
 * -----------------------------------
 */

namespace Modules\Admin\Http\Controllers;



use ArPHP\Http\Controller;
use ArPHP\Http\Request;


class HomeController extends Controller
{
    public function index(Request $request){
       return __CLASS__;
    }
}