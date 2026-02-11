<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageConfig;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PageController extends Controller
{
    public function edit()
    {
        $config = PageConfig::getHomeConfig();

        return Inertia::render('Admin/PageBuilder', [
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
}
