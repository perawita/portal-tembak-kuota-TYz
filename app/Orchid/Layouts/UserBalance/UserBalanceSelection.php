<?php

namespace App\Orchid\Layouts\UserBalance;

use App\Orchid\Filters\UserBalanceFilter;
use Orchid\Filters\Filter;
use Orchid\Screen\Layouts\Selection;


class UserBalanceSelection extends Selection
{
    /**
     * @return Filter[]
     */
    public function filters(): iterable
    {
        return [
            UserBalanceFilter::class,
        ];
    }
}
