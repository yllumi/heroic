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
        /**
         * Copy heroic.js to public/
         */
        $source = realpath(__DIR__ . '/../../resources/dist');
        $target = FCPATH . 'vendor/heroic/';

        if (!is_dir($target)) {
            mkdir($target, 0755, true);
        }

        foreach (glob($source . '/*.js') as $file) {
            $filename = basename($file);
            copy($file, $target . $filename);
            CLI::write("✔ Copied {$filename}", 'green');
        }

        CLI::write('✔ All assets successfully published to public/vendor/heroic/');

        /**
         * TODO: Copy resources/layouts/ folder file to app/Views/
         */

        /**
         * TODO: Create folder app/Pages/
         */

        /**
         * TODO: Copy layout.php, router.php and HeroicController.php from resources/Pages/ to app/Pages/
         */


    }
}
