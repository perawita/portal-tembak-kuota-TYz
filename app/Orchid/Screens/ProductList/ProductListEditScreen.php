<?php

declare(strict_types=1);

namespace App\Orchid\Screens\ProductList;


use Illuminate\Http\Request;

use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use Orchid\Support\Color;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;


use App\Orchid\Layouts\ProductList\ProductListLEditLayout;
use App\Models\Attachment;
use App\Models\Quota;

class ProductListEditScreen extends Screen
{
    /**
     * @var attachment
     */
    public $attachment;

    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(Attachment $attachment): iterable
    {
        return [
            'attachment' => $attachment,
        ];
    }


    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return 'Update your product';
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return 'Create and publish your products.';
    }

    public function permission(): ?iterable
    {
        return [
            'platform.systems.users',
        ];
    }

    /**
     * @return \Orchid\Screen\Layout[]
     */
    public function layout(): iterable
    {
        return [
            Layout::block(ProductListLEditLayout::class)
                ->title(__('Information Product'))
                ->description(__('Add a price name and some details about your product.'))
                ->commands(
                    Button::make(__('Save'))
                        ->type(Color::BASIC)
                        ->icon('bs.check-circle')
                        ->method('save')
                ),

        ];
    }


    /**
     * @throws \Exception
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save(Request $request, Quota $quota)
    {
        // Validasi input
        $request->validate([
            'attachment_id' => 'required',
            'quotas.name' => 'required|max:255',
            'quotas.price' => 'required',
            'quotas.status' => 'required',
            'quotas.description' => 'required',
        ]);

        // Cari Quota berdasarkan ID
        $quota = Quota::findOrFail($request->route('id'));

        // Mengisi model Quota dengan data dari request
        $quota->fill([
            'attachment_id' => $request->input('attachment_id'),
            'name' => $request->input('quotas.name'),
            'price' => $request->input('quotas.price'),
            'status' => $request->input('quotas.status'),
            'description' => $request->input('quotas.description'),
            'updated_by' => auth()->id(),
        ]);

        // Simpan perubahan ke dalam database
        $quota->save();

        // Berikan pesan sukses
        Toast::info(__('Data berhasil disimpan.'));

        return redirect()->route('platform.product-list');
    }
}
