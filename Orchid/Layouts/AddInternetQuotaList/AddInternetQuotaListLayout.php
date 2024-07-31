<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\AddInternetQuotaList;

use Illuminate\Support\Str;


use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;


use App\Models\Attachment;

class AddInternetQuotaListLayout extends Table
{

    /**
     * @var string
     */
    public $target = 'attachment';

    /**
     * @return TD[]
     */
    public function columns(): array
    {
        return [
            TD::make('title', __('Title'))
                ->align(TD::ALIGN_LEFT)
                ->render(function (Attachment $Attachment) {
                    return Str::limit($Attachment->name ?? '', 50);
                }),

            TD::make('original_title', __('Original Title'))
                ->align(TD::ALIGN_LEFT)
                ->render(function (Attachment $Attachment) {
                    return Str::limit($Attachment->original_name ?? '', 50);
                }),

            TD::make('mime', __('Mime'))
                ->align(TD::ALIGN_LEFT)
                ->render(function (Attachment $Attachment) {
                    return Str::limit($Attachment->mime ?? '', 50);
                }),

            TD::make('', __('Size'))
                ->align(TD::ALIGN_LEFT)
                ->render(function (Attachment $attachment) {
                    return $this->formatBytes($attachment->size);
                }),


            // Actions column
            TD::make(__('Actions'))
                ->align(TD::ALIGN_RIGHT)
                ->render(function (Attachment $Attachment) {
                    return DropDown::make()
                        ->icon('bs.three-dots-vertical')
                        ->list([
                            Link::make(__('Edit'))
                                ->icon('bs.pencil')
                                ->route('platform.add-internet-quota-list.edit', $Attachment->id),

                            Button::make(__('Delete'))
                                ->icon('bs.trash3')
                                ->confirm(__('Once the item is deleted, all of its resources and data will be permanently deleted.'))
                                ->method('remove', [
                                    'id' => $Attachment->id,
                                ]),
                        ]);
                }),

        ];
    }


    protected function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        return round($bytes / pow(1024, $pow), $precision) . ' ' . $units[$pow];
    }
}
