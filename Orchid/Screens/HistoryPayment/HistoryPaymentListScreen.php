<?php

declare(strict_types=1);

namespace App\Orchid\Screens\HistoryPayment;

use Orchid\Screen\Screen;

use App\Orchid\Layouts\HistoryPayment\HistoryPaymentListLayout;


use App\Models\PaymentHistory;

class HistoryPaymentListScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'history' => PaymentHistory::with('quota')
                ->where('user_id', auth()->id())
                ->orderBy('id', 'DESC')
                ->paginate(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return 'History You Payments';
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return 'Show your payment history.';
    }

    /**
     * The screen's layout elements.
     *
     * @return string[]|\Orchid\Screen\Layout[]
     */
    public function layout(): iterable
    {
        return [
            HistoryPaymentListLayout::class
        ];
    }
}
