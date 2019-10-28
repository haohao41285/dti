<?php

use Illuminate\Database\Seeder;
use App\Models\MainMenuDti;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        MainMenuDti::truncate();
        $menu_arr = [
            [
                'name' => 'Dashboard',
                'icon' => 'fas fa-tachometer-alt',
                'link' => 'dashboard',
                'parent_id' => 0,
                'status' => 1,
            ],
            [
                'name' => 'Task',
                'icon' => 'fas fa-users',
                'link' => 'task',
                'parent_id' => 0,
                'status' => 1,
            ],
            [
                'name' => 'Customers',
                'icon' => 'fas fa-users',
                'link' => 'customer',
                'parent_id' => 0,
                'status' => 1,
            ],
            [
                'name' => 'Marketing',
                'icon' => 'fas fa-lightbulb',
                'link' => 'marketing',
                'parent_id' => 0,
                'status' => 1,
            ],
            [
                'name' => 'Statistic',
                'icon' => 'fas fa-chart-bar',
                'link' => 'statistic',
                'parent_id' => 0,
                'status' => 1,
            ],
            [
                'name' => 'IT Tools',
                'icon' => 'fas fa-toolbox',
                'link' => 'tools',
                'parent_id' => 0,
                'status' => 1,
            ],
            [
                'name' => 'Orders',
                'icon' => 'fas fa-shopping-cart',
                'link' => 'orders',
                'parent_id' => 0,
                'status' => 1,
            ],
            [
                'name' => 'Users',
                'icon' => 'fas fa-user-cog',
                'link' => 'user',
                'parent_id' => 0,
                'status' => 1,
            ],
            [
                'name' => 'Settings',
                'icon' => 'fas fa-cog',
                'link' => 'setting',
                'parent_id' => 0,
                'status' => 1,
            ],
            [
                'name' => 'Notification',
                'icon' => 'fas fa-sms',
                'link' => 'notification',
                'parent_id' => 0,
                'status' => 1,
            ],
            [
                'name' => 'Recent Logs',
                'icon' => 'fas fa-list-alt',
                'link' => 'recentlog',
                'parent_id' => 0,
                'status' => 1,
            ],
            [
                'name' => 'All Task',
                'icon' => '',
                'link' => 'task/all-task',
                'parent_id' => 2,
                'status' => 1,
            ],
            [
                'name' => 'My Task',
                'icon' => '',
                'link' => 'task',
                'parent_id' => 2,
                'status' => 1,
            ],
            [
                'name' => 'Create New Task',
                'icon' => '',
                'link' => 'task/add',
                'parent_id' => 2,
                'status' => 1,
            ],
            [
                'name' => 'All Customers',
                'icon' => '',
                'link' => 'customer/customers',
                'parent_id' => 3,
                'status' => 1,
            ],
            [
                'name' => 'My Customer',
                'icon' => '',
                'link' => 'customer/my-customers',
                'parent_id' => 3,
                'status' => 1,
            ],
            [
                'name' => 'Create New Customer',
                'icon' => '',
                'link' => 'customer/add',
                'parent_id' => 3,
                'status' => 1,
            ],
            [
                'name' => 'Send SMS',
                'icon' => '',
                'link' => 'marketing/sendsms',
                'parent_id' => 4,
                'status' => 1,
            ],
            [
                'name' => 'Tracking History',
                'icon' => '',
                'link' => 'marketing/tracking-history',
                'parent_id' => 4,
                'status' => 1,
            ],
            [
                'name' => 'News',
                'icon' => '',
                'link' => 'marketing/news',
                'parent_id' => 4,
                'status' => 1,
            ],
            [
                'name' => 'POS',
                'icon' => '',
                'link' => 'statistic/seller',
                'parent_id' => 5,
                'status' => 1,
            ],
            [
                'name' => 'POS',
                'icon' => '',
                'link' => 'statistic/pos',
                'parent_id' => 5,
                'status' => 1,
            ],
            [
                'name' => 'Website',
                'icon' => '',
                'link' => 'statistic/website',
                'parent_id' => 5,
                'status' => 1,
            ],
            [
                'name' => 'Website theme',
                'icon' => '',
                'link' => 'tools/website-themes',
                'parent_id' => 6,
                'status' => 1,
            ],
            [
                'name' => 'App banners',
                'icon' => '',
                'link' => 'tools/app-banners',
                'parent_id' => 6,
                'status' => 1,
            ],
            [
                'name' => 'Places',
                'icon' => '',
                'link' => 'tools/places',
                'parent_id' => 6,
                'status' => 1,
            ],
            [
                'name' => 'My Orders',
                'icon' => '',
                'link' => 'orders/my-orders',
                'parent_id' => 6,
                'status' => 1,
            ],
            [
                'name' => 'All Orders',
                'icon' => '',
                'link' => 'orders/all',
                'parent_id' => 6,
                'status' => 1,
            ],
            [
                'name' => "Seller\'s Orders",
                'icon' => '',
                'link' => 'orders/sellers',
                'parent_id' => 6,
                'status' => 1,
            ],
            [
                'name' => 'New Order',
                'icon' => '',
                'link' => 'statistic/website',
                'parent_id' => 6,
                'status' => 1,
            ],
            [
                'name' => 'Users',
                'icon' => '',
                'link' => 'user/list',
                'parent_id' => 7,
                'status' => 1,
            ],
            [
                'name' => 'Roles',
                'icon' => '',
                'link' => 'user/roles',
                'parent_id' => 7,
                'status' => 1,
            ],
            [
                'name' => 'Service Permission',
                'icon' => '',
                'link' => 'user/service-permission',
                'parent_id' => 7,
                'status' => 1,
            ],
            [
                'name' => 'Setup Team',
                'icon' => '',
                'link' => 'setting/setup-team',
                'parent_id' => 8,
                'status' => 1,
            ],
            [
                'name' => 'Setup Team Type',
                'icon' => '',
                'link' => 'setting/setup-team-type',
                'parent_id' => 8,
                'status' => 1,
            ],
            [
                'name' => 'Setup Service',
                'icon' => '',
                'link' => 'setting/setup-service',
                'parent_id' => 8,
                'status' => 1,
            ],
            [
                'name' => 'Setup Service Type',
                'icon' => '',
                'link' => 'setting/setup-service-type',
                'parent_id' => 8,
                'status' => 1,
            ],
            [
                'name' => 'Setup Template SMS',
                'icon' => '',
                'link' => 'setting/setup-template-sms',
                'parent_id' => 8,
                'status' => 1,
            ],
            [
                'name' => 'Setup Login Background',
                'icon' => '',
                'link' => 'setting/login-background',
                'parent_id' => 8,
                'status' => 1,
            ],
            [
                'name' => 'Setup Event Holiday',
                'icon' => '',
                'link' => 'setting/setup-event-holiday',
                'parent_id' => 8,
                'status' => 1,
            ],
        ];
        MainMenuDti::create($menu_arr);

    }
}
