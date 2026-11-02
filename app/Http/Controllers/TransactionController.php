<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with('user');

        // Filter by Date Range (From)
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        // Filter by Date Range (To)
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filter by Search Query
        if ($request->filled('search')) {
            $searchTerm = trim($request->search);
            
            $query->where(function($q) use ($searchTerm) {
                // 1. Search by Transaction ID or Cashier Name/Full Name
                $q->where('transaction_id', 'like', '%' . $searchTerm . '%')
                  ->orWhereHas('user', function($userQuery) use ($searchTerm) {
                      $userQuery->where('firstname', 'like', '%' . $searchTerm . '%')
                                ->orWhere('lastname', 'like', '%' . $searchTerm . '%')
                                ->orWhereRaw("CONCAT(firstname, ' ', lastname) like ?", ['%' . $searchTerm . '%']);
                  });

                // 2. Safely check if the search term can be interpreted as a valid date
                if (strlen($searchTerm) >= 6) {
                    $timestamp = strtotime($searchTerm);
                    if ($timestamp !== false) {
                        $formattedDate = date('Y-m-d', $timestamp);
                        if ($formattedDate && $formattedDate !== '1970-01-01') {
                            $q->orWhereDate('created_at', $formattedDate);
                        }
                    }
                }
            });
        }

        // Set maximum pagination limit to 25 items per page
        $transactions = $query->orderBy('created_at', 'desc')->paginate(16)->withQueryString();

        return view('pos.transactionHistory', compact('transactions'));
    }

    public function showReceipt($id)
    {
        $transaction = Transaction::with(['transactionItems.product', 'user'])->findOrFail($id);

        return view('pos.receipt', compact('transaction'));
    }
}