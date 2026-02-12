<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ComponentController extends Controller
{
  public function checkCustomFolder()
  {
    $customPath = resource_path('js/Pages/Home/Custom');

    return response()->json([
      'exists' => File::exists($customPath)
    ]);
  }

  public function saveComponent(Request $request)
  {
    $request->validate([
      'componentName' => 'required|string|regex:/^[A-Z][a-zA-Z0-9]*$/',
      'componentCode' => 'required|string',
      'html' => 'required|string',
      'jsx' => 'required|string',
    ]);

    try {
      // Define the path
      $homePath = resource_path('js/Pages/Home');
      $customPath = $homePath . '/Custom';

      // Create Home directory if it doesn't exist
      if (!File::exists($homePath)) {
        File::makeDirectory($homePath, 0755, true);
      }

      // Create Custom directory if it doesn't exist
      if (!File::exists($customPath)) {
        File::makeDirectory($customPath, 0755, true);
      }

      // Define the file path
      $fileName = $request->componentName . '.jsx';
      $filePath = $customPath . '/' . $fileName;

      // Check if file already exists
      if (File::exists($filePath)) {
        return response()->json([
          'success' => false,
          'message' => 'A component with this name already exists. Please choose a different name.'
        ], 422);
      }

      // Save the component file
      File::put($filePath, $request->componentCode);

      // Optional: Save component metadata to database
      // $component = Component::create([
      //     'name' => $request->componentName,
      //     'file_path' => 'js/Pages/Home/Custom/' . $fileName,
      //     'html' => $request->html,
      //     'jsx' => $request->jsx,
      // ]);

      return response()->json([
        'success' => true,
        'message' => 'Component saved successfully!',
        'path' => 'js/Pages/Home/Custom/' . $fileName,
        'componentName' => $request->componentName
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'Failed to save component: ' . $e->getMessage()
      ], 500);
    }
  }

  public function getCustomComponents()
  {
    $customPath = resource_path('js/Pages/Home/Custom');

    if (!File::exists($customPath)) {
      return response()->json([
        'components' => []
      ]);
    }

    $files = File::files($customPath);
    $components = [];

    foreach ($files as $file) {
      if ($file->getExtension() === 'jsx') {
        $components[] = [
          'name' => $file->getFilenameWithoutExtension(),
          'filename' => $file->getFilename(),
          'path' => 'js/Pages/Home/Custom/' . $file->getFilename(),
          'modified' => $file->getMTime(),
          'size' => $file->getSize()
        ];
      }
    }

    return response()->json([
      'components' => $components
    ]);
  }

  public function deleteComponent($name)
  {
    try {
      $filePath = resource_path('js/Pages/Home/Custom/' . $name . '.jsx');

      if (!File::exists($filePath)) {
        return response()->json([
          'success' => false,
          'message' => 'Component not found'
        ], 404);
      }

      File::delete($filePath);

      return response()->json([
        'success' => true,
        'message' => 'Component deleted successfully!'
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'Failed to delete component: ' . $e->getMessage()
      ], 500);
    }
  }
}
