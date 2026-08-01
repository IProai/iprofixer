<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\NavigationMenu;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

final class NavigationSeeder extends Seeder
{
    public function run(): void
    {
        $menus = [
            'header' => [
                'name_en' => 'Header Navigation',
                'name_ar' => 'التنقل الرئيسي',
                'items' => [
                    ['label_en' => 'Services', 'label_ar' => 'الخدمات', 'route_name' => 'services'],
                    ['label_en' => 'Industries', 'label_ar' => 'القطاعات', 'route_name' => 'industries'],
                    ['label_en' => 'Process', 'label_ar' => 'آلية العمل', 'route_name' => 'process'],
                    ['label_en' => 'Proof & Results', 'label_ar' => 'الإثبات والنتائج', 'route_name' => 'results'],
                    ['label_en' => 'About', 'label_ar' => 'عن الشركة', 'route_name' => 'about'],
                    ['label_en' => 'Resources', 'label_ar' => 'الموارد', 'route_name' => 'resources'],
                ],
            ],
            'mobile' => [
                'name_en' => 'Mobile Navigation',
                'name_ar' => 'تنقل الجوال',
                'items' => [
                    ['label_en' => 'Services', 'label_ar' => 'الخدمات', 'route_name' => 'services'],
                    ['label_en' => 'Industries', 'label_ar' => 'القطاعات', 'route_name' => 'industries'],
                    ['label_en' => 'Process', 'label_ar' => 'آلية العمل', 'route_name' => 'process'],
                    ['label_en' => 'Proof & Results', 'label_ar' => 'الإثبات والنتائج', 'route_name' => 'results'],
                    ['label_en' => 'About', 'label_ar' => 'عن الشركة', 'route_name' => 'about'],
                    ['label_en' => 'Resources', 'label_ar' => 'الموارد', 'route_name' => 'resources'],
                    ['label_en' => 'Client portal', 'label_ar' => 'بوابة العملاء', 'route_name' => 'portal'],
                    ['label_en' => 'Request assessment', 'label_ar' => 'اطلب تقييماً', 'route_name' => 'contact'],
                ],
            ],
            'footer_explore' => [
                'name_en' => 'Footer Explore',
                'name_ar' => 'استكشف',
                'items' => [
                    ['label_en' => 'Services', 'label_ar' => 'الخدمات', 'route_name' => 'services'],
                    ['label_en' => 'Industries', 'label_ar' => 'القطاعات', 'route_name' => 'industries'],
                    ['label_en' => 'Process', 'label_ar' => 'آلية العمل', 'route_name' => 'process'],
                    ['label_en' => 'Proof & Results', 'label_ar' => 'الإثبات والنتائج', 'route_name' => 'results'],
                ],
            ],
            'footer_start' => [
                'name_en' => 'Footer Start',
                'name_ar' => 'ابدأ',
                'items' => [
                    ['label_en' => 'Resources', 'label_ar' => 'الموارد', 'route_name' => 'resources'],
                    ['label_en' => 'Client portal', 'label_ar' => 'بوابة العملاء', 'route_name' => 'portal'],
                    ['label_en' => 'Request assessment', 'label_ar' => 'طلب تقييم', 'route_name' => 'contact'],
                ],
            ],
        ];

        foreach ($menus as $location => $data) {
            $menu = NavigationMenu::firstOrCreate(
                ['location' => $location],
                [
                    'id' => (string) Str::uuid(),
                    'name_en' => $data['name_en'],
                    'name_ar' => $data['name_ar'],
                    'is_active' => true,
                ]
            );

            $order = 1;
            foreach ($data['items'] as $itemData) {
                $menu->items()->firstOrCreate(
                    [
                        'navigation_menu_id' => $menu->id,
                        'route_name' => $itemData['route_name'],
                    ],
                    [
                        'id' => (string) Str::uuid(),
                        'label_en' => $itemData['label_en'],
                        'label_ar' => $itemData['label_ar'],
                        'destination_type' => 'internal_route',
                        'sort_order' => $order++,
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
