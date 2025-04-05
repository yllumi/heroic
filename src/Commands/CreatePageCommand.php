<?php

namespace Yllumi\Heroic\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CreatePageCommand extends BaseCommand
{
    protected $group       = 'Heroic';
    protected $name        = 'heroic:createPage';
    protected $description = 'Create a new page';

    public function run(array $params)
    {
        if ($params === []) {
            CLI::error('Please specify the page name.');
            return;
        }

        $pagePath       = $params[0];
        $options        = service('request')->getOptions();
        $createScript   = array_key_exists('script', $options) || array_key_exists('s', $options) ? true : false;
        $basePath       = APPPATH . "Pages/{$pagePath}";
        $controllerPath = "{$basePath}/PageController.php";
        $viewPath       = "{$basePath}/template.php";

        $templatePath = dirname(__DIR__) . '/templates';

        if (! is_dir($basePath)) {
            mkdir($basePath, 0755, true);
            CLI::write("Folder created: {$basePath}", 'green');
        } else {
            CLI::write("Folder already exists: {$basePath}", 'yellow');
        }

        $faker = \Faker\Factory::create();

        $this->createFileFromTemplate(
            "{$templatePath}/PageController.php.tpl",
            $controllerPath,
            [
                '{{pagePath}}'      => $pagePath,
                '{{pageName}}'      => ucwords(str_replace('/', ' ', $pagePath)),
                '{{pageNamespace}}' => str_replace('/', '\\', $pagePath),
                '{{fakerName}}'     => $faker->name()
            ],
        );

        $this->createFileFromTemplate(
            "{$templatePath}/template" . ($createScript ? '.withscript' : '') . ".php.tpl",
            $viewPath,
            [
                '{{pagePath}}' => $pagePath,
                '{{pageSlug}}' => str_replace('/', '_', $pagePath),
            ],
        );

        // Load and update router from Router.php
        $routerFile = APPPATH . 'Pages/Router.php';
        if (!file_exists($routerFile)) {
            CLI::error("Router.php file not found");
            return;
        }

        if (!is_writable($routerFile)) {
            CLI::error("Router.php file is not writable");
            return;
        }

        require_once $routerFile;
        $routerClass = 'App\\Pages\\Router';

        if (!class_exists($routerClass) || !property_exists($routerClass, 'router')) {
            CLI::error("Class App\\Pages\\Router or static \$router property not found");
            return;
        }

        $router = $routerClass::$router;
        $routeKey = "/$pagePath";

        if (!array_key_exists($routeKey, $router)) {
            $router[$routeKey] = [];
        } else {
            CLI::write("Route already exists in Router.php", 'yellow');
        }

        $exportedArray = $this->arrayToFormattedString($router, 2);
        $newContent = <<<PHP
<?php

namespace App\\Pages;

class Router
{
    public static array \$router = {$exportedArray};
}
PHP;

        file_put_contents($routerFile, $newContent);
        CLI::write("✅ Route '$pagePath' has been added to Router.php", 'green');
    }

    private function createFileFromTemplate(string $templateFile, string $targetFile, array $replacements)
    {
        if (! file_exists($templateFile)) {
            CLI::error("Template not found: {$templateFile}");
            return;
        }

        if (file_exists($targetFile)) {
            CLI::write("File already exists: {$targetFile}", 'yellow');
            return;
        }

        $content = file_get_contents($templateFile);
        $content = str_replace(array_keys($replacements), array_values($replacements), $content);
        file_put_contents($targetFile, $content);
        CLI::write("File created: {$targetFile}", 'green');
    }

    private function arrayToFormattedString(array $array, int $indentLevel = 1): string
    {
        $indent = str_repeat('    ', $indentLevel);
        $lines = ["["];

        foreach ($array as $key => $value) {
            $keyStr = var_export($key, true);
            if (is_array($value)) {
                if (empty($value)) {
                    $valueStr = '[]';
                } else {
                    $valueStr = $this->arrayToFormattedString($value, $indentLevel + 1);
                }
            } else {
                $valueStr = var_export($value, true);
            }
            $lines[] = "{$indent}{$keyStr} => {$valueStr},";
        }

        $lines[] = str_repeat('    ', $indentLevel - 1) . "]";
        return implode("\n", $lines);
    }
}
