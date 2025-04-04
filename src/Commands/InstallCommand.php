<?php

namespace Yllumi\Heroic\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class InstallCommand extends BaseCommand
{
    protected $group       = 'Heroic';
    protected $name        = 'heroic:install';
    protected $description = 'Install necessary heroic files to project.';

    public function run(array $params)
    {
        // 1. Copy heroic assets ke public/vendor/heroic
        $sourceAssets = realpath(__DIR__ . '/../../resources/dist');
        $targetAssets = FCPATH . 'vendor/heroic/';

        if (!is_dir($targetAssets)) {
            mkdir($targetAssets, 0755, true);
        }

        foreach (glob($sourceAssets . '/*.*') as $file) {
            $filename = basename($file);
            copy($file, $targetAssets . $filename);
            CLI::write("✔ Copied asset: {$filename}", 'green');
        }

        CLI::write('✔ All assets successfully published to public/vendor/heroic/');

        // 2. Copy layouts ke app/Views/layouts/
        $sourceLayouts = realpath(__DIR__ . '/../../templates/layouts');
        $targetLayouts = APPPATH . 'Views/layouts/';

        if (!is_dir($targetLayouts)) {
            mkdir($targetLayouts, 0755, true);
        }

        foreach (glob($sourceLayouts . '/*.php') as $file) {
            $filename = basename($file);
            $targetFile = $targetLayouts . $filename;

            if (!file_exists($targetFile)) {
                copy($file, $targetFile);
                CLI::write("✔ Copied layout: {$filename}", 'green');
            } else {
                CLI::write("⚠ Layout already exists: {$filename}", 'yellow');
            }
        }

        // 3. Pastikan folder app/Pages/ ada
        $pagesFolder = APPPATH . 'Pages/';
        if (!is_dir($pagesFolder)) {
            mkdir($pagesFolder, 0755, true);
            CLI::write("✔ Created folder: Pages/", 'green');
        }

        // 4. Copy BaseController.php, layout.php, router.php ke app/Pages/
        $sourcePages = realpath(__DIR__ . '/../../templates/Pages');
        $filesToCopy = ['BaseController.php', 'layout.php', 'router.php'];

        foreach ($filesToCopy as $file) {
            $sourceFile = $sourcePages . '/' . $file;
            $targetFile = $pagesFolder . $file;

            if (!file_exists($targetFile)) {
                copy($sourceFile, $targetFile);
                CLI::write("✔ Copied page file: {$file}", 'green');
            } else {
                CLI::write("⚠ Page file already exists: {$file}", 'yellow');
            }
        }

        // 5. Copy folder home/, notfound/, whatsnext/
        $pageDirs = ['home', 'notfound', 'whatsnext'];

        foreach ($pageDirs as $dirName) {
            $sourceDir = $sourcePages . '/' . $dirName;
            $targetDir = $pagesFolder . $dirName;

            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
                foreach (glob($sourceDir . '/*.php') as $file) {
                    $filename = basename($file);
                    copy($file, $targetDir . '/' . $filename);
                    CLI::write("✔ Copied {$dirName}/{$filename}", 'green');
                }
            } else {
                CLI::write("⚠ Folder already exists: Pages/{$dirName}", 'yellow');
            }
        }

        CLI::write('🎉 Heroic installation complete!', 'light_blue');
    }
}
