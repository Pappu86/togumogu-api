<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('menus')->delete();
        $menus = [
            [
                'title' => 'Product',
                'icon' => 'mdi-store-24-hour',
                'link' => null,
                'status' => 'active',
                'children' => [
                    [
                        'title' => 'Product',
                        'icon' => 'mdi-basket',
                        'link' => '/product/products',
                        'status' => 'active',
                    ],
                    [
                        'title' => 'Product Trashed',
                        'icon' => 'mdi-trash-can',
                        'link' => '/product/products-trash',
                        'status' => 'active',
                    ],
                    [
                        'title' => 'Category',
                        'icon' => 'mdi-shopping',
                        'link' => '/product/categories',
                        'status' => 'active',
                    ],
                ]
            ],
            [
                'title' => 'Order',
                'icon' => 'mdi-order-numeric-ascending',
                'link' => null,
                'status' => 'active',
                'children' => [
                    [
                        'title' => 'Orders',
                        'icon' => 'mdi-order-numeric-descending',
                        'link' => '/order/orders',
                        'status' => 'active',
                    ],
                    [
                        'title' => 'Order Status',
                        'icon' => 'mdi-label',
                        'link' => '/order/statuses',
                        'status' => 'active',
                    ],
                ]
            ],
            [
                'title' => 'Marketing',
                'icon' => 'mdi-bullhorn',
                'link' => null,
                'status' => 'active',
                'children' => [
                    [
                        'title' => 'Coupons',
                        'icon' => 'mdi-cards',
                        'link' => '/marketing/coupons',
                        'status' => 'active',
                    ],
                    [
                        'title' => 'Reward Points',
                        'icon' => 'mdi-bitcoin',
                        'link' => '/marketing/reward-points',
                        'status' => 'active',
                    ],
                    [
                        'title' => 'Offer',
                        'icon' => 'mdi-offer',
                        'link' => '/marketing/offers',
                        'status' => 'active',
                    ],
                    [
                        'title' => 'Offer Redeem',
                        'icon' => 'mdi-point-of-sale',
                        'link' => '/marketing/offer-redeem',
                        'status' => 'active',
                    ],
                    [
                        'title' => 'Service',
                        'icon' => 'mdi-bus-multiple',
                        'link' => '/marketing/services',
                        'status' => 'active',
                    ],
                    [
                        'title' => 'Service Registration',
                        'icon' => 'mdi-calendar-check',
                        'link' => '/marketing/service-registration',
                        'status' => 'active',
                    ],
                ]
            ],
            [
                'title' => 'Brand',
                'icon' => 'mdi-bullhorn',
                'link' => null,
                'status' => 'active',
                'children' => [
                    [
                        'title' => 'Brands',
                        'icon' => 'mdi-shield-star',
                        'link' => '/brand/brands',
                        'status' => 'active',
                    ],
                    [
                        'title' => 'Category',
                        'icon' => 'mdi-ev-plug-type2',
                        'link' => '/brand/categories',
                        'status' => 'active',
                    ],
                    [
                        'title' => 'Brand Outlets',
                        'icon' => 'mdi-office-building',
                        'link' => '/brand/brand-outlets',
                        'status' => 'active',
                    ]
                ]
            ],
            [
                'title' => 'Blog',
                'icon' => 'mdi-forum',
                'link' => null,
                'status' => 'active',
                'children' => [
                    [
                        'title' => 'Article',
                        'icon' => 'mdi-forum',
                        'link' => '/blog/articles',
                        'status' => 'active',
                    ],
                    [
                        'title' => 'Article Trashed',
                        'icon' => 'mdi-trash-can',
                        'link' => '/blog/articles-trash',
                        'status' => 'active',
                    ],
                    [
                        'title' => 'Category',
                        'icon' => 'mdi-message',
                        'link' => '/blog/categories',
                        'status' => 'active',
                    ],
                ]
            ],
            [
                'title' => 'Community',
                'icon' => 'mdi-wechat',
                'link' => null,
                'status' => 'active',
                'children' => [
                    [
                        'title' => 'Post',
                        'icon' => 'mdi-post',
                        'link' => '/community/posts',
                        'status' => 'active',
                    ],
                    [
                        'title' => 'Comment',
                        'icon' => 'mdi-wechat',
                        'link' => '/community/comments',
                        'status' => 'active',
                    ],
                    [
                        'title' => 'Topic',
                        'icon' => 'mdi-apps',
                        'link' => '/community/topics',
                        'status' => 'active',
                    ],
                    [
                        'title' => 'Reports',
                        'icon' => 'mdi-alert-circle-check',
                        'link' => '/community/reports',
                        'status' => 'active',
                    ],
                    [
                        'title' => 'Report Reasons',
                        'icon' => 'mdi-alert-plus',
                        'link' => '/community/report-reasons',
                        'status' => 'active',
                    ],
                ]
            ],
            [
                'title' => 'Videos',
                'icon' => 'mdi-video',
                'link' => null,
                'status' => 'active',
                'children' => [
                    [
                        'title' => 'Video',
                        'icon' => 'mdi-video',
                        'link' => '/video/videos',
                        'status' => 'active',
                    ],
                    [
                        'title' => 'Video Trashed',
                        'icon' => 'mdi-trash-can',
                        'link' => '/video/videos-trash',
                        'status' => 'active',
                    ],
                    [
                        'title' => 'Category',
                        'icon' => 'mdi-shape',
                        'link' => '/video/categories',
                        'status' => 'active',
                    ],
                ]
            ],
            [
                'title' => 'Common',
                'icon' => 'mdi-forum',
                'link' => null,
                'status' => 'active',
                'children' => [
                    [
                        'title' => 'Filters',
                        'icon' => 'mdi-filter',
                        'link' => '/common/filters',
                        'status' => 'active',
                    ],
                    [
                        'title' => 'Filter Trashed',
                        'icon' => 'mdi-trash-can',
                        'link' => '/common/filters-trash',
                        'status' => 'active',
                    ],
                    [
                        'title' => 'Tags',
                        'icon' => 'mdi-label',
                        'link' => '/common/tags',
                        'status' => 'active',
                    ],
                    [
                        'title' => 'Age Groups',
                        'icon' => 'mdi-account-reactivate',
                        'link' => '/common/age-groups',
                        'status' => 'active',
                    ],
                    [
                        'title' => 'Hashtags',
                        'icon' => 'mdi-tag',
                        'link' => '/common/hashtags',
                        'status' => 'active',
                    ],
                ]
            ],
            [
                'title' => 'Shipping',
                'icon' => 'mdi-truck',
                'link' => null,
                'status' => 'active',
                'children' => [
                    [
                        'title' => 'Provider',
                        'icon' => 'mdi-truck',
                        'link' => '/shipping/providers',
                        'status' => 'active',
                    ],
                    [
                        'title' => 'Division',
                        'icon' => 'mdi-map-marker',
                        'link' => '/shipping/divisions',
                        'status' => 'active',
                    ],
                    [
                        'title' => 'District',
                        'icon' => 'mdi-map-marker',
                        'link' => '/shipping/districts',
                        'status' => 'active',
                    ],
                    [
                        'title' => 'Area/ Thana',
                        'icon' => 'mdi-map-marker',
                        'link' => '/shipping/areas',
                        'status' => 'active',
                    ],
                    [
                        'title' => 'Shipping Cost',
                        'icon' => 'mdi-currency-bdt',
                        'link' => '/shipping/costs',
                        'status' => 'active',
                    ],
                    [
                        'title' => 'Shipping Cost Tree View',
                        'icon' => 'mdi-currency-bdt',
                        'link' => '/shipping/cost-trees',
                        'status' => 'active',
                    ],
                ]
            ],
            [
                'title' => 'Payment',
                'icon' => 'mdi-credit-card-outline',
                'link' => null,
                'status' => 'active',
                'children' => [
                    [
                        'title' => 'Payment Status',
                        'icon' => 'mdi-label',
                        'link' => '/payment/statuses',
                        'status' => 'active',
                    ],
                    [
                        'title' => 'Payment Method',
                        'icon' => 'mdi-credit-card-outline',
                        'link' => '/payment/payment-methods',
                        'status' => 'active',
                    ]
                ]
            ],
            [
                'title' => 'Corporate',
                'icon' => 'mdi-briefcase',
                'link' => null,
                'status' => 'active',
                'children' => [
                    [
                        'title' => 'Partnership',
                        'icon' => 'mdi-arrange-bring-forward',
                        'link' => '/corporate/partnership',
                        'status' => 'active',
                    ],
                    [
                        'title' => 'Company List',
                        'icon' => 'mdi-factory',
                        'link' => '/corporate/companies',
                        'status' => 'active',
                    ],
                    [
                        'title' => 'Company Category',
                        'icon' => 'mdi-briefcase',
                        'link' => '/corporate/company-categories',
                        'status' => 'active',
                    ],
                    [
                        'title' => 'Employee Group',
                        'icon' => 'mdi-account-multiple',
                        'link' => '/corporate/employee-group',
                        'status' => 'active',
                    ]
                ]
            ],
            [
                'title' => 'Daycare',
                'icon' => 'mdi-home-heart',
                'link' => null,
                'status' => 'active',
                'children' => [
                    [
                        'title' => 'Daycare',
                        'icon' => 'mdi-home-heart',
                        'link' => '/daycare/daycares',
                        'status' => 'active',
                    ],
                    [
                        'title' => 'Category',
                        'icon' => 'mdi-label',
                        'link' => '/daycare/categories',
                        'status' => 'active',
                    ],
                    [
                        'title' => 'Features',
                        'icon' => 'mdi-checkbox-marked-circle-outline',
                        'link' => '/daycare/features',
                        'status' => 'active',
                    ],
                ]
            ],
            [
                'title' => 'Quiz',
                'icon' => 'mdi-store-24-hour',
                'link' => null,
                'status' => 'active',
                'children' => [
                    [
                        'title' => 'Quiz',
                        'icon' => 'mdi-crosshairs-gps',
                        'link' => '/quiz/quiz',
                        'status' => 'active',
                    ],
                    [
                        'title' => 'Question',
                        'icon' => 'mdi-help-circle',
                        'link' => '/quiz/question',
                        'status' => 'active',
                    ],
                ]
            ],
            [
                'title' => 'Home',
                'icon' => 'mdi-home',
                'link' => null,
                'status' => 'active',
                'children' => [
                    [
                        'title' => 'Main Sliders',
                        'icon' => 'mdi-folder-multiple-image',
                        'link' => '/home/sliders',
                        'status' => 'active',
                    ]
                ]
            ],
            [
                'title' => 'Users',
                'icon' => 'mdi-account-multiple',
                'link' => null,
                'status' => 'active',
                'children' => [
                    [
                        'title' => 'Customers',
                        'icon' => 'mdi-account-multiple',
                        'link' => '/customers',
                        'status' => 'active',
                    ],
                    [
                        'title' => 'Users',
                        'icon' => 'mdi-account-multiple',
                        'link' => '/users',
                        'status' => 'active',
                    ]
                ]
            ],
            [
                'title' => 'Reports',
                'icon' => 'mdi-poll-box',
                'link' => null,
                'status' => 'active',
                'children' => [
                    [
                        'title' => 'Sales',
                        'icon' => 'mdi-cards',
                        'link' => '/reports/sales',
                        'status' => 'active',
                    ]
                ]
            ],
            [
                'title' => 'Roles',
                'icon' => 'mdi-lock',
                'link' => null,
                'status' => 'active',
                'children' => [
                    [
                        'title' => 'Roles',
                        'icon' => 'mdi-lock',
                        'link' => '/roles',
                        'status' => 'active',
                    ],
                    [
                        'title' => 'Role wise Permissions',
                        'icon' => 'mdi-lock',
                        'link' => '/role-permissions',
                        'status' => 'active',
                    ]
                ]
            ],
            [
                'title' => 'Media Library',
                'icon' => 'mdi-folder',
                'link' => null,
                'status' => 'active',
                'children' => [
                    [
                        'title' => 'Assets',
                        'icon' => 'mdi-file',
                        'link' => '/media/assets',
                        'status' => 'active',
                    ],
                    [
                        'title' => 'Category',
                        'icon' => 'mdi-folder',
                        'link' => '/media/categories',
                        'status' => 'active',
                    ],
                    // [
                    //     'title' => 'File Manager',
                    //     'icon' => 'mdi-folder',
                    //     'link' => '/media/files',
                    //     'status' => 'active',
                    // ],
                ]
            ],
            [
                'title' => 'Message',
                'icon' => 'mdi-email-multiple',
                'link' => null,
                'status' => 'active',
                'children' => [
                    [
                        'title' => 'Notification',
                        'icon' => 'mdi-rocket-launch',
                        'link' => '/message/notifications',
                        'status' => 'active',
                    ],
                    [
                        'title' => 'Schedule Notification',
                        'icon' => 'mdi-rocket-launch',
                        'link' => '/message/schedule-notifications',
                        'status' => 'active',
                    ],
                    [
                        'title' => 'Templates',
                        'icon' => 'mdi-email-edit',
                        'link' => '/message/templates',
                        'status' => 'active',
                    ]
                ]
            ],
            [
                'title' => 'Administrations',
                'icon' => 'mdi-lock',
                'link' => null,
                'status' => 'active',
                'children' => [
                    [
                        'title' => 'Menus',
                        'icon' => 'mdi-menu',
                        'link' => '/administrations/menus',
                        'status' => 'active',
                    ],
                    [
                        'title' => 'Settings',
                        'icon' => 'mdi-cogs',
                        'link' => '/administrations/settings',
                        'status' => 'active',
                    ],
                    [
                        'title' => 'Activities Log',
                        'icon' => 'mdi-history',
                        'link' => '/administrations/activity-log',
                        'status' => 'active',
                    ],
                    [
                        'title' => 'Cache Management',
                        'icon' => 'mdi-folder',
                        'link' => '/administrations/cache-management',
                        'status' => 'active',
                    ],
                ]
            ],
        ];

        foreach ($menus as $menu) {
            Menu::create($menu);
        }

        $items = Menu::all();
        foreach ($items as $item) {
            $item->roles()->attach([1, 2]);
        }
    }
}