<?php

namespace App\Http\Controllers\Api;

use Exception;

use Midtrans\Snap;
use Midtrans\Config;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


class TransactionController extends Controller
{
    public function all(Request $request)
    {
        $id = $request->input('id');
        $limit = $request->input('limit', 6);
        $food_id = $request->input('food_id');
        $status = $request->input('status');


        if ($id) {
            $transaction = Transaction::with(['food', 'user'])->find($id);
            if ($transaction) {
                return ResponseFormatter::success(
                    $transaction,
                    "Transaction Data Successfully Retrived"
                );
            } else {
                return ResponseFormatter::error(
                    null,
                    "Transaction Doesn't  Exist",
                    404
                );
            }
        }
        $transaction = Transaction::with(['food', 'user'])->where('user_id', '=', Auth::user()->id);


        if ($food_id)
            $transaction->where('food_id', '=', $food_id);
        if ($status)
            $transaction->where('status', 'like', '%' . $status . '%');

        return ResponseFormatter::success(
            $transaction->paginate($limit),
            'Transaction Data List Successfully Retrived'
        );
    }

    public function update(Request $request, $id)
    {
        $transaction = Transaction::findOrFail($id);
        $transaction->update($request->all());
        return ResponseFormatter::success($transaction, "Transaction Has Been Updated");
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'food_id' => 'required|exists:food,id',
            'user_id' => 'required|exists:user,id',
            'total' => 'required',
            'quantity' => 'required',
            'status' => 'required',
        ]);

        $transaction = Transaction::create([
            'food_id' => $request->food_id,
            'user_id' => $request->user_id,
            'quantity' => $request->quantity,
            'total' => $request->total,
            'status' => $request->status,
            'payment_url' => ''
        ]);

        Config::$clientKey = config('services.midtrans.clientKey');
        Config::$serverKey = config('services.midtrans.serverKey');
        Config::$isProduction = config('services.midtrans.isProduction');
        Config::$isSanitized = config('services.midtrans.isSanitized');
        Config::$is3ds = config('services.midtrans.is3ds');

        $transaction = Transaction::with(['food', 'user'])->where($transaction->id);

        $midtrans = [
            'transaction_details' => [
                'order_id' => $transaction->id,
                'gross_amount' => $transaction->total,
            ],
            'customer_details' => [
                'first_name' => $transaction->user->name,
                'email' => $transaction->user->email,
            ],
            'enabled_payments' => ['gopay', 'bank_transfer'],
            'vtweb'=>[]
        ];
         
        try {
            $paymentUrl = Snap::createTransaction($midtrans)->redirect_url;
            $transaction->payment_url=$paymentUrl;
            $transaction->save();
            return ResponseFormatter::success($transaction," Successful Transaction");
          }
          catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(),"Failed Transaction");
          }
    }
}
