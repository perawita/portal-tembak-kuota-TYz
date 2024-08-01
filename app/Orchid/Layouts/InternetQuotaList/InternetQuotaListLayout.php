<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\InternetQuotaList;

use Orchid\Screen\Actions\Button;
use Orchid\Support\Color;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use Illuminate\Support\Str;

use App\Models\Quota;

class InternetQuotaListLayout extends Table
{
    /**
     * @var string
     */
    public $target = 'quota';

    /**
     * @return TD[]
     */
    public function columns(): array
    {
        return [
            TD::make('quota.name', __('Name'))
                ->align(TD::ALIGN_LEFT)
                ->sort()
                ->cantHide()
                ->render(function (Quota $quota) {
                    return Str::limit($quota->name ?? '', 50);
                }),

            // TD::make('quota.extension', __('Extension'))
            //     ->align(TD::ALIGN_LEFT)
            //     ->sort()
            //     ->cantHide()
            //     ->render(function (Quota $quota) {
            //         return Str::limit(optional($quota->attachment)->extension ?? '', 50);
            //     }),

            TD::make('quota.price', __('Price'))
                ->align(TD::ALIGN_LEFT)
                ->sort()
                ->cantHide()
                ->render(function (Quota $quota) {
                    return 'Rp ' . $quota->price;
                }),

            TD::make(__('Actions'))
                ->align(TD::ALIGN_RIGHT)
                ->width('100px')
                ->render(
                    fn (Quota $quota) =>
                    Button::make(__('Buy'))
                        ->type(Color::SUCCESS)
                        ->icon('bs.wallet')
                        ->confirm(__('Are you sure you will buy a package at a price of ' . $quota->price . ' thousand.'))
                        ->method('handleBuy')
                        ->parameters([
                            'quota_id' => $quota->id,
                        ]),
                ),
        ];
    }
}
