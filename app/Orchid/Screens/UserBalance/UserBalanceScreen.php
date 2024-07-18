<?php

declare(strict_types=1);

namespace App\Orchid\Screens\UserBalance;

use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;


use App\Orchid\Layouts\UserBalance\UserBalanceSelection;
use App\Orchid\Layouts\UserBalance\UserBalanceListLayout;

use App\Models\Balance;

class UserBalanceScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        $userId = request()->route()->parameter('user');

        $query = Balance::with('user')
        ->defaultSort('id', 'desc');

        // Jika parameter 'user' ada, tambahkan kondisi where
        if ($userId) {
            $query->where('user_id', $userId);
        }

        return [
            'user-balance' => $query->paginate(),
        ];
    }


    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return 'User Balance';
    }


    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return 'Manage user balances of your application users.';
    }


    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make(__('Add User Balance'))
                ->icon('bs.plus-circle')
                ->route('platform.user-balance.create'),
        ];
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
            UserBalanceListLayout::class,
        ];
    }
}
