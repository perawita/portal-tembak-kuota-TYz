<?php

declare(strict_types=1);

namespace App\Orchid\Screens\AddInternetQuotaList;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;


use App\Orchid\Layouts\AddInternetQuotaList\AddInternetQuotaLUploadLayout;
use App\Orchid\Layouts\AddInternetQuotaList\AttachmentSelection;
use App\Orchid\Layouts\AddInternetQuotaList\AddInternetQuotaListLayout;
use App\Orchid\Layouts\AddInternetQuotaList\AddInternetQuotaListEditLayout;


use App\Models\Attachment;


class AddInternetQuotaListScreen extends Screen
{

    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): array
    {
        return [
            'attachment' => Attachment::where('name', 'like', request()->route()->parameter('attachment') ?? '%%')
                ->defaultSort('id', 'desc')
                ->paginate(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return 'Add Internet Quota List';
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return 'Add a list of your internet services.';
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
            AddInternetQuotaLUploadLayout::class,
            AttachmentSelection::class,
            AddInternetQuotaListLayout::class,

            Layout::modal('asyncEditAttachmentModal', AddInternetQuotaListEditLayout::class)
                ->async('asyncGetAttachment'),

        ];
    }

    /**
     * @return array
     */
    public function asyncGetAttachment(Attachment $Attachment): iterable
    {
        return [
            'attachment' => $Attachment,
        ];
    }

    public function remove(Request $request)
    {
        $attachment = Attachment::findOrFail($request->get('id'));

        // Hapus file terkait dari sistem penyimpanan (opsional)
        Storage::disk($attachment->disk)->delete($attachment->path . '/' . $attachment->name . '.' . $attachment->extension);

        // Hapus record dari database
        $attachment->delete();

        Toast::info(__('Attachment has been deleted.'));
    }
}
