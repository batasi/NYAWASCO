<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PricingTier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Models\MeterCategory;

class MeterCategoryController extends Controller
{
    public function index()
    {
        $categories = MeterCategory::withCount('meters')
            ->with('pricingTiers')
            ->ordered()
            ->paginate(20);

        return view('admin.meter-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.meter-categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:meter_categories,name',
            'code' => 'required|string|max:10|unique:meter_categories,code',
            'description' => 'nullable|string|max:500',
            'base_charge' => 'required|numeric|min:0',
            'meter_rent' => 'required|numeric|min:0',
            'has_tiers' => 'boolean',
            'default_rate' => 'required|numeric|min:0',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
            'installation_fee' => 'nullable|numeric|min:0',
            'connection_fee' => 'nullable|numeric|min:0',
            'deposit' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::transaction(function () use ($validated) {
                // Prepare additional charges
                $additionalCharges = [
                    'installation_fee' => $validated['installation_fee'] ?? 0,
                    'connection_fee' => $validated['connection_fee'] ?? 0,
                    'deposit' => $validated['deposit'] ?? 0,
                ];

                $category = MeterCategory::create([
                    'name' => $validated['name'],
                    'code' => $validated['code'],
                    'description' => $validated['description'],
                    'base_charge' => $validated['base_charge'],
                    'meter_rent' => $validated['meter_rent'],
                    'has_tiers' => $validated['has_tiers'] ?? false,
                    'default_rate' => $validated['default_rate'],
                    'is_active' => $validated['is_active'] ?? true,
                    'sort_order' => $validated['sort_order'] ?? 0,
                    'additional_charges' => $additionalCharges,
                ]);

                // If category has tiers, create default tier
                if ($category->has_tiers) {
                    PricingTier::create([
                        'meter_category_id' => $category->id,
                        'name' => 'Default Tier',
                        'min_consumption' => 0,
                        'max_consumption' => null,
                        'rate_per_unit' => $category->default_rate,
                        'description' => 'Default pricing tier',
                        'sort_order' => 1,
                        'is_active' => true,
                    ]);
                }
            });

            return redirect()->route('admin.meter-categories.index')
                ->with('success', 'Meter category created successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating category: ' . $e->getMessage());
        }
    }

    public function show(MeterCategory $meterCategory)
    {
        $meterCategory->load(['pricingTiers', 'meters.customer']);
        return view('admin.meter-categories.show', compact('meterCategory'));
    }

    public function edit(MeterCategory $meterCategory)
    {
        $meterCategory->load('pricingTiers');
        return view('admin.meter-categories.edit', compact('meterCategory'));
    }

    public function update(Request $request, MeterCategory $meterCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:meter_categories,name,' . $meterCategory->id,
            'code' => 'required|string|max:10|unique:meter_categories,code,' . $meterCategory->id,
            'description' => 'nullable|string|max:500',
            'base_charge' => 'required|numeric|min:0',
            'meter_rent' => 'required|numeric|min:0',
            'has_tiers' => 'boolean',
            'default_rate' => 'required|numeric|min:0',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
            'installation_fee' => 'nullable|numeric|min:0',
            'connection_fee' => 'nullable|numeric|min:0',
            'deposit' => 'nullable|numeric|min:0',
        ]);

        try {
            // Prepare additional charges
            $additionalCharges = [
                'installation_fee' => $validated['installation_fee'] ?? 0,
                'connection_fee' => $validated['connection_fee'] ?? 0,
                'deposit' => $validated['deposit'] ?? 0,
            ];

            $meterCategory->update([
                'name' => $validated['name'],
                'code' => $validated['code'],
                'description' => $validated['description'],
                'base_charge' => $validated['base_charge'],
                'meter_rent' => $validated['meter_rent'],
                'has_tiers' => $validated['has_tiers'] ?? false,
                'default_rate' => $validated['default_rate'],
                'is_active' => $validated['is_active'] ?? true,
                'sort_order' => $validated['sort_order'] ?? 0,
                'additional_charges' => $additionalCharges,
            ]);

            return redirect()->route('admin.meter-categories.index')
                ->with('success', 'Meter category updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating category: ' . $e->getMessage());
        }
    }

    public function destroy(MeterCategory $meterCategory)
    {
        if ($meterCategory->meters()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete category that has meters assigned. Please reassign meters first.');
        }

        $meterCategory->delete();

        return redirect()->route('admin.meter-categories.index')
            ->with('success', 'Meter category deleted successfully!');
    }

    // Pricing Tiers Management
    public function storeTier(Request $request, MeterCategory $meterCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'min_consumption' => 'required|numeric|min:0',
            'max_consumption' => 'nullable|numeric|min:0|gt:min_consumption',
            'rate_per_unit' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        // Check for overlapping tiers
        $overlappingTier = PricingTier::where('meter_category_id', $meterCategory->id)
            ->where(function($query) use ($validated) {
                $query->where(function($q) use ($validated) {
                    $q->where('min_consumption', '<=', $validated['min_consumption'])
                      ->where(function($q2) use ($validated) {
                          $q2->where('max_consumption', '>=', $validated['min_consumption'])
                             ->orWhereNull('max_consumption');
                      });
                })->orWhere(function($q) use ($validated) {
                    if ($validated['max_consumption']) {
                        $q->where('min_consumption', '<=', $validated['max_consumption'])
                          ->where(function($q2) use ($validated) {
                              $q2->where('max_consumption', '>=', $validated['max_consumption'])
                                 ->orWhereNull('max_consumption');
                          });
                    }
                });
            })
            ->first();

        if ($overlappingTier) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'This tier overlaps with existing tier: ' . $overlappingTier->name);
        }

        PricingTier::create([
            'meter_category_id' => $meterCategory->id,
            'name' => $validated['name'],
            'min_consumption' => $validated['min_consumption'],
            'max_consumption' => $validated['max_consumption'],
            'rate_per_unit' => $validated['rate_per_unit'],
            'description' => $validated['description'],
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->back()
            ->with('success', 'Pricing tier added successfully!');
    }

    public function updateTier(Request $request, MeterCategory $meterCategory, PricingTier $pricingTier)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'min_consumption' => 'required|numeric|min:0',
            'max_consumption' => 'nullable|numeric|min:0|gt:min_consumption',
            'rate_per_unit' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        // Check for overlapping tiers (excluding current tier)
        $overlappingTier = PricingTier::where('meter_category_id', $meterCategory->id)
            ->where('id', '!=', $pricingTier->id)
            ->where(function($query) use ($validated) {
                $query->where(function($q) use ($validated) {
                    $q->where('min_consumption', '<=', $validated['min_consumption'])
                      ->where(function($q2) use ($validated) {
                          $q2->where('max_consumption', '>=', $validated['min_consumption'])
                             ->orWhereNull('max_consumption');
                      });
                })->orWhere(function($q) use ($validated) {
                    if ($validated['max_consumption']) {
                        $q->where('min_consumption', '<=', $validated['max_consumption'])
                          ->where(function($q2) use ($validated) {
                              $q2->where('max_consumption', '>=', $validated['max_consumption'])
                                 ->orWhereNull('max_consumption');
                          });
                    }
                });
            })
            ->first();

        if ($overlappingTier) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'This tier overlaps with existing tier: ' . $overlappingTier->name);
        }

        $pricingTier->update($validated);

        return redirect()->back()
            ->with('success', 'Pricing tier updated successfully!');
    }

    public function destroyTier(MeterCategory $meterCategory, PricingTier $pricingTier)
    {
        $pricingTier->delete();

        return redirect()->back()
            ->with('success', 'Pricing tier deleted successfully!');
    }

    public function calculateCharge(Request $request, MeterCategory $meterCategory)
    {
        $request->validate([
            'consumption' => 'required|numeric|min:0'
        ]);

        $consumption = $request->consumption;
        
        $consumptionCharge = $meterCategory->calculateCharge($consumption);
        $baseCharge = $meterCategory->base_charge;
        $meterRent = $meterCategory->meter_rent;
        $totalCharge = $baseCharge + $meterRent + $consumptionCharge;

        return response()->json([
            'success' => true,
            'consumption' => $consumption,
            'base_charge' => $baseCharge,
            'meter_rent' => $meterRent,
            'consumption_charge' => $consumptionCharge,
            'total_charge' => $totalCharge,
        ]);
    }
}