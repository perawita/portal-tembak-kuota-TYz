<?php

declare(strict_types=1);

namespace App\Orchid\Screens\ProductList;

use Illuminate\Http\Request;

use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

use App\Orchid\Layouts\ProductList\QuotaSelection;
use App\Orchid\Layouts\ProductList\ProductListLayout;
use App\Orchid\Layouts\ProductList\ProductListLEditLayout;


use App\Models\Quota;

class ProductListScreen extends Screen
{

    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): array
    {
        return [
            'Quota' => Quota::with('attachment')
                ->where('name', 'like', request()->route()->parameter('quota') ?? '%%')
                ->paginate(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return 'Your product collection';
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return 'Management of all your products.';
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
            QuotaSelection::class,
            ProductListLayout::class,

            Layout::modal('asyncEditQuotaModal', ProductListLEditLayout::class)
                ->async('asyncGetQuota'),
        ];
    }

    /**
     * @return array
     */
    public function asyncGetQuota(Quota $Quota): iterable
    {
        return [
            'Quota' => $Quota,
        ];
    }

    public function remove(Request $request)
    {
        $Quota = Quota::findOrFail($request->get('id'));

        // Hapus record dari database
        $Quota->delete();

        Toast::info(__('Quota has been deleted.'));
    }
}
