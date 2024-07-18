<?php

namespace App\Orchid\Layouts\ProductList;

use App\Orchid\Filters\QuotaFilter;

use Orchid\Filters\Filter;
use Orchid\Screen\Layouts\Selection;

class QuotaSelection extends Selection
{
    /**
     * @return Filter[]
     */
    public function filters(): iterable
    {
        return [
            QuotaFilter::class
        ];
    }
}
