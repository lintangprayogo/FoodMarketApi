<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{

    public function index()
    {
        $transactions = Transaction::paginate(10);
        return view('transaction.index', [
            'transactions' => $transactions
        ]);
    }


    public function create()
    {
        //
    }


    public function store(Request $request)
    {
    }


    public function show($id)
    {
        $transaction = Transaction::findOrFail($id);
        return view('transaction.detail',[
            'transaction' => $transaction
        ]);
    
    }


    public function edit($id)
    {
        //
    }


    public function update(Request $request, $id)
    {
        //
    }


    public function destroy($id)
    {
        //
    }

    public function changeStatus($id,$status){
          $transaction=Transaction::findOrFail($id);
          $transaction->status=$status;
          $transaction->save();
          return redirect()->route('transaction.show', $id);
    }
}
