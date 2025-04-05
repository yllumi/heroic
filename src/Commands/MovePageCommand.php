<?php

namespace Yllumi\Heroic\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use RegexIterator;

class MovePageCommand extends BaseCommand
{
    protected $group       = 'heroic';
    protected $name        = 'heroic:movePage';
    protected $description = 'Move page folder and update namespace and router path.';

    public function run(array $params)
    {
        CLI::write("⚠️  Moving a page will automatically change its path and namespace, but other part of your code might break.", 'yellow');
        CLI::write("    Please make sure to refactor related code manually if necessary.", 'yellow');

        if (CLI::prompt('Do you want to proceed with the move?', ['y', 'n']) !== 'y') {
            CLI::write("❌ Operation cancelled by user.", 'light_gray');
            return;
        }

        $from = $params[0] ?? null;
        $to   = $params[1] ?? null;

        if (!$from || !$to) {
            CLI::error("Gunakan format: php spark heroic:movePage [from] [to]");
            return;
        }

        $fromPath = APPPATH . 'Pages/' . $from;
        $toPath   = APPPATH . 'Pages/' . $to;

        if (!is_dir($fromPath)) {
            CLI::error("Folder halaman asal tidak ditemukan: $fromPath");
            return;
        }

        if (is_dir($toPath)) {
            CLI::error("Folder tujuan sudah ada: $toPath");
            return;
        }

        // Periksa apakah semua file di dalam folder asal writable
        $directory = new RecursiveDirectoryIterator($fromPath);
        $iterator = new RecursiveIteratorIterator($directory);
        foreach ($iterator as $file) {
            if (is_file($file) && !is_writable($file)) {
                CLI::error("File tidak bisa ditulis: $file");
                return;
            }
        }

        rename($fromPath, $toPath);
        CLI::write("Folder berhasil dipindahkan dari $from ke $to", 'green');

        // Update namespace dalam semua file PHP di folder baru
        $this->updateNamespace($from, $to);

        // Update routepath di Router.php
        $this->updateRouterPath($from, $to);
    }

    protected function updateNamespace(string $oldPath, string $newPath)
    {
        $path = APPPATH . 'Pages/' . $newPath;
        $directory = new RecursiveDirectoryIterator($path);
        $iterator = new RecursiveIteratorIterator($directory);
        $regex = new RegexIterator($iterator, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);

        $oldNamespaceBase = 'App\\Pages\\' . str_replace('/', '\\', $oldPath);
        $newNamespaceBase = 'App\\Pages\\' . str_replace('/', '\\', $newPath);

        foreach ($regex as $file => $unused) {
            if (!is_writable($file)) {
                CLI::error("File tidak bisa ditulis: $file");
                continue;
            }

            $content = file_get_contents($file);
            $updatedContent = str_replace($oldNamespaceBase, $newNamespaceBase, $content);

            if ($content !== $updatedContent) {
                file_put_contents($file, $updatedContent);
            }
        }

        CLI::write("Namespace berhasil diperbarui dari $oldNamespaceBase ke $newNamespaceBase", 'green');
    }

    protected function updateRouterPath(string $oldPath, string $newPath)
    {
        $routerFile = APPPATH . 'Pages/Router.php';
        if (!file_exists($routerFile)) {
            CLI::error("File Router.php tidak ditemukan");
            return;
        }

        if (!is_writable($routerFile)) {
            CLI::error("File Router.php tidak bisa ditulis");
            return;
        }

        require_once $routerFile;
        $fullClassName = 'App\\Pages\\Router';

        if (!class_exists($fullClassName) || !property_exists($fullClassName, 'router')) {
            CLI::error("Class App\\Pages\\Router atau properti static \$router tidak ditemukan.");
            return;
        }

        $routerArray = $fullClassName::$router;

        $updated = [];
        foreach ($routerArray as $key => $value) {
            if (str_starts_with($key, "/$oldPath")) {
                $newKey = "/" . preg_replace('#^' . preg_quote($oldPath, '#') . '#', $newPath, ltrim($key, '/'));
                $updated[$newKey] = $value;
            } else {
                $updated[$key] = $value;
            }
        }

        $exportedArray = self::arrayToFormattedString($updated, 2);
        $newContent = <<<PHP
<?php

namespace App\\Pages;

class Router
{
    public static array \$router = {$exportedArray};
}
PHP;

        file_put_contents($routerFile, $newContent);

        CLI::write("Router path berhasil diperbarui dari $oldPath ke $newPath", 'green');
    }

    private static function arrayToFormattedString(array $array, int $indentLevel = 1): string
    {
        $indent = str_repeat('    ', $indentLevel);
        $lines = ["["];

        foreach ($array as $key => $value) {
            $keyStr = var_export($key, true);
            if (is_array($value)) {
                if (empty($value)) {
                    $valueStr = '[]';
                } else {
                    $valueStr = self::arrayToFormattedString($value, $indentLevel + 1);
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
