<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\UserBalance;

use Orchid\Screen\Field;

use Orchid\Screen\Layouts\Rows;

use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\Input;

use App\Models\User;

class UserBalanceEditLayout extends Rows
{
    /**
     * The screen's layout elements.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        return [
            Select::make('user-balance.user_id')
                ->fromModel(User::class, 'name', 'id')
                ->multiple()
                ->title(__('Name'))
                ->help('Specify which groups this account should belong to'),

            Input::make('user-balance.amount')
                ->type('number')
                ->required()
                ->title(__('Balance'))
                ->placeholder(__('Balance')),
        ];
    }
}
