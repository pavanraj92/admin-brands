<?php

namespace admin\brands;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class BrandServiceProvider extends ServiceProvider
{

    public function boot()
    {
        // Load routes, views, migrations from the package  
        $this->loadViewsFrom([
            base_path('Modules/Brands/resources/views'), // Published module views first
            resource_path('views/admin/brand'), // Published views second
            __DIR__ . '/../resources/views'      // Package views as fallback
        ], 'brand');

        $this->mergeConfigFrom(__DIR__.'/../config/brand.php', 'brand.constants');
        
        // Also register module views with a specific namespace for explicit usage
        if (is_dir(base_path('Modules/Brands/resources/views'))) {
            $this->loadViewsFrom(base_path('Modules/Brands/resources/views'), 'brands-module');
        }
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        // Also load migrations from published module if they exist
        if (is_dir(base_path('Modules/Brands/database/migrations'))) {
            $this->loadMigrationsFrom(base_path('Modules/Brands/database/migrations'));
        }

        // Only publish automatically during package installation, not on every request
        // Use 'php artisan brands:publish' command for manual publishing
        // $this->publishWithNamespaceTransformation();
        
        // Standard publishing for non-PHP files
        $this->publishes([
            __DIR__ . '/../database/migrations' => base_path('Modules/Brands/database/migrations'),
            __DIR__ . '/../resources/views' => base_path('Modules/Brands/resources/views/'),
        ], 'brand');
       
        $this->registerAdminRoutes();

    }

    protected function registerAdminRoutes()
    {
        if (!Schema::hasTable('admins')) {
            return; // Avoid errors before migration
        }

        $admin = DB::table('admins')
            ->orderBy('created_at', 'asc')
            ->first();
            
        $slug = $admin->website_slug ?? 'admin';

        Route::middleware('web')
            ->prefix("{$slug}/admin") // dynamic prefix
            ->group(function () {
                $this->loadRoutesFrom(__DIR__.'/routes/web.php');
            });
    }

    public function register()
    {
        // Register the publish command
        if ($this->app->runningInConsole()) {
            $this->commands([
                \admin\brands\Console\Commands\PublishBrandsModuleCommand::class,
                \admin\brands\Console\Commands\CheckModuleStatusCommand::class,
                \admin\brands\Console\Commands\DebugBrandsCommand::class,
                \admin\brands\Console\Commands\TestViewResolutionCommand::class,
            ]);
        }
    }

    /**
     * Publish files with namespace transformation
     */
    protected function publishWithNamespaceTransformation()
    {
        // Define the files that need namespace transformation
        $filesWithNamespaces = [
            // Controllers
            __DIR__ . '/../src/Controllers/BrandManagerController.php' => base_path('Modules/Brands/app/Http/Controllers/Admin/BrandManagerController.php'),
            
            // Models
            __DIR__ . '/../src/Models/Brand.php' => base_path('Modules/Brands/app/Models/Brand.php'),
            
            // Requests
            __DIR__ . '/../src/Requests/BrandCreateRequest.php' => base_path('Modules/Brands/app/Http/Requests/BrandCreateRequest.php'),
            __DIR__ . '/../src/Requests/BrandUpdateRequest.php' => base_path('Modules/Brands/app/Http/Requests/BrandUpdateRequest.php'),
            
            // Routes
            __DIR__ . '/routes/web.php' => base_path('Modules/Brands/routes/web.php'),
        ];

        foreach ($filesWithNamespaces as $source => $destination) {
            if (File::exists($source)) {
                // Create destination directory if it doesn't exist
                File::ensureDirectoryExists(dirname($destination));
                
                // Read the source file
                $content = File::get($source);
                
                // Transform namespaces based on file type
                $content = $this->transformNamespaces($content, $source);
                
                // Write the transformed content to destination
                File::put($destination, $content);
            }
        }
    }

    /**
     * Transform namespaces in PHP files
     */
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
            $content = $this->transformControllerNamespaces($content);
        } elseif (str_contains($sourceFile, 'Models')) {
            $content = $this->transformModelNamespaces($content);
        } elseif (str_contains($sourceFile, 'Requests')) {
            $content = $this->transformRequestNamespaces($content);
        } elseif (str_contains($sourceFile, 'routes')) {
            $content = $this->transformRouteNamespaces($content);
        }

        return $content;
    }

    /**
     * Transform controller-specific namespaces
     */
    protected function transformControllerNamespaces($content)
    {
        // Update use statements for models and requests
        $content = str_replace(
            'use admin\\brands\\Models\\Brand;',
            'use Modules\\Brands\\app\\Models\\Brand;',
            $content
        );
        
        $content = str_replace(
            'use admin\\brands\\Requests\\BrandCreateRequest;',
            'use Modules\\Brands\\app\\Http\\Requests\\BrandCreateRequest;',
            $content
        );
        
        $content = str_replace(
            'use admin\\brands\\Requests\\BrandUpdateRequest;',
            'use Modules\\Brands\\app\\Http\\Requests\\BrandUpdateRequest;',
            $content
        );

        return $content;
    }

    /**
     * Transform model-specific namespaces
     */
    protected function transformModelNamespaces($content)
    {
        // Any model-specific transformations
        return $content;
    }

    /**
     * Transform request-specific namespaces
     */
    protected function transformRequestNamespaces($content)
    {
        // Any request-specific transformations
        return $content;
    }

    /**
     * Transform route-specific namespaces
     */
    protected function transformRouteNamespaces($content)
    {
        // Update controller references in routes
        $content = str_replace(
            'admin\\brands\\Controllers\\BrandManagerController',
            'Modules\\Brands\\app\\Http\\Controllers\\Admin\\BrandManagerController',
            $content
        );

        return $content;
    }
}
