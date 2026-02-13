<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageConfig;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\File;

class PageController extends Controller
{
    public function edit()
    {
        $config = PageConfig::getHomeConfig();

        return Inertia::render('Admin/PageBuilder/index', [
            'sections' => config('sections.sections'),
            'layoutConfig' => $config['layout_config'] ?? [],
            'sectionSettings' => $config['section_settings'] ?? [],
            'pageConfig' => $config instanceof PageConfig ? $config : null
        ]);
    }


    public function update(Request $request)
    {
        $validated = $request->validate([
            'layout_config' => 'required|array',
            'layout_config.sections' => 'required|array',
            'layout_config.sections.*.type' => 'required|string',
            'layout_config.sections.*.variant' => 'required|string',
            'layout_config.sections.*.order' => 'required|integer',
            'layout_config.sections.*.navLabel' => 'required|string',
            'layout_config.sections.*.navId' => 'required|string',
            'layout_config.sections.*.settings' => 'array',
            'section_settings' => 'array'
        ]);


        $config = PageConfig::updateOrCreate(
            ['page_name' => 'home', 'is_active' => true],
            [
                'layout_config' => $validated['layout_config'],
                'section_settings' => $validated['section_settings'] ?? []
            ]
        );

        return redirect()->back()->with('success', 'Page configuration updated successfully!');
    }

    public function custom()
    {
        // Get custom components from the Custom folder
        $customPath = resource_path('js/Pages/Home/Custom');
        $savedComponents = [];

        if (File::exists($customPath)) {
            $files = File::files($customPath);
            foreach ($files as $file) {
                if ($file->getExtension() === 'jsx') {
                    $savedComponents[] = [
                        'name' => $file->getFilenameWithoutExtension(),
                        'path' => 'js/Pages/Home/Custom/' . $file->getFilename(),
                        'modified' => $file->getMTime(),
                    ];
                }
            }
        }

        return Inertia::render('Admin/CustomComponentBuilder', [
            'availableComponents' => config('sections.sections', []),
            'savedComponents' => $savedComponents,
        ]);
    }
}
