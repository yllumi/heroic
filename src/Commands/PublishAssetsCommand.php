<?php

namespace Yllumi\Heroic\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class PublishAssetsCommand extends BaseCommand
{
    protected $group       = 'Heroic';
    protected $name        = 'heroic:publishAssets';
    protected $description = 'Publish asset heroic to folder public/vendor.';

    public function run(array $params)
    {
        $source = realpath(__DIR__ . '/../../resources/js');
        $target = FCPATH . 'vendor/heroic/';

        if (!is_dir($target)) {
            mkdir($target, 0755, true);
        }

        foreach (glob($source . '/*.js') as $file) {
            $filename = basename($file);
            copy($file, $target . $filename);
            CLI::write("✔ Copied {$filename}", 'green');
        }

        CLI::write('✔ Semua asset berhasil dipublish ke public/vendor/heroic/');
    }
}
