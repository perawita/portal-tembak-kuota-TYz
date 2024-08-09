<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Dashboard;

use Orchid\Screen\Actions\Button;
use Orchid\Support\Color;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use Illuminate\Support\Str;

use App\Models\File;

class ListConfigLayout extends Table
{
    /**
     * @var string
     */
    public $target = 'file';

    /**
     * @return TD[]
     */
    public function columns(): array
    {
        return [
            TD::make('file.name', __('Name'))
                ->align(TD::ALIGN_LEFT)
                ->cantHide()
                ->render(function (File $file) {
                    return Str::limit($file->name ?? '', 50);
                }),

            // use
            TD::make(__(''))
                ->align(TD::ALIGN_RIGHT)
                ->width('100px')
                ->render(
                    fn (File $file) =>
                    Button::make(__('Use'))
                        ->type(Color::SUCCESS)
                        ->confirm(__('Are you sure to use '. $file->name.$file->mime_type . ' this file for login?'))
                        ->icon('bs.folder')
                        ->method('useConfig')
                        ->parameters([
                            'file_id' => $file->id,
                        ]),
                ),
                
                // delete
                TD::make(__(''))
                    ->align(TD::ALIGN_RIGHT)
                    ->width('100px')
                    ->render(
                        fn (File $file) =>
                        Button::make(__('Delete'))
                            ->type(Color::WARNING)
                            ->confirm(__('Are you sure to delete '. $file->name.$file->mime_type))
                            ->icon('bs.trash')
                            ->method('delete')
                            ->parameters([
                                'file_id' => $file->id,
                            ]),
                    ),
        ];
    }
}
