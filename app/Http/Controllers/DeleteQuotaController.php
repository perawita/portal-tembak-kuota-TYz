<?php

namespace App\Http\Controllers;


use App\Http\Services\delete_quota;

class DeleteQuotaController
{
    public function index()
    {
        $delete_quota_service = new delete_quota(session('filename'));
        $get_list_quota = $delete_quota_service->getQuotaDetails();

        return json_decode($get_list_quota, true);
    }

    public function delete($id)
    {
        $delete_quota_service = new delete_quota(session('filename'));
        $delete = $delete_quota_service->unsubscribePackage($id);

        return $delete;
    }
}
