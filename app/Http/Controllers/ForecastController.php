<?php

namespace App\Http\Controllers;
use App\Models\Product;
use App\Models\DemandRecord;
use Illuminate\Http\Request;

class ForecastController extends Controller
{

public function calculateForecast(Request $request)
{
    $data = $request->validate([

        'product_id' => ['required', 'exists:products,id'],

        'sma_period' => ['required', 'integer', 'min:1'],

        'sma_unit' => ['required', 'in:day,week,month'],

    ]);

    $product = Product::findOrFail($data['product_id']);

    $smaPeriod = $data['sma_period'];

    $smaUnit = $data['sma_unit'];

    $records = DemandRecord::where(
        'product_id',
        $product->id
    )
        ->orderBy('record_date', 'asc')
        ->get();

    $groupedDemand = [];

    foreach ($records as $record) {

        $date = \Carbon\Carbon::parse($record->record_date);

        if ($smaUnit === 'day') {

            $period = $date->format('Y-m-d');

        } elseif ($smaUnit === 'week') {

            $period = $date->format('o-W');

        } else {

            $period = $date->format('Y-m');

        }

        if (!isset($groupedDemand[$period])) {

            $groupedDemand[$period] = 0;

        }

        $groupedDemand[$period] += $record->quantity;
    }

    $groupedDemand = array_slice(
        $groupedDemand,
        -$smaPeriod,
        $smaPeriod,
        true
    );

    $totalDemand = array_sum($groupedDemand);

    $numberOfPeriods = count($groupedDemand);

    if ($numberOfPeriods > 0) {

        $sma = $totalDemand / $numberOfPeriods;

    } else {

        $sma = 0;

    }

    return view(
        'forecastResult',
        compact(
            'product',
            'records',
            'groupedDemand',
            'sma',
            'smaPeriod',
            'smaUnit'
        )
    );
}

    public function forecast()
{
    $products = Product::all();

    return view('forecast', compact('products'));
}
// --------------------------------------------------------------------------------------
    public function confirmImport(Request $request)
{
    $data = $request->validate([

        'file_path' => ['required'],

        'product_column' => ['required'],

        'date_column' => ['required'],

        'quantity_column' => ['required'],

    ]);

    $filePath = storage_path('app/private/' . $data['file_path']);

    if (!file_exists($filePath)) {

        return redirect()
            ->route('sales.import')
            ->with('error', 'The uploaded file could not be found.');

    }

    $handle = fopen($filePath, 'r');

    $headers = fgetcsv($handle);

    $importedCount = 0;

    while (($row = fgetcsv($handle)) !== false) {

        $record = array_combine($headers, $row);

        $productName = trim(
            $record[$data['product_column']]
        );

        $date = trim(
            $record[$data['date_column']]
        );

        $quantity = trim(
            $record[$data['quantity_column']]
        );

        $product = Product::where(
            'product_name',
            $productName
        )->first();

        if (!$product) {

            continue;

        }

        if (!strtotime($date)) {

            continue;

        }

        if (!is_numeric($quantity) || $quantity <= 0) {

            continue;

        }

        DemandRecord::create([

            'product_id' => $product->id,

            'record_date' => date(
                'Y-m-d',
                strtotime($date)
            ),

            'quantity' => $quantity,

            'source' => 'imported',

        ]);

        $importedCount++;

    }

    fclose($handle);

    return redirect()
        ->route('sales.import')
        ->with(
            'success',
            $importedCount . ' sales records imported successfully.'
        );
}
// --------------------------------------------------------------------------------------
    public function previewSales(Request $request)
{
    $data = $request->validate([

        'file_path' => ['required'],

        'product_column' => ['required'],

        'date_column' => ['required'],

        'quantity_column' => ['required'],

    ]);

    $filePath = storage_path('app/private/' . $data['file_path']);

    if (!file_exists($filePath)) {

        return redirect()
            ->route('sales.import')
            ->with('error', 'The uploaded file could not be found.');

    }

    $handle = fopen($filePath, 'r');

    $headers = fgetcsv($handle);

    $records = [];

    while (($row = fgetcsv($handle)) !== false) {

        $record = array_combine($headers, $row);

        $productName = trim($record[$data['product_column']]);

        $date = trim($record[$data['date_column']]);

        $quantity = trim($record[$data['quantity_column']]);

        $product = Product::where('product_name', $productName)->first();

        $status = 'Valid';

        $message = '';

        if (!$product) {

            $status = 'Invalid';

            $message = 'Product not found.';

        }

        elseif (!strtotime($date)) {

            $status = 'Invalid';

            $message = 'Invalid date.';

        }

        elseif (!is_numeric($quantity) || $quantity <= 0) {

            $status = 'Invalid';

            $message = 'Invalid quantity.';

        }

        $records[] = [

            'product_name' => $productName,

            'product_id' => $product?->id,

            'date' => $date,

            'quantity' => $quantity,

            'status' => $status,

            'message' => $message,

        ];

    }

    fclose($handle);

    return view('salesPreview', compact('records', 'data'));
}
// --------------------------------------------------------------------------------------
    public function uploadSalesFile(Request $request)
    {
        $request->validate([

            'sales_file' => ['required', 'file', 'mimes:csv,txt'],

        ]);

        $file = $request->file('sales_file');

        $filePath = $file->store('sales_imports');

        $handle = fopen($file->getRealPath(), 'r');

        $headers = fgetcsv($handle);

        fclose($handle);

        if (!$headers) {

            return redirect()
                ->route('sales.import')
                ->with('error', 'The file is empty or could not be read.');

        }

        return view('mapSalesColumns', compact('headers', 'filePath'));
    }

    public function showImportPage()
    {
        return view('importSales');
    }
}
