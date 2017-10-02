<?php

/*
|--------------------------------------------------------------------------
| Register Namespaces And Routes
|--------------------------------------------------------------------------
|
| When a module starting, this file will executed automatically. This helps
| to register some namespaces like translator or view. Also this file
| will load the routes file for each module. You may also modify
| this file as you want.
|
*/

if (!app()->routesAreCached()) {
    require __DIR__ . '/Http/routes.php';
}

/**
 * @param $current
 * @param $to
 * @return string
 */
function crudify_route($to, $parameters = null){

    $routeName = request()->route()->getName();

    $segments = explode('.', $routeName);

    array_pop($segments);
    $segments[] = $to;

    if( $parameters ){
        return route(implode('.', $segments),$parameters);
    }

    return route(implode('.', $segments));
}

function crudify_back(){

    $previous = url()->previous();

    if( $previous  == url()->current() ){
        //return crudify_route($current, '')
    }
}