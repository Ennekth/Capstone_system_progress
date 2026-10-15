<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with('user');

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $query->where('transaction_id', 'like', '%' . $request->search . '%');
        }

        $transactions = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        return view('pos.transactionHistory', compact('transactions'));
    }
}