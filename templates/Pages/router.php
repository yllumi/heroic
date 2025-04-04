<?php

$router = [
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
    "whatsnext" => [ ],
];

echo ltrim(renderRouter($router));