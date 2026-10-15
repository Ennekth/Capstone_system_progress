<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\DemandRecord;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PosController extends Controller
{
    // ==================== POS / CART ====================

    public function index(Request $request)
    {
        $query = Product::with('category');

        if ($request->filled('search')) {
            $searchTerms = array_filter(explode(' ', trim($request->search)));
            $query->where(function ($q) use ($searchTerms) {
                foreach ($searchTerms as $term) {
                    $q->where('product_name', 'like', '%' . $term . '%');
                }
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $items = $query->orderBy('product_name')->paginate(24)->withQueryString();
        $categories = Category::orderBy('name')->get();

        $cart = session()->get('cart', []);
        $total = 0;

        foreach ($cart as $id => $item) {
            $subtotal = $item['selling_price'] * $item['quantity'];
            $cart[$id]['subtotal'] = $subtotal;
            $total += $subtotal;
        }

        return view('pos.pointOfSale', compact('items', 'categories', 'cart', 'total'));
    }

    public function addToCart(Request $request, $id)
    {
        $data = $request->validate(['quantity' => ['required', 'integer', 'min:1']]);
        $product = Product::findOrFail($id);
        $quantity = $data['quantity'];
        $cart = session()->get('cart', []);
        $currentQuantity = isset($cart[$id]) ? $cart[$id]['quantity'] : 0;
        $newTotalQuantity = $currentQuantity + $quantity;

        if ($newTotalQuantity > $product->store_quantity) {
            return redirect()->route('pos')
                ->with('error', 'Not enough stock for ' . $product->product_name . '. Available stock: ' . $product->store_quantity);
        }

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] += $quantity;
        } else {
            $cart[$id] = [
                'product_name' => $product->product_name,
                'selling_price' => $product->selling_price,
                'quantity' => $quantity,
            ];
        }

        session()->put('cart', $cart);

        return redirect()->route('pos')->with('success', $product->product_name . ' added to cart.');
    }

    public function removeFromCart($id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        return redirect()->back();
    }

    // ==================== CHECKOUT ====================

    public function checkout()
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('pos');
        }

        $total = 0;
        foreach ($cart as $item) {
            $total += $item['selling_price'] * $item['quantity'];
        }

        return view('pos.checkout', compact('cart', 'total'));
    }

    public function processCheckout(Request $request)
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('pos');
        }

        $total = 0;
        foreach ($cart as $item) {
            $total += $item['selling_price'] * $item['quantity'];
        }

        $data = $request->validate([
            'amount_paid' => ['required', 'numeric', 'min:' . $total],
        ]);

        $amountPaid = $data['amount_paid'];
        $change = $amountPaid - $total;
        $transactionId = 'POS-' . now()->format('YmdHis') . '-' . uniqid();

        $transaction = DB::transaction(function () use ($cart, $total, $amountPaid, $change, $transactionId) {

            $transaction = Transaction::create([
                'user_id' => Auth::id(),
                'transaction_id' => $transactionId,
                'transaction_type' => 0,
                'total_amount' => $total,
                'amount_paid' => $amountPaid,
                'amount_change' => $change,
            ]);

            foreach ($cart as $productId => $item) {
                $product = Product::findOrFail($productId);

                if ($product->store_quantity < $item['quantity']) {
                    throw new \Exception('Not enough stock for ' . $product->product_name);
                }

                $subtotal = $item['selling_price'] * $item['quantity'];

                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $product->id,
                    'product_name' => $product->product_name,
                    'selling_price' => $item['selling_price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $subtotal,
                ]);

                DemandRecord::create([
                    'product_id' => $product->id,
                    'transaction_id' => $transaction->id,
                    'record_date' => now()->toDateString(),
                    'quantity' => $item['quantity'],
                    'source' => 'pos',
                ]);

                $product->decrement('store_quantity', $item['quantity']);
            }

            return $transaction;
        });

        session()->forget('cart');
        return redirect()->route('receipt', $transaction->id);
    }

    public function receipt($id)
    {
        $transaction = Transaction::with('transactionItems')->findOrFail($id);
        return view('pos.receipt', compact('transaction'));
    }
}