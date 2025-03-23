<?php

/**
 * This file is part of yllumi/ci4-pages.
 *
 * (c) 2024 Toni Haryanto <toha.samba@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Yllumi\Heroic\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CreatePageCommand extends BaseCommand
{
    protected $group       = 'Heroic'; // Group command
    protected $name        = 'heroic:createPage'; // Nama command
    protected $description = 'Create a new page';

    /**
     * @param list<string> $params
     */
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

        // Path to templates
        $templatePath = dirname(__DIR__) . '/templates';

        // Create the folder if it doesn't exist
        if (! is_dir($basePath)) {
            mkdir($basePath, 0755, true);
            CLI::write("Folder created: {$basePath}", 'green');
        } else {
            CLI::write("Folder already exists: {$basePath}", 'yellow');
        }

        $faker = \Faker\Factory::create();
        
        // Create the PageController.php file
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

        // Create the index.php file
        $this->createFileFromTemplate(
            "{$templatePath}/template" . ( $createScript ? '.withscript' : '' ) . ".php.tpl",
            $viewPath,
            [
                '{{pagePath}}' => $pagePath,
                '{{pageSlug}}' => str_replace('/', '_', $pagePath),
            ],
        );

        // Add route
        $routerFile = APPPATH . 'Pages/router.php';
        $routerCode = file_get_contents($routerFile);

        $pos = strpos($routerCode, '$router = [');
        if ($pos === false) {
            CLI::error('❌ Tidak menemukan variabel $router.');
            return;
        }
        $newEntry = "\n    \"$pagePath\" => [ ]";
        $updated = preg_replace('/\];\s/', "$newEntry,\n];", $routerCode, 1);

        file_put_contents($routerFile, $updated);
        CLI::write("✅ Route '$pagePath' berhasil ditambahkan ke router.php", 'green');
    }

    /**
     * Create a file from a template, replacing placeholders.
     *
     * @param array<string, string> $replacements
     *
     * @return void
     */
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

        /**
         * Read template content
         *
         * @var string $content
         */
        $content = file_get_contents($templateFile);

        // Replace placeholders
        $content = str_replace(array_keys($replacements), array_values($replacements), $content);

        // Write to target file
        file_put_contents($targetFile, $content);
        CLI::write("File created: {$targetFile}", 'green');
    }
}
