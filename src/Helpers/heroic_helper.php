<?php 


function renderRouter($router = [], $minify = false)
{
    $routerString = "";

    foreach($router as $route => $routeProp) 
    {
        $routePath = is_string($routeProp) ? $routeProp : $route;
        
        $routerString .= "<template \nx-route=\"{$routePath}\" \n";
        
        $routerString .= "x-template". ($routeProp['preload'] ?? false ? ".preload" : "") . ($routeProp['interpolate'] ?? false ? ".interpolate" : "") . "=\"";
        
        // Print template
        if(isset($routeProp['template'])) 
        {
            
            if(is_array($routeProp['template'])){
                $routerString .= str_replace(['"','\/'], ["'",'/'], json_encode($routeProp['template']));
            } else {
                $routerString .= $routeProp['template'];
            }
            $routerString .= "\" \n";
        } else {
            $cleanedPath = preg_replace('/:([^\/]+)/', '', $routePath); // Hapus :param
            $cleanedPath = rtrim($cleanedPath, '/'); // Hapus trailing slash
            if($cleanedPath === '') {
                $cleanedPath = '/home';
            }

            $routerString .= $cleanedPath . "/template\" \n";
        }
        
        // Print handpler
        if(isset($routeProp['handler']))
        {
            $routerString .= "x-handler=\"" . $routeProp['handler'] . "\"";
        }
        
        $routerString .= "></template>\n\n";
    }

    return $minify ? str_replace("\n", "", $routerString) : $routerString;
}