<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\AddInternetQuotaList;

use Orchid\Screen\Field;
use Orchid\Screen\Fields\Upload;
use Orchid\Screen\Layouts\Rows;

class AddInternetQuotaLUploadLayout extends Rows
{
    /**
     * The screen's layout elements.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        return [
            Upload::make('docs')
                ->groups('documents'),
        ];
    }
}
