<?php

namespace App\Orchid\Screens\DeleteQuota;

use Orchid\Screen\Screen;
use App\Orchid\Layouts\DeleteQuota\DeleteQuotaListLayout;
use Orchid\Support\Facades\Toast;
use Illuminate\Http\Request;
use App\Http\Controllers\DeleteQuotaController;

class DeleteQuotaScreen extends Screen
{
    protected $nomor = null;
    
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): array
    {
        $quota_controller = null;
        $this->nomor = session('response_json') ?? null;
        $this->nomor ? $quota_controller = new DeleteQuotaController() : $this->handleWindows();


        return [
            'list-quota' => $this->nomor ? $quota_controller->index() : null
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return 'Delete Quota';
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return 'Manage your quota.';
    }

    /**
     * The screen's layout elements.
     *
     * @return array
     */
    public function layout(): array
    {
        if (empty($this->nomor)){
            $this->handleWindows();
        }

        return [
            DeleteQuotaListLayout::class
        ];
    }

    public function handleWindows()
    {
        Toast::error(__('Please login for continue this pages'));
        return redirect()->route('platform.main');
    }

    
    /**
     * @throws \Exception
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(Request $request)
    {
        $quota_controller = new DeleteQuotaController();
        $quota = $quota_controller->delete($request->get('quota_code'));
        Toast::info(__($quota));
    }
}

