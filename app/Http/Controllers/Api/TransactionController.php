<?php

namespace App\Http\Controllers\Api;

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

    public function update(Request $request,$id){
        $transaction =Transaction::findOrFail($id);
        $transaction->update($request->all());
        return ResponseFormatter::success($transaction,"Transaction Has Been Updated");
    }
}
