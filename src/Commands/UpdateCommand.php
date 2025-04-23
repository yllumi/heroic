<?php

namespace Yllumi\Heroic\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class UpdateCommand extends BaseCommand
{
    protected $group       = 'Heroic';
    protected $name        = 'heroic:update';
    protected $description = 'Update necessary heroic files to project.';

    public function run(array $params)
    {
        // 1. Copy heroic assets ke public/vendor/heroic
        $sourceAssets = realpath(__DIR__ . '/../../resources/dist');
        $targetAssets = FCPATH . 'vendor/heroic/';

        foreach (glob($sourceAssets . '/*.*') as $file) {
            $filename = basename($file);
            copy($file, $targetAssets . $filename);
            CLI::write("✔ Copied asset: {$filename}", 'green');
        }

        CLI::write('✔ All assets successfully published to public/vendor/heroic/');
    }
}
