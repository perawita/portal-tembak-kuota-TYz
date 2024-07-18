<?php

namespace App\Orchid\Layouts\AddInternetQuotaList;


use App\Orchid\Filters\AttachmentsFilter;
use Orchid\Filters\Filter;
use Orchid\Screen\Layouts\Selection;

class AttachmentSelection extends Selection
{
    /**
     * @return Filter[]
     */
    public function filters(): iterable
    {
        return [
            AttachmentsFilter::class
        ];
    }
}
