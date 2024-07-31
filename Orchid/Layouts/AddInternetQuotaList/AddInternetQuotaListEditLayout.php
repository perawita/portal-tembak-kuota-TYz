<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\AddInternetQuotaList;

use App\Models\Attachment;
use Orchid\Screen\Field;
use Orchid\Screen\Layouts\Rows;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;

class AddInternetQuotaListEditLayout extends Rows
{
    /**
     * The screen's layout elements.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        $attachment = Attachment::find(request()->route()->parameter('id'));

        return [
            Input::make('attachment.id')
                ->type('hidden')
                ->required(false)
                ->value(request()->route()->parameter('id') ?? null)
                ->title(__('ID'))
                ->placeholder(__('ID')),

            Input::make('attachment.name')
                ->type('text')
                ->max(255)
                ->required(false)
                ->readonly(true)
                ->value($attachment->original_name ?? null)
                ->title(__('Name'))
                ->placeholder(__('Name')),

            Input::make('quotas.name')
                ->type('text')
                ->max(255)
                ->title(__('Name Quota'))
                ->placeholder(__('Name Quota'))
                ->required(),

            Input::make('quotas.price')
                ->type('number')
                ->title(__('Price Quoat'))
                ->placeholder(__('Price Quota'))
                ->mask(
                    [
                        'alias' => 'currency',
                        'prefix' => ' ',
                        'groupSeparator' => ' ',
                        'digitsOptional' => true,
                    ]
                )
                ->required(),

            Select::make('quotas.status')
                ->options([
                    'Public'  => 'Public',
                    'Private'  => 'Private',
                ])
                ->empty('No select')
                ->required()
                ->rules('required', array(['Public', 'Private'])),
        ];
    }
}
