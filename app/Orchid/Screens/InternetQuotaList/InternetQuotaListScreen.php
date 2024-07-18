<?php

declare(strict_types=1);

namespace App\Orchid\Screens\InternetQuotaList;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

use Orchid\Screen\Screen;
use Orchid\Support\Facades\Toast;



use App\Models\Quota;
use App\Models\Balance;

use App\Orchid\Layouts\InternetQuotaList\InternetQuotaSelection;
use App\Orchid\Layouts\InternetQuotaList\InternetQuotaListLayout;

class InternetQuotaListScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        $param = request()->route()->parameter('quota');

        $query = Quota::with('attachment')
            ->where('status', 'like', '%public%');

        // Jika parameter 'quota' ada, tambahkan kondisi where
        if ($param) {
            $query->where('name', 'like', $param);
        }

        return [
            'quota' => $query->paginate(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return 'Internet Quota List';
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return 'Make purchases against the quota that has been provided.';
    }

    /**
     * The screen's layout elements.
     *
     * @return string[]|\Orchid\Screen\Layout[]
     */
    public function layout(): iterable
    {
        return [
            InternetQuotaSelection::class,
            InternetQuotaListLayout::class
        ];
    }



    /**
     * @throws \Exception
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleBuy(Request $request)
    {
        $quota = Quota::findOrFail($request->get('quota_id'));
        $user = Balance::where('user_id', auth()->id())->first();

        if ((int)$quota->price <= (int)$user->amount) {
            return redirect()->route('platform.internet-quota-list.buy', Crypt::encryptString($request->get('quota_id')));
        } else {
            Toast::error(__('Your balance amount is less than the amount to be paid, your balance amount is ' . $user->amount));
            return redirect()->route('platform.internet-quota-list');
        }
    }
}
