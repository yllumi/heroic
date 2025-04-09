<?php

namespace App\Pages;

class Router
{
    public static array $router = [
        "/" => [
            'template' => '/home/template',
            'preload'  => true
        ],
        "/offline" => [
            'template' => '/offline/template',
            'preload'  => true
        ],
        "notfound" => [
            'template' => '/notfound/template',
            'preload'  => true
        ],
        "/whatsnext" => [],
        '/whatsnext/sample' => [],
        '/whatsnext/sample/todo_session' => [],
        '/whatsnext/sample/todo_db' => [],

    ];
}