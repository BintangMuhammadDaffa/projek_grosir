<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->canAccessTransactionHistory()) {
            abort(403, 'Unauthorized access');
        }

        $query = Transaction::with('user');

        // Search by transaction code or customer name
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('transaction_code', 'like', '%' . $search . '%')
                  ->orWhere('customer_name', 'like', '%' . $search . '%');
            });
        }

        // Filter by payment method
        if ($request->has('payment_method') && !empty($request->payment_method)) {
            $query->where('payment_method', $request->payment_method);
        }

        // Filter by status
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        $transactions = $query->orderBy('transaction_date', 'desc')
            ->paginate(15);

        return view('transactions.index', compact('transactions'));
    }

    public function show(Transaction $transaction)
    {
        if (!auth()->user()->canAccessTransactionHistory()) {
            abort(403, 'Unauthorized access');
        }

        $transaction->load(['transactionItems', 'user']);

        return view('transactions.show', compact('transaction'));
    }

    public function edit(Transaction $transaction)
    {
        if (!auth()->user()->canAccessTransactionHistory()) {
            abort(403, 'Unauthorized access');
        }

        if ($transaction->status !== 'uncomplete') {
            abort(403, 'Hanya transaksi uncomplete yang dapat diedit');
        }

        $transaction->load(['transactionItems', 'user']);

        return view('transactions.edit', compact('transaction'));
    }

    public function update(Request $request, Transaction $transaction)
    {
        if (!auth()->user()->canAccessTransactionHistory()) {
            abort(403, 'Unauthorized access');
        }

        if ($transaction->status !== 'uncomplete') {
            abort(403, 'Hanya transaksi uncomplete yang dapat diedit');
        }

        $request->validate([
            'payment_method' => 'required|in:cash,transfer,qris',
        ]);

        $transaction->update([
            'payment_method' => $request->payment_method,
            'status' => 'completed'
        ]);

        return redirect()->route('transactions.show', $transaction)->with('success', 'Transaksi berhasil diperbarui');
    }
}
