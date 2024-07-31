<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\ListHistoryPayment;

use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Support\Color;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\Artisan;

use Illuminate\Support\Carbon;

use App\Models\PaymentHistory;

class ListHistoryPaymentListLayout extends Table
{
    /**
     * @var string
     */
    public $target = 'history';

    /**
     * @return TD[]
     */
    public function columns(): array
    {
        return [

            TD::make('history.created_at', __('Date'))
                ->align(TD::ALIGN_LEFT)
                ->render(function (PaymentHistory $history) {
                    return Str::limit($history->created_at ?? '', 50);
                }),

            TD::make('history.user_id', __('Name'))
                ->align(TD::ALIGN_LEFT)
                ->render(function (PaymentHistory $history) {
                    return Str::limit(optional($history->user)->name ?? '', 50);
                }),

            TD::make('history.quota.name', __('Name'))
                ->align(TD::ALIGN_LEFT)
                ->render(function (PaymentHistory $history) {
                    return Str::limit(optional($history->quota)->name ?? '', 50);
                }),

            TD::make('history.quota.price', __('Price'))
                ->align(TD::ALIGN_LEFT)
                ->render(function (PaymentHistory $history) {
                    return 'Rp ' . optional($history->quota)->price;
                }),

            TD::make('history.status', __('Payment'))
                ->align(TD::ALIGN_LEFT)
                ->render(function (PaymentHistory $history) {
                    return Str::limit($history->status ?? '', 50);
                }),

            TD::make('history.expired_at', __('Expired'))
                ->align(TD::ALIGN_LEFT)
                ->render(function (PaymentHistory $history) {
                    $now =  new Carbon;
                    $expiry_diff = $now->diffInMinutes($history->expired_at);

                    $remainingMinutes = $expiry_diff % 60;

                    if ($remainingMinutes < 1) {
                        Artisan::call('payments:process-expired');
                    }

                    return Str::limit($remainingMinutes > 0 ? $remainingMinutes . ' minute' : '0' . ' minute');
                }),

            TD::make(__('Actions'))
                ->align(TD::ALIGN_RIGHT)
                ->width('100px')
                ->render(fn (PaymentHistory $history) => DropDown::make()
                    ->icon('bs.three-dots-vertical')
                    ->list([
                        Link::make(__('Pay'))
                            ->icon('bs.wallet')
                            ->target('_blank')
                            ->href($history->payment_url),


                        Button::make(__('Delete'))
                            ->icon('bs.trash3')
                            ->confirm(__('Once the account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.'))
                            ->method('remove', [
                                'id' => $history->id,
                            ]),
                    ])),
        ];
    }
}
