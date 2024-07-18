<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\PageController;

use Orchid\Screen\Field;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Rows;
use Orchid\Screen\Fields\Select;

use App\Models\MaintenanceSetting;

class PageEditLayout extends Rows
{
    /**
     * The screen's layout elements.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        $maintenance = MaintenanceSetting::find(request()->route()->parameter('id'));

        return [
            Input::make('maintenance.id')
                ->type('hidden')
                ->required(false)
                ->value(request()->route()->parameter('id') ?? null)
                ->placeholder(__('ID')),

            Input::make('maintenance.page_name')
                ->type('text')
                ->max(255)
                ->value($maintenance->page_name ?? null)
                ->title(__('Name'))
                ->placeholder(__('Example: internet-quota-list')),

            Select::make('maintenance.is_open')
                ->options([
                    0  => 'Maintenance',
                    1  => 'Open',
                ])
                ->title('Maintenance')
                ->empty('No select')
                ->required()
                ->rules('required', array([0, 1])),
        ];
    }
}
