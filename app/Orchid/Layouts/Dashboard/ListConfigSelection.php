<?php

namespace App\Orchid\Layouts\Dashboard;

use App\Orchid\Filters\FileFilter;

use Orchid\Filters\Filter;
use Orchid\Screen\Layouts\Selection;

class ListConfigSelection extends Selection
{
    /**
     * @return Filter[]
     */
    public function filters(): iterable
    {
        return [
            FileFilter::class,
        ];
    }
}
