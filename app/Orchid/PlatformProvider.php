<?php

declare(strict_types=1);

namespace App\Orchid;

use Orchid\Platform\Dashboard;
use Orchid\Platform\ItemPermission;
use Orchid\Platform\OrchidServiceProvider;
use Orchid\Screen\Actions\Menu;
use Orchid\Support\Color;

class PlatformProvider extends OrchidServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @param Dashboard $dashboard
     *
     * @return void
     */
    public function boot(Dashboard $dashboard): void
    {
        parent::boot($dashboard);

        // ...
    }

    // /**
    //  * Register the application menu.
    //  *
    //  * @return Menu[]
    //  */
    // public function menu(): array
    // {
    //     return [
    //         Menu::make('Get Started')
    //             ->icon('bs.book')
    //             ->title('Navigation')
    //             ->route(config('platform.index')),

    //         Menu::make('Sample Screen')
    //             ->icon('bs.collection')
    //             ->route('platform.example')
    //             ->badge(fn () => 6),

    //         Menu::make('Form Elements')
    //             ->icon('bs.card-list')
    //             ->route('platform.example.fields')
    //             ->active('*/examples/form/*'),

    //         Menu::make('Overview Layouts')
    //             ->icon('bs.window-sidebar')
    //             ->route('platform.example.layouts'),

    //         Menu::make('Grid System')
    //             ->icon('bs.columns-gap')
    //             ->route('platform.example.grid'),

    //         Menu::make('Charts')
    //             ->icon('bs.bar-chart')
    //             ->route('platform.example.charts'),

    //         Menu::make('Cards')
    //             ->icon('bs.card-text')
    //             ->route('platform.example.cards')
    //             ->divider(),

    //         Menu::make(__('Users'))
    //             ->icon('bs.people')
    //             ->route('platform.systems.users')
    //             ->permission('platform.systems.users')
    //             ->title(__('Access Controls')),

    //         Menu::make(__('Roles'))
    //             ->icon('bs.shield')
    //             ->route('platform.systems.roles')
    //             ->permission('platform.systems.roles')
    //             ->divider(),

    //         Menu::make('Documentation')
    //             ->title('Docs')
    //             ->icon('bs.box-arrow-up-right')
    //             ->url('https://orchid.software/en/docs')
    //             ->target('_blank'),

    //         Menu::make('Changelog')
    //             ->icon('bs.box-arrow-up-right')
    //             ->url('https://github.com/orchidsoftware/platform/blob/master/CHANGELOG.md')
    //             ->target('_blank')
    //             ->badge(fn () => Dashboard::version(), Color::DARK),
    //     ];
    // }


    /**
     * Register the application menu.
     *
     * @return Menu[]
     */
    public function menu(): array
    {
        return [
            Menu::make('Dashboard')
                ->icon('bs.columns-gap')
                ->title('Navigation')
                ->route(config('platform.index')),

            Menu::make('Top Up Balance')
                ->icon('bs.collection')
                ->route('platform.top-up'),

            Menu::make('Internet Quota List')
                ->icon('bs.card-text')
                ->route('platform.internet-quota-list')
                ->active('*/internet-quota-list*'),

            Menu::make('History Payments')
                ->icon('bs.card-list')
                ->route('platform.history-payment')
                ->active('*/history-payment*'),

            Menu::make(__('Delete Quota'))
                ->icon('bs.trash3')
                ->route('platform.delete-quota')
                ->active('*/delete-quota*')
                ->divider(),

            Menu::make(__('User Balance'))
                ->icon('bs.wallet')
                ->route('platform.user-balance')
                ->permission('platform.systems.users')
                ->active('*/user-balance*')
                ->title(__('Settings')),

            Menu::make(__('User Data'))
                ->icon('bs.database')
                ->route('platform.user-data')
                ->permission('platform.systems.users')
                ->active('*/user-data*'),

            Menu::make(__('Products List'))
                ->icon('bs.card-text')
                ->route('platform.product-list')
                ->permission('platform.systems.users')
                ->active('*/product-list*'),

            Menu::make(__('Add Internet Quota List'))
                ->icon('bs.bar-chart')
                ->route('platform.add-internet-quota-list')
                ->permission('platform.systems.users')
                ->active('*/add-internet-quota-list*'),

            Menu::make(__('Pages Controllers'))
                ->icon('bs.window-sidebar')
                ->route('platform.page-controller')
                ->permission('platform.systems.users')
                ->active('*/page-controller*'),

            Menu::make(__('History Payment User'))
                ->icon('bs.card-list')
                ->route('platform.admin-history-payment')
                ->permission('platform.systems.users')
                ->active('*/admin-history-payment*')
                ->divider(),

            Menu::make(__('Users'))
                ->icon('bs.people')
                ->route('platform.systems.users')
                ->permission('platform.systems.users')
                ->title(__('Access Controls')),

            Menu::make(__('Roles'))
                ->icon('bs.shield')
                ->route('platform.systems.roles')
                ->permission('platform.systems.roles')
                ->divider(),
        ];
    }

    /**
     * Register permissions for the application.
     *
     * @return ItemPermission[]
     */
    public function permissions(): array
    {
        return [
            ItemPermission::group(__('System'))
                ->addPermission('platform.systems.roles', __('Roles'))
                ->addPermission('platform.systems.users', __('Users')),
        ];
    }
}
