<?php

namespace admin\brands\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\View;

class TestViewResolutionCommand extends Command
{
    protected $signature = 'brands:test-views';
    protected $description = 'Test view resolution for Brands module';

    public function handle()
    {
        $this->info('🔍 Testing View Resolution for Brands Module...');
        
        // Test views to check
        $testViews = [
            'admin.index',
            'admin.createOrEdit',
            'admin.show',
        ];
        
        foreach ($testViews as $viewName) {
            $this->info("\n📄 Testing view: {$viewName}");
            
            // Test different namespaces
            $namespaces = [
                'brands-module::' . $viewName => 'Module View',
                'brand::' . $viewName => 'Package View',
            ];
            
            foreach ($namespaces as $fullPath => $description) {
                try {
                    if (View::exists($fullPath)) {
                        $this->info("  ✅ {$description}: EXISTS - {$fullPath}");
                        
                        // Get the actual file path
                        try {
                            $finder = app('view')->getFinder();
                            $path = $finder->find($fullPath);
                            $this->line("     File: {$path}");
                            $this->line("     Modified: " . date('Y-m-d H:i:s', filemtime($path)));
                        } catch (\Exception $e) {
                            $this->line("     Path resolution failed: {$e->getMessage()}");
                        }
                    } else {
                        $this->warn("  ❌ {$description}: NOT FOUND - {$fullPath}");
                    }
                } catch (\Exception $e) {
                    $this->error("  ❌ {$description}: ERROR - {$e->getMessage()}");
                }
            }
        }
        
        // Test the dynamic resolution method
        $this->info("\n🎯 Testing Dynamic View Resolution:");
        $controller = new \Modules\Brands\app\Http\Controllers\Admin\BrandManagerController();
        
        foreach ($testViews as $viewName) {
            try {
                $reflection = new \ReflectionClass($controller);
                $method = $reflection->getMethod('getViewPath');
                $method->setAccessible(true);
                
                $resolvedPath = $method->invoke($controller, $viewName);
                $this->info("  📍 {$viewName} → {$resolvedPath}");
                
                if (View::exists($resolvedPath)) {
                    $this->info("    ✅ Resolved view exists");
                } else {
                    $this->error("    ❌ Resolved view does not exist");
                }
            } catch (\Exception $e) {
                $this->error("  ❌ Error testing {$viewName}: {$e->getMessage()}");
            }
        }
        
        $this->info("\n📋 View Loading Order:");
        $this->info("1. brands-module:: (Module views - highest priority)");
        $this->info("2. brand:: (Package views - fallback)");
        
        $this->info("\n💡 Tips:");
        $this->info("- Edit views in Modules/Brands/resources/views/ to use module views");
        $this->info("- Module views will automatically take precedence over package views");
        $this->info("- If module view doesn't exist, it will fallback to package view");
    }
}
