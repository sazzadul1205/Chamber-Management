<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeJsxComponent extends Command
{
  protected $signature = 'make:jsx {path}';
  protected $description = 'Create a new JSX component with full path';

  public function handle()
  {
    $fullPath = $this->argument('path');

    // Remove .jsx if accidentally included
    $fullPath = str_replace('.jsx', '', $fullPath);

    // Extract component name and directory from path
    $parts = explode('/', $fullPath);
    $componentName = array_pop($parts);
    $directory = implode('/', $parts);

    // If no directory specified, use Components as default
    if (empty($directory)) {
      $directory = 'Components';
      $this->info("No directory specified, using default: {$directory}");
    }

    // Build full path
    $path = resource_path("js/{$directory}/{$componentName}.jsx");

    // Create directory if it doesn't exist
    $dirPath = dirname($path);
    if (!File::exists($dirPath)) {
      File::makeDirectory($dirPath, 0755, true);
      $this->info("Created directory: {$dirPath}");
    }

    // Check if file already exists
    if (File::exists($path)) {
      if (!$this->confirm("File {$componentName}.jsx already exists. Overwrite?")) {
        $this->error("Component creation cancelled.");
        return;
      }
    }

    // Get stub content
    $stubContent = $this->getStubContent();

    // Replace placeholders
    $content = str_replace('ComponentName', $componentName, $stubContent);

    // Create the file
    File::put($path, $content);

    $this->info("✅ JSX component created successfully!");
    $this->line("📁 Location: resources/js/{$directory}/{$componentName}.jsx");
  }

  protected function getStubContent()
  {
    $stubPath = base_path('stubs/jsx/react-component.stub');

    if (File::exists($stubPath)) {
      return File::get($stubPath);
    }

    // Fallback to default stub
    return <<<'EOD'
import React from "react";

const ComponentName = () => {
  return (
    <div className="component-name">
      <h1>ComponentName Component</h1>
      <p>This component was created with php artisan make:jsx</p>
    </div>
  );
};

export default ComponentName;
EOD;
  }
}
