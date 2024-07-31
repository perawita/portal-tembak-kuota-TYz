<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Storage;

class shell_exec
{
    public static function execute($path, $choice)
    {
        // Konstruksi path lengkap ke dalam direktori storage
        $fullPath = storage_path('app/' . $path);

        if (Storage::exists($path)) {
            $fileContent = Storage::get($path);
            // Lakukan pengecekan apakah file adalah skrip PHP
            if (strpos($fileContent, '<?php') === 0) {
                // Jika iya, eksekusi skrip dengan menyediakan input
                ob_start();
                // Memasukkan nomor pilihan ke dalam environment variable
                session(['pay' => $choice]);
                include $fullPath;
                $output = ob_get_clean();
                return $output;
            } else {
                return 'File bukan skrip PHP.';
            }
        } else {
            return 'takda ' . $path;
        }
    }
}
