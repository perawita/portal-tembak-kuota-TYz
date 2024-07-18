<?php

namespace App\Http\Controllers;

use Carbon\Carbon;

use Illuminate\Http\Request;

use App\Http\Services\shell_exec;

use App\Models\PaymentHistory;


class TransactionController extends Controller
{
    /**
     * Display payment information using shell execution.
     *
     * This method retrieves payment information based on the provided path
     * and payment parameters using shell execution. It executes a shell command
     * with the provided path and payment parameters and returns the result.
     * The result is then returned as a JSON response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $path = $request->input('path');
        $payment = $request->input('payment');

        $shell = shell_exec::execute($path, $payment);

        return response()->json(['xdg-open' => $shell], 200);
    }

    /**
     * ==============for developer testing=====================
     * Get payment information using shell execution.
     *
     * This method retrieves payment information based on the provided path
     * and payment parameters using shell execution. It executes a shell command
     * with the provided path and payment parameters and returns the result.
     * The result is then returned as a JSON response.
     *
     * @param  string  $path
     * @param  string  $payment
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPayments($path, $payment)
    {
        $shell = shell_exec::execute($path, $payment);

        return response()->json(['xdg-open' => $shell], 200);
    }




    /**
     * Create history payments and save to databases.
     *
     * This method creates a payment history record based on the provided request data
     * and saves it to the database. The payment history includes details such as
     * the user ID, quota ID, payment URL, status, and expiration time.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createHistoryPayment(Request $request)
    {
        $id_quota = $request->input('id_quota');
        $url_payment = $request->input('url_payment');

        $currentDateTime = Carbon::now();
        $expiredAt = $currentDateTime->addMinutes(5);

        $historyModel = new PaymentHistory;

        $historyModel->fill([
            'user_id' => auth()->id(),
            'quota_id' => (int)$id_quota,
            'payment_url' => (string)$url_payment,
            'status' => 'Unpaid', // Default payment status is 'Unpaid'
            'expired_at' => $expiredAt,
            'created_at' =>  Carbon::now('Asia/Jakarta')
        ]);

        $historyModel->save();

        return response()->json(['history_new_key' => $historyModel->getKey()], 201);
    }


    /**
     * Update payment history status.
     *
     * This method updates the status of a payment history record based on the provided
     * request data and saves it to the database. The method retrieves the payment history
     * record by its ID, and then updates its status to 'Sudah dibayar' (Paid).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function updatePaymentHistory(Request $request)
    {
        $id_history = $request->input('id_history');

        $historyModel = PaymentHistory::findOrFail((int)$id_history);

        $historyModel->update(['status' => 'Sudah dibayar']);
    }
}
