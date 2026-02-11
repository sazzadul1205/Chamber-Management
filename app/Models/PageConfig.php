<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PageConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'page_name',
        'layout_config',
        'section_settings',
        'is_active'
    ];

    protected $casts = [
        'layout_config' => 'array',
        'section_settings' => 'array',
        'is_active' => 'boolean'
    ];

    public static function getHomeConfig()
    {
        $config = self::where('page_name', 'home')
            ->where('is_active', true)
            ->first();

        if (!$config) {
            // Return default configuration
            $defaultSections = [];
            $order = 1;

            foreach (config('sections.sections') as $key => $section) {
                $defaultSections[] = [
                    'id' => uniqid(),
                    'type' => $key,
                    'variant' => $section['default'],
                    'order' => $order++,
                    'settings' => [
                        'is_visible' => true,
                        'title' => $section['name'],
                        // Add other default settings here
                    ]
                ];
            }

            return [
                'layout_config' => [
                    'sections' => $defaultSections
                ],
                'section_settings' => []
            ];
        }

        return $config;
    }
}
