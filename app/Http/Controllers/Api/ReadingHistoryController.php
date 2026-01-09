<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MeterReading;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReadingHistoryController extends Controller
{
    /**
     * Get meter reader's reading history
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getReadingHistory(Request $request)
    {
        $request->validate([
            'reader_name' => 'required|string|max:255',
            'date' => 'nullable|date', // Specific date, default today
            'limit' => 'nullable|integer|min:1|max:100',
        ]);

        $readerName = $request->input('reader_name');
        $date = $request->input('date', Carbon::today()->format('Y-m-d'));
        $limit = $request->input('limit', 50);

        $readings = MeterReading::whereDate('reading_date', $date)
            ->where('read_by', $readerName)
            ->with(['customer:id,customer_number,first_name,last_name,phone',
                    'meter:id,meter_number,meter_type'])
            ->orderBy('reading_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        $formattedReadings = $readings->map(function($reading) {
            return [
                'id' => $reading->id,
                'reading_date' => $reading->reading_date->format('Y-m-d H:i'),
                'reading_period' => $reading->reading_period,
                'reading_status' => $reading->reading_status,
                'current_reading' => (float) $reading->current_reading,
                'previous_reading' => (float) $reading->previous_reading,
                'consumption' => (float) $reading->consumption,
                'billed' => (bool) $reading->billed,
                'customer' => $reading->customer ? [
                    'customer_number' => $reading->customer->customer_number,
                    'name' => $reading->customer->first_name . ' ' . $reading->customer->last_name,
                    'phone' => $reading->customer->phone,
                ] : null,
                'meter' => $reading->meter ? [
                    'meter_number' => $reading->meter->meter_number,
                    'meter_type' => $reading->meter->meter_type,
                ] : null,
                'notes' => $reading->notes,
                'created_at' => $reading->created_at->format('Y-m-d H:i:s'),
            ];
        });

        // Statistics
        $totalReadings = $readings->count();
        $normalReadings = $readings->where('reading_status', 'recorded')->count();
        $exceptionReadings = $readings->where('reading_status', 'exception')->count();
        $estimatedReadings = $readings->where('reading_status', 'estimated')->count();
        $totalConsumption = $readings->where('reading_status', 'recorded')->sum('consumption');

        return response()->json([
            'success' => true,
            'date' => $date,
            'reader' => $readerName,
            'statistics' => [
                'total_readings' => $totalReadings,
                'normal_readings' => $normalReadings,
                'exception_readings' => $exceptionReadings,
                'estimated_readings' => $estimatedReadings,
                'total_consumption' => (float) $totalConsumption,
            ],
            'data' => $formattedReadings,
            'count' => $totalReadings
        ]);
    }
}
