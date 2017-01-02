<?php
/**
 * -----------------------------------
 * File  : helper.php
 * User  : Mohamed Helal
 * Email : mohamedhelal123456@gmail.com
 * Site  : {URL}
 * -----------------------------------
 */
/**
 * get app object
 * @return \ArPHP\Application\Application|mixed
 */
function &app()
{
    $instance = &\ArPHP\Application\Application::getInstance();
    $args = array_filter(func_get_args());
    if (count($args)) {
        return call_user_func_array([$instance, 'make'], $args);
    }
    return $instance;
}

/**
 * get config item
 * @param null $key
 * @param bool $default
 * @param bool $force
 * @return mixed
 */
function config($key = null, $default = false, $force = false)
{
    if (count(func_get_args()) == 0) {
        return app()->config;
    }
    if (is_array($key)) {
        foreach ($key as $name => $value) {
            app()->config->set($name, $value);
        }
    } else {
        return app()->config->get($key, $default, $force);
    }
}

/**
 * @param $key
 * @param array $replace
 * @return mixed
 */
function lang($key = false, $replace = [])
{
    if (count(func_get_args()) == 0) {
        return app()->translate;
    }
    return app()->translate->get($key, $replace);
}

/**
 * get Request
 * @return \ArPHP\Http\Request
 */
function request()
{
    return app(\ArPHP\Http\Request::class);
}

/**
 * get Response
 * @return \ArPHP\Http\Response
 */
function response()
{
    return app(\ArPHP\Http\Response::class);
}

/**
 * get Redirect class
 * @return \ArPHP\Http\Redirect
 */
function redirect(){
    return app(\ArPHP\Http\Redirect::class);
}
/**
 * get route by name
 * @param $name
 * @param array $parameters
 * @param null $route
 * @return mixed
 */
function route($name, $parameters = [], $route = null)
{
    return app(\ArPHP\Http\UrlGenerator::class)->route($name, $parameters, $route);
}

/**
 * get route by action
 * @param $action
 * @param array $parameters
 * @return mixed
 */
function action($action, $parameters = [])
{
    return app(\ArPHP\Http\UrlGenerator::class)->action($action, $parameters);
}

/**
 * @param bool $url
 * @param bool $secure
 * @return mixed
 */
function url($url = false, $secure = false)
{
    if (count(func_get_args()) == 0) {
        return app(\ArPHP\Http\UrlGenerator::class);
    }
    return app(\ArPHP\Http\UrlGenerator::class)->url($url, $secure);
}

/**
 * @param bool $path
 * @param bool $secure
 * @return mixed
 */
function base_url($path = false, $secure = false)
{
    return url()->base($path, $secure);
}
/**
 * get  base path
 * @return bool|mixed
 */
function base_path()
{
    return app()->path('basePath');
}
/**
 * get  public path
 * @return bool|mixed
 */
function public_path()
{
    return app()->path('public');
}
/**
 * get  public url
 * @return bool|mixed
 */
function public_url()
{
    return base_url(public_path());
}

/**
 * get  app path
 * @return bool|mixed
 */
function app_path()
{
    return app()->path('app');
}

/**
 * get  app url
 * @return bool|mixed
 */
function app_url()
{
    return base_url(app_path());
}

/**
 * get  config path
 * @return bool|mixed
 */
function config_path()
{
    return app()->path('config');
}


/**
 * get  resources path
 * @return bool|mixed
 */
function resources_path()
{
    return app()->path('resources');
}

/**
 * get  resources url
 * @return bool|mixed
 */
function resources_url()
{
    return base_url(resources_path());
}

/**
 * get  storage path
 * @return bool|mixed
 */
function storage_path()
{
    return app()->path('storage');
}

/**
 * get  storage url
 * @return bool|mixed
 */
function storage_url()
{
    return base_url(storage_path());
}

/**
 * get  modules path
 * @return bool|mixed
 */
function modules_path()
{
    return app()->path('modules');
}

/**
 * get  modules url
 * @return bool|mixed
 */
function modules_url()
{
    return base_url(modules_path());
}

/**
 * get  lang path
 * @return bool|mixed
 */
function lang_path()
{
    return app()->path('lang');
}


/**
 * get  view path
 * @return bool|mixed
 */
function view_path()
{
    return app()->path('view');
}

/**
 * get  view url
 * @return bool|mixed
 */
function view_url()
{
    return base_url(view_path());
}

/**
 * get  assets path
 * @return bool|mixed
 */
function assets_path()
{
    return app()->path('assets');
}

/**
 * get  assets url
 * @return bool|mixed
 */
function assets_url()
{
    return base_url(assets_path());
}
