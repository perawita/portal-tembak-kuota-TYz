<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\DeleteQuota;

use Orchid\Screen\Actions\Link;
use Orchid\Support\Color;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\TD;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DeleteQuotaListLayout extends Table
{
    /**
     * @var string
     */
    public $target = 'list-quota';

    /**
     * Define table columns.
     *
     * @return TD[]
     */
    public function columns(): array
    {
        return [
            TD::make('name', __('Quota Name'))
                ->align(TD::ALIGN_LEFT)
                ->sort()
                ->cantHide()
                ->render(function ($quota) {
                    return Str::limit($quota['name'] ?? '', 50);
                }),

            TD::make('active_date', __('Active Date'))
                ->align(TD::ALIGN_LEFT)
                ->sort()
                ->cantHide()
                ->render(function ($quota) {
                    return Carbon::createFromTimestamp($quota['active_date'])->toDateString();
                }),

            TD::make('end_date', __('End Date'))
                ->align(TD::ALIGN_LEFT)
                ->sort()
                ->cantHide()
                ->render(function ($quota) {
                    return Carbon::createFromTimestamp($quota['end_date'])->toDateString();
                }),

            TD::make('group_name', __('Group Name'))
                ->align(TD::ALIGN_LEFT)
                ->sort()
                ->cantHide()
                ->render(function ($quota) {
                    return Str::limit($quota['group_name'] ?? '', 50);
                }),

                TD::make(__('Actions'))
                ->align(TD::ALIGN_RIGHT)
                ->width('100px')
                ->render(function ($quota) {
                    return Button::make(__('Delete'))
                        ->icon('trash')
                        ->type(Color::DANGER)
                        ->confirm(__('Are you sure you want to delete this quota?'))
                        ->method('delete')
                        ->parameters([
                            'quota_code' => $quota['quota_code'],
                        ]);
                }),
        ];
    }
}
