<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\PageController;

use Illuminate\Support\Str;

use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;



use App\Models\MaintenanceSetting;


class PageListLayout extends Table
{
    /**
     * @var string
     */
    public $target = 'maintenance';

    /**
     * @return TD[]
     */
    public function columns(): array
    {
        return [
            TD::make('maintenance.page_name', __('Name Pages'))
                ->align(TD::ALIGN_LEFT)
                ->render(function (MaintenanceSetting $maintenance) {
                    return Str::limit($maintenance->page_name ?? '', 50);
                }),

            TD::make('maintenance.is_open', __('Status'))
                ->align(TD::ALIGN_LEFT)
                ->render(function (MaintenanceSetting $maintenance) {
                    return Str::limit($maintenance->is_open === 0 ? 'Maintenance' : 'Open');
                }),

            TD::make(__('Actions'))
                ->align(TD::ALIGN_RIGHT)
                ->width('100px')
                ->render(
                    fn (MaintenanceSetting $maintenance) =>

                    Link::make(__('Edit'))
                        ->route('platform.page-controller.edit-page', $maintenance->id)
                        ->icon('bs.pencil'),
                ),
        ];
    }
}
