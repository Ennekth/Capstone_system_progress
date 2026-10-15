<?php

namespace App\Http\Controllers;

use App\Models\EoqSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EoqSettingController extends Controller
{
    public function index()
    {
        $settings = EoqSetting::first();
        return view('forecast.eoqSettings', compact('settings'));
    }

    

    public function update(Request $request)
    {
        $data = $request->validate([
            'default_ordering_cost' => ['nullable', 'numeric', 'min:0'],
            'ordering_cost_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'holding_cost_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'holding_cost_per_cbm' => ['required', 'numeric', 'min:0'],
        ]);

        EoqSetting::updateOrCreate(['id' => 1], array_merge($data, ['updated_by' => Auth::id()]));

        return back()->with('success', 'EOQ settings updated.');
    }

}