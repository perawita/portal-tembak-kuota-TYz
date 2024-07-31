<?php

namespace App\Orchid\Layouts\InternetQuotaList;

use App\Orchid\Filters\QuotaFilter;

use Orchid\Filters\Filter;
use Orchid\Screen\Layouts\Selection;

class InternetQuotaSelection extends Selection
{
    /**
     * @return Filter[]
     */
    public function filters(): iterable
    {
        return [
            QuotaFilter::class,
        ];
    }
}
