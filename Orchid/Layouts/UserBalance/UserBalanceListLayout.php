<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\UserBalance;


use Illuminate\Support\Str;

use Orchid\Screen\TD;

use Orchid\Screen\Layouts\Table;

use App\Models\Balance;

class UserBalanceListLayout extends Table
{
    /**
     * Data target.
     *
     * @var string
     */
    public $target = 'user-balance';

    /**
     * @return array
     */
    public function columns(): array
    {
        return [
            TD::make('user-balance.user.name', __('Name'))
                ->cantHide()
                ->render(function (Balance $balance) {
                    return Str::limit(optional($balance->user)->name ?? '', 50);
                }),

            TD::make('user-balance.user.email', __('Email'))
                ->cantHide()
                ->render(function (Balance $balance) {
                    return Str::limit(optional($balance->user)->email ?? '', 50);
                }),

            TD::make('user-balance.amount', __('Balance'))
                ->align(TD::ALIGN_RIGHT)
                ->render(function (Balance $balance) {
                    return 'Rp ' . $balance->amount;
                }),

            TD::make('user-balance.last_topup', __('Last Top Up'))
                ->align(TD::ALIGN_RIGHT)
                ->render(function (Balance $balance) {
                    return Str::limit($balance->last_topup ?? '', 50);
                }),
        ];
    }
}
