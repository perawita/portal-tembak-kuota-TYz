<?php

declare(strict_types=1);

namespace App\Orchid\Screens\ListHistoryPayment;

use Orchid\Screen\Screen;

use App\Orchid\Layouts\UserBalance\UserBalanceSelection;
use App\Orchid\Layouts\ListHistoryPayment\ListHistoryPaymentListLayout;

use App\Models\PaymentHistory;
use Illuminate\Http\Request;
use Orchid\Support\Facades\Toast;

class ListHistoryPaymentListScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        $userId = request()->route()->parameter('user');

        $query = PaymentHistory::with('quota', 'user')
            ->orderBy('id', 'DESC');

        // Jika parameter 'user' ada, tambahkan kondisi where
        if ($userId) {
            $query->where('user_id', $userId);
        }

        return [
            'history' => $query->paginate(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return 'History User Payments';
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return 'Management your user payment history.';
    }

    public function permission(): ?iterable
    {
        return [
            'platform.systems.users',
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return string[]|\Orchid\Screen\Layout[]
     */
    public function layout(): iterable
    {
        return [
            UserBalanceSelection::class,
            ListHistoryPaymentListLayout::class
        ];
    }


    public function remove(Request $request): void
    {
        PaymentHistory::findOrFail($request->get('id'))->delete();

        Toast::info(__('User was removed'));
    }
}
