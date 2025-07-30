<?php

namespace admin\brands\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class PublishBrandsModuleCommand extends Command
{
    protected $signature = 'brands:publish {--force : Force overwrite existing files}';
    protected $description = 'Publish Brands module files with proper namespace transformation';

    public function handle()
    {
        $this->info('Publishing Brands module files...');

        // Check if module directory exists
        $moduleDir = base_path('Modules/Brands');
        if (!File::exists($moduleDir)) {
            File::makeDirectory($moduleDir, 0755, true);
        }

        // Publish with namespace transformation
        $this->publishWithNamespaceTransformation();
        
        // Publish other files
        $this->call('vendor:publish', [
            '--tag' => 'brand',
            '--force' => $this->option('force')
        ]);

        // Update composer autoload
        $this->updateComposerAutoload();

        $this->info('Brands module published successfully!');
        $this->info('Please run: composer dump-autoload');
    }

    protected function publishWithNamespaceTransformation()
    {
        $basePath = dirname(dirname(__DIR__)); // Go up to packages/admin/brands/src
        
        $filesWithNamespaces = [
            // Controllers
            $basePath . '/Controllers/BrandManagerController.php' => base_path('Modules/Brands/app/Http/Controllers/Admin/BrandManagerController.php'),
            
            // Models
            $basePath . '/Models/Brand.php' => base_path('Modules/Brands/app/Models/Brand.php'),
            
            // Requests
            $basePath . '/Requests/BrandCreateRequest.php' => base_path('Modules/Brands/app/Http/Requests/BrandCreateRequest.php'),
            $basePath . '/Requests/BrandUpdateRequest.php' => base_path('Modules/Brands/app/Http/Requests/BrandUpdateRequest.php'),
            
            // Routes
            $basePath . '/routes/web.php' => base_path('Modules/Brands/routes/web.php'),
        ];

        foreach ($filesWithNamespaces as $source => $destination) {
            if (File::exists($source)) {
                File::ensureDirectoryExists(dirname($destination));
                
                $content = File::get($source);
                $content = $this->transformNamespaces($content, $source);
                
                File::put($destination, $content);
                $this->info("Published: " . basename($destination));
            } else {
                $this->warn("Source file not found: " . $source);
            }
        }
    }

    protected function transformNamespaces($content, $sourceFile)
    {
        // Define namespace mappings
        $namespaceTransforms = [
            // Main namespace transformations
            'namespace admin\\brands\\Controllers;' => 'namespace Modules\\Brands\\app\\Http\\Controllers\\Admin;',
            'namespace admin\\brands\\Models;' => 'namespace Modules\\Brands\\app\\Models;',
            'namespace admin\\brands\\Requests;' => 'namespace Modules\\Brands\\app\\Http\\Requests;',
            
            // Use statements transformations
            'use admin\\brands\\Controllers\\' => 'use Modules\\Brands\\app\\Http\\Controllers\\Admin\\',
            'use admin\\brands\\Models\\' => 'use Modules\\Brands\\app\\Models\\',
            'use admin\\brands\\Requests\\' => 'use Modules\\Brands\\app\\Http\\Requests\\',
            
            // Class references in routes
            'admin\\brands\\Controllers\\BrandManagerController' => 'Modules\\Brands\\app\\Http\\Controllers\\Admin\\BrandManagerController',
        ];

        // Apply transformations
        foreach ($namespaceTransforms as $search => $replace) {
            $content = str_replace($search, $replace, $content);
        }

        // Handle specific file types
        if (str_contains($sourceFile, 'Controllers')) {
            $content = str_replace('use admin\\brands\\Models\\Brand;', 'use Modules\\Brands\\app\\Models\\Brand;', $content);
            $content = str_replace('use admin\\brands\\Requests\\BrandCreateRequest;', 'use Modules\\Brands\\app\\Http\\Requests\\BrandCreateRequest;', $content);
            $content = str_replace('use admin\\brands\\Requests\\BrandUpdateRequest;', 'use Modules\\Brands\\app\\Http\\Requests\\BrandUpdateRequest;', $content);
        }

        return $content;
    }

    protected function updateComposerAutoload()
    {
        $composerFile = base_path('composer.json');
        $composer = json_decode(File::get($composerFile), true);

        // Add module namespace to autoload
        if (!isset($composer['autoload']['psr-4']['Modules\\Brands\\'])) {
            $composer['autoload']['psr-4']['Modules\\Brands\\'] = 'Modules/Brands/app/';
            
            File::put($composerFile, json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            $this->info('Updated composer.json autoload');
        }
    }
}
