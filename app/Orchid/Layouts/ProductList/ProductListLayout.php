<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\ProductList;


use Illuminate\Support\Str;

use Orchid\Screen\TD;

use Orchid\Screen\Layouts\Table;

use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;



use App\Models\Quota;

class ProductListLayout extends Table
{

    /**
     * @var string
     */
    public $target = 'Quota';

    /**
     * @return TD[]
     */
    public function columns(): array
    {
        return [
            TD::make('Quota.name', __('Name Product'))
                ->align(TD::ALIGN_LEFT)
                ->render(function (Quota $Quota) {
                    return Str::limit($Quota->name ?? '', 50);
                }),

            TD::make('Quota.status', __('Status'))
                ->align(TD::ALIGN_RIGHT)
                ->render(function (Quota $Quota) {
                    return Str::limit($Quota->status ?? '', 50);
                }),

            TD::make('Quota.created_at', __('Create'))
                ->align(TD::ALIGN_RIGHT)
                ->render(function (Quota $Quota) {
                    return $Quota->created_at ?? '';
                }),


            // Actions column
            TD::make(__('Actions'))
                ->align(TD::ALIGN_RIGHT)
                ->render(function (Quota $Quota) {
                    return DropDown::make()
                        ->icon('bs.three-dots-vertical')
                        ->list([
                            Link::make(__('Edit'))
                                ->icon('bs.pencil')
                                ->route('platform.product-list.edit', $Quota->id),

                            Button::make(__('Delete'))
                                ->icon('bs.trash3')
                                ->confirm(__('Once the item is deleted, all of its resources and data will be permanently deleted.'))
                                ->method('remove', [
                                    'id' => $Quota->id,
                                ]),
                        ]);
                }),

        ];
    }
}
