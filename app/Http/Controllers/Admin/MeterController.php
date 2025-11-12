<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class MeterController extends Controller
{
    public function index()
    {
        $meters = Customer::with('meterReadings')
            ->latest()
            ->paginate(10);
            
        return view('admin.meters.index', compact('meters'));
    }

    public function show(Customer $meter)
    {
        $meterReadings = $meter->meterReadings()->latest()->paginate(10);
        return view('admin.meters.show', compact('meter', 'meterReadings'));
    }
}