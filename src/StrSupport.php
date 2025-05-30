<?php

namespace Health;


class StrSupport
{
    
    /**
     * 将路由地址转成路由名称
     * @return string
     */
    public static function routerToName(string $routerUri, string $delimiter = '/')
    {
        return str_replace($delimiter, '.', ltrim($routerUri, '/'));
    }
}
