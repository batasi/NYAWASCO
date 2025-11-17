<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MeterReading;
use App\Models\Customer;
use App\Models\Meter;
use App\Services\OCRService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class MeterReadingController extends Controller
{
    public function index()
    {
        $readings = MeterReading::with('customer', 'reader')
            ->latest()
            ->paginate(20);
            
        return view('admin.meter-readings.index', compact('readings'));
    }

    public function create(Request $request)
    {
        $customerId = $request->get('customer');
        $customer = null;
        $lastReading = null;
        $meter = null;

        if ($customerId) {
            $customer = Customer::with('meter')->findOrFail($customerId);
            $meter = $customer->meter;
            $lastReading = MeterReading::where('customer_id', $customerId)
                ->latest()
                ->first();
        }

        return view('admin.meter-readings.create', compact('customer', 'lastReading', 'meter'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'current_reading' => 'required|numeric|min:0',
            'reading_date' => 'required|date',
            'reading_image' => 'nullable|image|max:2048',
            'notes' => 'nullable|string|max:500',
        ]);

        // Get customer and meter
        $customer = Customer::findOrFail($request->customer_id);
        $meter = $customer->meter;

        if (!$meter) {
            return back()->with('error', 'Customer does not have a meter assigned.');
        }

        // Check for duplicate reading in the same period
        $readingPeriod = Carbon::parse($request->reading_date)->format('F Y');
        $existingReading = MeterReading::where('customer_id', $request->customer_id)
            ->where('reading_period', $readingPeriod)
            ->first();

        if ($existingReading) {
            return back()->withInput()->with('warning', 'Meter reading for ' . $readingPeriod . ' has already been recorded. Please wait until next month to record again.');
        }

        // Get previous reading
        $previousReading = MeterReading::where('customer_id', $request->customer_id)
            ->latest()
            ->first();

        $previousReadingValue = $previousReading ? $previousReading->current_reading : ($meter->initial_reading ?? 0);

        // Handle image upload and OCR processing
        $imagePath = null;
        $ocrReading = null;

        if ($request->hasFile('reading_image')) {
            // Store the image
            $imagePath = $request->file('reading_image')->store('meter-readings', 'public');
            
            // Perform OCR on the uploaded image
            $ocrService = new OCRService();
            $fullImagePath = storage_path('app/public/' . $imagePath);
            
            $ocrReading = $ocrService->extractMeterReading($fullImagePath);
            
            // If OCR detected a valid reading, use it (user can still override manually)
            if ($ocrReading !== null && $ocrReading > $previousReadingValue) {
                // Update the request with OCR reading
                $request->merge(['current_reading' => $ocrReading]);
                
                // Log OCR success
                \Log::info("OCR successfully detected reading: {$ocrReading} for customer: {$customer->id}");
            } else if ($ocrReading !== null) {
                // OCR detected reading but it's not valid (less than previous)
                \Log::warning("OCR detected reading {$ocrReading} is less than previous reading {$previousReadingValue} for customer: {$customer->id}");
            }
        }

        // Check if current reading is valid (after potential OCR update)
        if ($request->current_reading < $previousReadingValue) {
            return back()->withInput()->with('error', 'Current reading cannot be less than previous reading (' . number_format($previousReadingValue, 2) . ' m³).');
        }

        try {
            // Create reading
            $readingData = [
                'customer_id' => $request->customer_id,
                'current_reading' => $request->current_reading,
                'previous_reading' => $previousReadingValue,
                'reading_date' => $request->reading_date,
                'reading_type' => 'monthly',
                'reading_period' => $readingPeriod,
                'read_by' => auth()->id(),
                'reading_image' => $imagePath,
                'notes' => $request->notes,
                'consumption' => $request->current_reading - $previousReadingValue,
            ];

            // Add OCR info if available (you can store this in notes or a separate field if you add the migration later)
            if ($ocrReading !== null) {
                $readingData['notes'] = $request->notes . "\n[OCR Detected: " . $ocrReading . " m³]";
            }

            $reading = MeterReading::create($readingData);

            // Update meter's current reading
            if ($meter) {
                $meter->update([
                    'current_reading' => $request->current_reading
                ]);
            }

            // Add OCR info to success message if applicable
            $successMessage = 'Meter reading recorded successfully!';
            if ($ocrReading !== null && (float)$request->current_reading === (float)$ocrReading) {
                $successMessage .= ' (Reading automatically detected from photo)';
            }

            return redirect()->route('admin.customers.show', $customer)
                ->with('success', $successMessage);

        } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
            return back()->withInput()->with('warning', 'Meter reading for ' . $readingPeriod . ' has already been recorded. Please wait until next month to record again.');
        } catch (\Exception $e) {
            \Log::error('Error creating meter reading: ' . $e->getMessage());
            return back()->withInput()->with('error', 'An error occurred while recording the reading. Please try again.');
        }
    }

    /**
     * API endpoint for OCR processing (optional - for real-time OCR)
     */
    public function processOCR(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:2048',
        ]);

        try {
            $imagePath = $request->file('image')->store('temp-ocr', 'public');
            $fullImagePath = storage_path('app/public/' . $imagePath);
            
            $ocrService = new OCRService();
            $reading = $ocrService->extractMeterReading($fullImagePath);
            
            // Clean up temporary file
            Storage::disk('public')->delete(str_replace('temp-ocr/', '', $imagePath));
            
            return response()->json([
                'success' => true,
                'detected_reading' => $reading,
                'message' => $reading ? 'Reading detected successfully' : 'No readable meter numbers found'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('OCR API Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error processing image: ' . $e->getMessage()
            ], 500);
        }
    }
}