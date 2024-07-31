<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\ProductList;

use Orchid\Screen\Field;
use Orchid\Screen\Layouts\Rows;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\SimpleMDE;

use App\Models\Quota;

class ProductListLEditLayout extends Rows
{
    /**
     * The screen's layout elements.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        $id = request()->route()->parameter('id');
        $quota = Quota::find($id);

        return [
            Input::make('attachment_id')
                ->type('hidden')
                ->value($quota->attachment->id ?? null),

            Input::make('attachment_name')
                ->type('text')
                ->max(255)
                ->required(false)
                ->readonly(true)
                ->value($quota->attachment->original_name ?? null)
                ->title(__('Attachment Name'))
                ->placeholder(__('Attachment Name')),

            Input::make('quotas.name')
                ->type('text')
                ->max(255)
                ->title(__('Name Quota'))
                ->placeholder(__('Name Quota'))
                ->value($quota->name ?? null)
                ->required(),

            Input::make('quotas.price')
                ->type('number')
                ->title(__('Price Quota'))
                ->placeholder(__('Price Quota'))
                ->value($quota->price ?? null)
                ->required(),

            Select::make('quotas.status')
                ->title(__('Status'))
                ->options([
                    'Public'  => 'Public',
                    'Private'  => 'Private',
                ])
                ->empty('No select')
                ->required()
                ->rules('required', ['in:Public,Private']),


            SimpleMDE::make('quotas.description')
                ->title(__('Quota Description'))
                ->value($quota->description ?? null)
                ->popover("Add a description of your quota, but this doesn't have to be filled in")
                ->required(false),
        ];
    }
}
