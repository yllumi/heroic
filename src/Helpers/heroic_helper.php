<?php 


function renderRouter($router = [], $minify = false)
{
    $routerString = "";

    foreach ($router as $route => $routeProp) {
        $routePath = is_string($routeProp) ? $routeProp : $route;
        $routerString .= "<template \nx-route=\"{$routePath}\" \n";

        // Siapkan nilai template
        $templateStr = "";
        $hasParamInTemplate = false;

        if (isset($routeProp['template'])) {
            if (is_array($routeProp['template'])) {
                $templateStr = str_replace(['"','\/'], ["'",'/'], json_encode($routeProp['template']));
                $hasParamInTemplate = preg_match('/:([^\/]+)/', implode('', $routeProp['template']));
            } else {
                $templateStr = $routeProp['template'];
                $hasParamInTemplate = preg_match('/:([^\/]+)/', $templateStr);
            }
        } else {
            // Default template jika tidak disediakan
            $cleanedPath = "/" . trim(preg_replace('/:([^\/]+)/', '', $routePath), '/');
            $cleanedPath = $cleanedPath === '/' ? '/home' : $cleanedPath;
            $templateStr = $cleanedPath . "/template";
        }

        $routerString .= "x-template"
            . (($routeProp['preload'] ?? false) ? ".preload" : "")
            . ($hasParamInTemplate ? ".interpolate" : "")
            . "=\"{$templateStr}\" \n";

        if (isset($routeProp['handler'])) {
            $routerString .= "x-handler=\"" . $routeProp['handler'] . "\"";
        }

        $routerString .= "></template>\n\n";
    }

    return $minify ? str_replace("\n", "", $routerString) : $routerString;
}

function heroic($title = null, $param_names = null, $fetch_url = null, $meta = [])
{
    $pageUri = $fetch_url;
    if(!$pageUri) {
        $uri = uri_string();
        $uriSegments = explode('/', $uri);
        array_pop($uriSegments);
        $pageUri = implode('/', $uriSegments) . '/data';
    }

    // Set heroic params to url
    if($param_names) {
        if(is_string($param_names)) {
            $param_names = explode(',', $param_names);
        }
        foreach($param_names as $param) {
            $pageUri .= '/${$params.' . $param . '}';
        }
    }

    // Print js function 
    echo "\$heroic({
        title: `{$title}`, 
        url: `/{$pageUri}`,
        meta: " . htmlspecialchars(json_encode($meta, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8') . "
    })";
}