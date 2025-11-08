<?php

if (!function_exists('is_active')) {
    function is_active($routes)
    {
        if (is_array($routes)) {
            foreach ($routes as $route) {
                if (request()->routeIs($route)) {
                    return true;
                }
            }
            return false;
        }
        return request()->routeIs($routes);
    }
}
