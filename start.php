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
 * @param $to
 * @param $parameters
 * @param null $routeName
 * @return string
 */
function crud_route($to, $parameters = null, $routeName = null)
{
    if (!$routeName) {
        $routeName = session('crud-route-name') ?? request()->route()->getName();
    }

    $namespace = '';
    $hasNamespace = strpos($routeName, '::');
    if ($hasNamespace) {
        $namespaceSegments = explode('::', $routeName);
        $namespace = array_get($namespaceSegments, 0) . '::';
    }

    $routeName = str_replace($namespace, '', $routeName);
    $segments = explode('.', $routeName);
    array_pop($segments);

    $segments[] = $to;

    $routeName = $namespace . implode('.', $segments);

    if ($parameters) {
        return route($routeName, $parameters);
    }

    return route($routeName);
}
