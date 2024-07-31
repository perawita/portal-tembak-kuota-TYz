<?php

namespace App\Orchid\Screens\TopUp;

use Orchid\Screen\Fields\Input;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Fields\Group;

use App\Models\Balance;
use App\Models\User;

class TopUpScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @param User $request
     * @return array
     */
    public function query(User $request): array
    {
        $balance = Balance::where('user_id', auth()->id())->first();

        return [
            'balance' => $balance ? $balance->amount : 0,
            'last-top-up' => $balance ? $balance->last_topup : '00-00-00',
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return 'Dashboards';
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return 'See some details of your activities';
    }

    /**
     * The screen's layout elements.
     *
     * @return string[]|\Orchid\Screen\Layout[]
     */
    public function layout(): iterable
    {
        return [
            Layout::metrics([
                'Last Top Up' => 'last-top-up',
                'Saldo' => 'balance',
            ]),

            Layout::rows([
                Group::make([
                    Input::make('phone')
                        ->type('tel')
                        ->title('Send a message to this WhatsApp number to top up')
                        ->value('0899999999')
                        ->placeholder('Enter phone number')
                        ->horizontal()
                        ->readonly(true),
                ]),
            ]),

        ];
    }

    /**
     * method for request otp
     *
     */
    public function request_code_otp()
    {
    }
}
