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
                    $q->where(function ($subQuery) use ($term) {
                        $subQuery->where('product_name', 'like', '%' . $term . '%')
                                 ->orWhereHas('category', function ($catQuery) use ($term) {
                                     $catQuery->where('name', 'like', '%' . $term . '%');
                                 });
                    });
                }
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Standardized to 24 items per page for POS layout grid
        $items = $query->orderBy('product_name')->paginate(24)->withQueryString();
        
        // Fetch parent categories along with children for structured dropdown layout
        $categories = Category::whereNull('parent_id')->with('children')->orderBy('name')->get();

        $cart = session()->get('cart', []);
        $rawSubtotal = 0;
        $totalItemDiscounts = 0;

        foreach ($cart as $id => $item) {
            $itemBaseTotal = $item['selling_price'] * $item['quantity'];
            $rawSubtotal += $itemBaseTotal;

            $discType = $item['item_discount_type'] ?? 'none';
            $discVal = (float)($item['item_discount_value'] ?? 0);
            $discScope = $item['item_discount_scope'] ?? 'whole';
            $itemDiscountAmount = 0;

            if ($discType === 'percentage' && $discVal > 0) {
                $itemDiscountAmount = $itemBaseTotal * ($discVal / 100);
            } elseif ($discType === 'amount' && $discVal > 0) {
                if ($discScope === 'per_item') {
                    $itemDiscountAmount = min($itemBaseTotal, $discVal * $item['quantity']);
                } else {
                    $itemDiscountAmount = min($itemBaseTotal, $discVal);
                }
            }

            $cart[$id]['item_discount_amount'] = $itemDiscountAmount;
            $cart[$id]['subtotal'] = max(0, $itemBaseTotal - $itemDiscountAmount);
            $totalItemDiscounts += $itemDiscountAmount;
        }

        $subtotal = max(0, $rawSubtotal - $totalItemDiscounts);

        $cartDiscount = session()->get('cart_discount', ['type' => 'none', 'value' => 0, 'mode' => 'percentage']);
        $discountAmount = 0;

        if ($cartDiscount['type'] === 'senior' || $cartDiscount['type'] === 'pwd') {
            $discountAmount = $subtotal * 0.20;
        } elseif ($cartDiscount['type'] === 'percentage') {
            $discountAmount = $subtotal * ((float)($cartDiscount['value'] ?? 0) / 100);
        } elseif ($cartDiscount['type'] === 'amount') {
            $discountAmount = min($subtotal, (float)($cartDiscount['value'] ?? 0));
        } elseif ($cartDiscount['type'] === 'employee' || $cartDiscount['type'] === 'voucher') {
            if (($cartDiscount['mode'] ?? 'percentage') === 'percentage') {
                $discountAmount = $subtotal * ((float)($cartDiscount['value'] ?? 0) / 100);
            } else {
                $discountAmount = min($subtotal, (float)($cartDiscount['value'] ?? 0));
            }
        }

        $total = max(0, $subtotal - $discountAmount);

        // Fetch receipt data if a transaction was just completed to show modal
        $completedTransaction = null;
        if (session()->has('completed_transaction_id')) {
            $completedTransaction = Transaction::with(['transactionItems.product', 'user'])->find(session('completed_transaction_id'));
        }

        return view('pos.pointOfSale', compact(
            'items', 'categories', 'cart', 'rawSubtotal', 'subtotal', 
            'totalItemDiscounts', 'discountAmount', 'total', 'cartDiscount', 'completedTransaction'
        ));
    }

    public function addToCart(Request $request, $id)
    {
        $data = $request->validate(['quantity' => ['required', 'integer']]);
        $product = Product::findOrFail($id);
        $quantityChange = $data['quantity']; // Can be +1 or -1
        
        $cart = session()->get('cart', []);
        $currentCartQty = isset($cart[$id]) ? $cart[$id]['quantity'] : 0;
        $newCartQty = $currentCartQty + $quantityChange;

        if ($newCartQty < 0) {
            $newCartQty = 0;
        }

        // If trying to add more than available store stock
        if ($quantityChange > 0 && $product->store_quantity < $quantityChange) {
            return redirect()->route('pos')->with('error', 'Not enough stock for ' . $product->product_name . '. Available stock: ' . $product->store_quantity);
        }

        return DB::transaction(function () use ($product, $id, $quantityChange, $currentCartQty, $newCartQty, &$cart) {
            if ($quantityChange > 0) {
                // Decrement store stock in database instantly
                $product->decrement('store_quantity', $quantityChange);
            } else {
                // Increment store stock back when decreasing quantity in cart
                $product->increment('store_quantity', abs($quantityChange));
            }

            if ($newCartQty === 0) {
                unset($cart[$id]);
            } else {
                if (isset($cart[$id])) {
                    $cart[$id]['quantity'] = $newCartQty;
                } else {
                    $cart[$id] = [
                        'product_name' => $product->product_name,
                        'selling_price' => $product->selling_price,
                        'quantity' => $newCartQty,
                        'item_discount_type' => 'none',
                        'item_discount_value' => 0,
                        'item_discount_scope' => 'whole',
                    ];
                }
            }

            session()->put('cart', $cart);

            return redirect()->route('pos');
        });
    }

    public function removeFromCart($id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            $product = Product::find($id);
            if ($product) {
                // Return stock back to inventory
                $product->increment('store_quantity', $cart[$id]['quantity']);
            }

            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        return redirect()->route('pos');
    }

    public function clearCart()
    {
        $cart = session()->get('cart', []);
        
        foreach ($cart as $productId => $item) {
            $product = Product::find($productId);
            if ($product) {
                $product->increment('store_quantity', $item['quantity']);
            }
        }

        session()->forget(['cart', 'cart_discount']);
        return redirect()->route('pos');
    }

    public function holdOrder(Request $request)
    {
        $cart = session()->get('cart', []);
        $cartDiscount = session()->get('cart_discount', ['type' => 'none', 'value' => 0, 'mode' => 'percentage']);

        if (empty($cart)) {
            return redirect()->route('pos')->with('error', 'Cannot hold an empty order.');
        }

        $parkedOrders = session()->get('parked_orders', []);
        
        $parkedOrders[] = [
            'id' => 'HOLD-' . strtoupper(substr(uniqid(), -6)),
            'time' => now()->format('M d, h:i A'),
            'cart' => $cart,
            'cart_discount' => $cartDiscount,
            'item_count' => array_sum(array_column($cart, 'quantity')),
        ];

        session()->put('parked_orders', $parkedOrders);
        session()->forget(['cart', 'cart_discount']);

        return redirect()->route('pos');
    }

    public function restoreParkedOrder($index)
    {
        $parkedOrders = session()->get('parked_orders', []);

        if (!isset($parkedOrders[$index])) {
            return redirect()->route('pos')->with('error', 'Parked order not found.');
        }

        $parkedOrder = $parkedOrders[$index];
        $currentCart = session()->get('cart', []);

        // Merge parked items directly into cart without double-decreasing stock
        foreach ($parkedOrder['cart'] as $productId => $parkedItem) {
            if (isset($currentCart[$productId])) {
                $currentCart[$productId]['quantity'] += $parkedItem['quantity'];
            } else {
                $currentCart[$productId] = $parkedItem;
            }
        }

        $currentCartDiscount = session()->get('cart_discount', ['type' => 'none', 'value' => 0, 'mode' => 'percentage']);
        if (($currentCartDiscount['type'] ?? 'none') === 'none' && isset($parkedOrder['cart_discount'])) {
            session()->put('cart_discount', $parkedOrder['cart_discount']);
        }

        session()->put('cart', $currentCart);

        unset($parkedOrders[$index]);
        session()->put('parked_orders', array_values($parkedOrders));

        // Redirect quietly without redundant flash messages
        return redirect()->route('pos');
    }

    public function deleteParkedOrder($index)
    {
        $parkedOrders = session()->get('parked_orders', []);

        if (isset($parkedOrders[$index])) {
            $parked = $parkedOrders[$index];
            
            foreach ($parked['cart'] as $productId => $item) {
                $product = Product::find($productId);
                if ($product) {
                    $product->increment('store_quantity', $item['quantity']);
                }
            }

            unset($parkedOrders[$index]);
            session()->put('parked_orders', array_values($parkedOrders));
        }

        return redirect()->route('pos');
    }

    public function applyDiscount(Request $request)
    {
        $data = $request->validate([
            'discount_type' => ['required', 'string', 'in:senior,pwd,employee,voucher,percentage,amount,none'],
            'customer_name' => ['nullable', 'string'],
            'id_number' => ['nullable', 'string'],
            'tin_number' => ['nullable', 'string'],
            'employee_name' => ['nullable', 'string'],
            'employee_id' => ['nullable', 'string'],
            'voucher_code' => ['nullable', 'string'],
            'percentage_value' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'amount_value' => ['nullable', 'numeric', 'min:0'],
            'employee_mode' => ['nullable', 'string', 'in:percentage,amount'],
            'employee_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'employee_amount' => ['nullable', 'numeric', 'min:0'],
            'voucher_mode' => ['nullable', 'string', 'in:percentage,amount'],
            'voucher_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'voucher_amount' => ['nullable', 'numeric', 'min:0'],
        ]);

        $type = $data['discount_type'];

        if ($type === 'none') {
            session()->forget('cart_discount');
            return redirect()->route('pos');
        }

        $value = 0;
        $mode = 'percentage';

        if ($type === 'percentage') {
            $value = $data['percentage_value'] ?? 0;
        } elseif ($type === 'amount') {
            $value = $data['amount_value'] ?? 0;
        } elseif ($type === 'employee') {
            $mode = $data['employee_mode'] ?? 'percentage';
            $value = $mode === 'percentage' ? ($data['employee_percentage'] ?? 0) : ($data['employee_amount'] ?? 0);
        } elseif ($type === 'voucher') {
            $mode = $data['voucher_mode'] ?? 'percentage';
            $value = $mode === 'percentage' ? ($data['voucher_percentage'] ?? 0) : ($data['voucher_amount'] ?? 0);
        }

        session()->put('cart_discount', array_merge($data, [
            'type' => $type,
            'value' => $value,
            'mode' => $mode,
        ]));

        return redirect()->route('pos');
    }

    public function applyItemDiscount(Request $request, $id)
    {
        $data = $request->validate([
            'item_discount_type' => ['required', 'string', 'in:percentage,amount,none'],
            'item_discount_value' => ['nullable', 'numeric', 'min:0'],
            'item_discount_scope' => ['nullable', 'string', 'in:whole,per_item'],
        ]);

        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            $cart[$id]['item_discount_type'] = $data['item_discount_type'];
            $cart[$id]['item_discount_value'] = (float)($data['item_discount_value'] ?? 0);
            $cart[$id]['item_discount_scope'] = $data['item_discount_scope'] ?? 'whole';
            session()->put('cart', $cart);
        }

        return redirect()->route('pos');
    }

    // ==================== CHECKOUT ====================

    public function checkout()
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('pos');
        }

        $rawSubtotal = 0;
        $totalItemDiscounts = 0;
        $discountedItemsList = [];

        foreach ($cart as $id => $item) {
            $baseItemSubtotal = $item['selling_price'] * $item['quantity'];
            $rawSubtotal += $baseItemSubtotal;

            $itemDiscountAmount = 0;
            $discType = $item['item_discount_type'] ?? 'none';
            $discVal = (float)($item['item_discount_value'] ?? 0);
            $discScope = $item['item_discount_scope'] ?? 'whole';

            if ($discType === 'percentage' && $discVal > 0) {
                $itemDiscountAmount = $baseItemSubtotal * ($discVal / 100);
            } elseif ($discType === 'amount' && $discVal > 0) {
                if ($discScope === 'per_item') {
                    $itemDiscountAmount = min($baseItemSubtotal, $discVal * $item['quantity']);
                } else {
                    $itemDiscountAmount = min($baseItemSubtotal, $discVal);
                }
            }

            $cart[$id]['item_discount_amount'] = $itemDiscountAmount;
            $cart[$id]['subtotal'] = max(0, $baseItemSubtotal - $itemDiscountAmount);
            $totalItemDiscounts += $itemDiscountAmount;

            if ($discType !== 'none' && $itemDiscountAmount > 0) {
                $discountedItemsList[] = $cart[$id];
            }
        }

        $subtotal = max(0, $rawSubtotal - $totalItemDiscounts);

        $cartDiscount = session()->get('cart_discount', ['type' => 'none', 'value' => 0, 'mode' => 'percentage']);
        $discountAmount = 0;

        if ($cartDiscount['type'] === 'senior' || $cartDiscount['type'] === 'pwd') {
            $discountAmount = $subtotal * 0.20;
        } elseif ($cartDiscount['type'] === 'percentage') {
            $discountAmount = $subtotal * ((float)($cartDiscount['value'] ?? 0) / 100);
        } elseif ($cartDiscount['type'] === 'amount') {
            $discountAmount = min($subtotal, (float)($cartDiscount['value'] ?? 0));
        } elseif ($cartDiscount['type'] === 'employee' || $cartDiscount['type'] === 'voucher') {
            if (($cartDiscount['mode'] ?? 'percentage') === 'percentage') {
                $discountAmount = $subtotal * ((float)($cartDiscount['value'] ?? 0) / 100);
            } else {
                $discountAmount = min($subtotal, (float)($cartDiscount['value'] ?? 0));
            }
        }

        $totalAllDiscounts = $totalItemDiscounts + $discountAmount;
        $total = max(0, $subtotal - $discountAmount);

        return view('pos.checkout', compact(
            'cart', 'rawSubtotal', 'subtotal', 'totalItemDiscounts', 
            'discountAmount', 'totalAllDiscounts', 'total', 'cartDiscount', 'discountedItemsList'
        ));
    }

    public function processCheckout(Request $request)
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('pos');
        }

        $rawSubtotal = 0;
        $totalItemDiscounts = 0;
        foreach ($cart as $item) {
            $baseItemSubtotal = $item['selling_price'] * $item['quantity'];
            $rawSubtotal += $baseItemSubtotal;

            $itemDiscountAmount = 0;
            $discType = $item['item_discount_type'] ?? 'none';
            $discVal = (float)($item['item_discount_value'] ?? 0);
            $discScope = $item['item_discount_scope'] ?? 'whole';

            if ($discType === 'percentage' && $discVal > 0) {
                $itemDiscountAmount = $baseItemSubtotal * ($discVal / 100);
            } elseif ($discType === 'amount' && $discVal > 0) {
                if ($discScope === 'per_item') {
                    $itemDiscountAmount = min($baseItemSubtotal, $discVal * $item['quantity']);
                } else {
                    $itemDiscountAmount = min($baseItemSubtotal, $discVal);
                }
            }

            $totalItemDiscounts += $itemDiscountAmount;
        }

        $subtotal = max(0, $rawSubtotal - $totalItemDiscounts);

        $cartDiscount = session()->get('cart_discount', ['type' => 'none', 'value' => 0, 'mode' => 'percentage']);
        $discountAmount = 0;

        if ($cartDiscount['type'] === 'senior' || $cartDiscount['type'] === 'pwd') {
            $discountAmount = $subtotal * 0.20;
        } elseif ($cartDiscount['type'] === 'percentage') {
            $discountAmount = $subtotal * ((float)($cartDiscount['value'] ?? 0) / 100);
        } elseif ($cartDiscount['type'] === 'amount') {
            $discountAmount = min($subtotal, (float)($cartDiscount['value'] ?? 0));
        } elseif ($cartDiscount['type'] === 'employee' || $cartDiscount['type'] === 'voucher') {
            if (($cartDiscount['mode'] ?? 'percentage') === 'percentage') {
                $discountAmount = $subtotal * ((float)($cartDiscount['value'] ?? 0) / 100);
            } else {
                $discountAmount = min($subtotal, (float)($cartDiscount['value'] ?? 0));
            }
        }

        $total = max(0, $subtotal - $discountAmount);

        $data = $request->validate([
            'amount_paid' => ['required', 'numeric', 'min:' . $total],
        ]);

        $amountPaid = $data['amount_paid'];
        $change = $amountPaid - $total;
        $transactionId = 'POS-' . now()->format('YmdHis') . '-' . uniqid();

        $transaction = DB::transaction(function () use ($cart, $total, $amountPaid, $change, $transactionId, $cartDiscount, $discountAmount) {

            $transaction = Transaction::create([
                'user_id' => Auth::id(),
                'transaction_id' => $transactionId,
                'transaction_type' => 0,
                'total_amount' => $total,
                'amount_paid' => $amountPaid,
                'amount_change' => $change,
                'cart_discount_type' => $cartDiscount['type'] ?? 'none',
                'cart_discount_value' => $discountAmount,
                'customer_name' => $cartDiscount['customer_name'] ?? null,
            ]);

            foreach ($cart as $productId => $item) {
                $product = Product::findOrFail($productId);

                $baseItemSubtotal = $item['selling_price'] * $item['quantity'];
                $itemDiscountAmount = 0;
                
                $discType = $item['item_discount_type'] ?? 'none';
                $discVal = (float)($item['item_discount_value'] ?? 0);
                $discScope = $item['item_discount_scope'] ?? 'whole';

                if ($discType === 'percentage' && $discVal > 0) {
                    $itemDiscountAmount = $baseItemSubtotal * ($discVal / 100);
                } elseif ($discType === 'amount' && $discVal > 0) {
                    if ($discScope === 'per_item') {
                        $itemDiscountAmount = min($baseItemSubtotal, $discVal * $item['quantity']);
                    } else {
                        $itemDiscountAmount = min($baseItemSubtotal, $discVal);
                    }
                }

                $subtotalItem = max(0, $baseItemSubtotal - $itemDiscountAmount);

                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $product->id,
                    'product_name' => $product->product_name,
                    'selling_price' => $item['selling_price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $subtotalItem,
                    'item_discount_type' => ($discType !== 'none' && $discVal > 0) ? $discType : null,
                    'item_discount_value' => $discVal,
                    'item_discount_scope' => ($discType !== 'none' && $discVal > 0) ? $discScope : null,
                ]);

                DemandRecord::create([
                    'product_id' => $product->id,
                    'transaction_id' => $transaction->id,
                    'record_date' => now()->toDateString(),
                    'quantity' => $item['quantity'],
                    'source' => 'pos',
                ]);
            }

            return $transaction;
        });

        session()->forget(['cart', 'cart_discount']);
        
        return redirect()->route('receipt', $transaction->id);
    }
}