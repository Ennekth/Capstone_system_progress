<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\DemandRecord;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SalesRecordController extends Controller
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

        if ($request->filled('source')) {
            $query->where('transaction_type', $request->source === 'imported' ? 1 : 0);
        }

        $records = $query->orderBy('created_at', 'desc')->paginate(25)->withQueryString();

        $summary = [
            'today' => Transaction::whereDate('created_at', today())->sum('total_amount'),
            'week' => Transaction::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->sum('total_amount'),
            'month' => Transaction::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('total_amount'),
            'year' => Transaction::whereYear('created_at', now()->year)->sum('total_amount'),
            'overall' => Transaction::sum('total_amount'),
        ];

        return view('sales.salesRecords', compact('records', 'summary'));
    }

    public function showImport()
    {
        $categories = Category::where('type', 'product')->orderBy('name')->get();
        return view('sales.importSales', compact('categories'));
    }

    public function processImport(Request $request)
    {
       $request->validate([
        'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
        'auto_create_products' => ['nullable', 'boolean'],
        'default_category_id' => ['nullable', 'exists:categories,id'],
        ]);

        $autoCreate = $request->boolean('auto_create_products');
        $defaultCategoryId = $request->default_category_id ?: Category::getOrCreateDefault()->id;

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');

        $header = fgetcsv($handle);
        $header = array_map(fn($h) => strtolower(trim($h)), $header);

        $required = ['product_name', 'quantity', 'date'];
        foreach ($required as $col) {
            if (!in_array($col, $header)) {
                fclose($handle);
                return back()->with('error', "CSV is missing required column: {$col}. Expected columns: product_name, quantity, date, price (optional).");
            }
        }

        $imported = 0;
        $productsCreated = 0;
        $skipped = [];
        $rowNum = 1;

        DB::transaction(function () use ($handle, $header, $autoCreate, $defaultCategoryId, &$imported, &$productsCreated, &$skipped, &$rowNum) {
            while (($row = fgetcsv($handle)) !== false) {
                $rowNum++;
                $data = array_combine($header, $row);

                $productName = trim($data['product_name'] ?? '');
                $quantity = (int) ($data['quantity'] ?? 0);
                $dateRaw = trim($data['date'] ?? '');
                $priceRaw = isset($data['price']) && is_numeric($data['price']) ? (float) $data['price'] : null;

                if (empty($productName) || $quantity <= 0 || empty($dateRaw)) {
                    $skipped[] = "Row {$rowNum}: missing or invalid data";
                    continue;
                }

                $product = Product::whereRaw('LOWER(TRIM(product_name)) = ?', [strtolower($productName)])->first();

                if (!$product) {
                    if (!$autoCreate) {
                        $skipped[] = "Row {$rowNum}: product '{$productName}' not found";
                        continue;
                    }

                    $product = Product::create([
                        'product_name' => $productName,
                        'product_description' => 'Auto-created from sales import on ' . now()->format('M d, Y'),
                        'purchase_cost' => $priceRaw ?? 0,
                        'selling_price' => $priceRaw ?? 0,
                        'store_quantity' => 0,
                        'warehouse_quantity' => 0,
                        'category_id' => $defaultCategoryId,
                    ]);

                    $productsCreated++;
                }

                try {
                    $date = Carbon::parse($dateRaw);
                } catch (\Exception $e) {
                    $skipped[] = "Row {$rowNum}: invalid date '{$dateRaw}'";
                    continue;
                }

                $price = $priceRaw ?? $product->selling_price;
                $subtotal = $price * $quantity;

                $transaction = Transaction::create([
                    'user_id' => Auth::id(),
                    'transaction_id' => 'IMP-' . $date->format('Ymd') . '-' . uniqid(),
                    'transaction_type' => 1,
                    'total_amount' => $subtotal,
                    'amount_paid' => $subtotal,
                    'amount_change' => 0,
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);

                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $product->id,
                    'product_name' => $product->product_name,
                    'selling_price' => $price,
                    'quantity' => $quantity,
                    'subtotal' => $subtotal,
                ]);

                DemandRecord::create([
                    'product_id' => $product->id,
                    'transaction_id' => $transaction->id,
                    'record_date' => $date->toDateString(),
                    'quantity' => $quantity,
                    'source' => 'imported',
                ]);

                $imported++;
            }
        });

        fclose($handle);

        $message = "{$imported} sales record(s) imported successfully.";
        if ($productsCreated > 0) {
            $message .= " {$productsCreated} new product(s) auto-created.";
        }
        if (count($skipped) > 0) {
            $message .= ' ' . count($skipped) . ' row(s) skipped.';
        }

        return redirect()->route('sales.records')
            ->with('success', $message)
            ->with('skipped', $skipped);
    }

    public function destroy(Transaction $transaction)
    {
        if ($transaction->transaction_type !== 1) {
            return back()->with('error', 'Only imported sales records can be deleted here.');
        }

        $transaction->delete(); // cascades to transaction_items and demand_records

        return back()->with('success', 'Sales record deleted.');
    }
}