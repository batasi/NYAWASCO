<?php

namespace App\Http\Controllers;

use App\Models\NomineeCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NomineeCategoryController extends Controller
{
    /**
     * Display a listing of the nominee categories.
     */
    public function index()
    {
        $categories = NomineeCategory::orderBy('name')->get();
        return view('nominee_categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new nominee category.
     */
    public function create()
    {
        return view('nominee_categories.create');
    }

    /**
     * Store a newly created nominee category in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:nominee_categories,slug',
            'color' => 'nullable|string|max:7', // for hex color code
        ]);

        $slug = $request->slug ?? Str::slug($request->name);

        $category = NomineeCategory::create([
            'name' => $request->name,
            'slug' => $slug,
            'color' => $request->color ?? '#8B5CF6',
            'is_active' => true,
        ]);

        // Handle AJAX request from voting create/edit page
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'category' => $category
            ]);
        }

        return redirect()->route('nominee-categories.index')
                         ->with('success', 'Category created successfully.');
    }

    /**
     * Show the form for editing the specified nominee category.
     */
    public function edit(NomineeCategory $nomineeCategory)
    {
        return view('nominee_categories.edit', compact('nomineeCategory'));
    }

    /**
     * Update the specified nominee category in storage.
     */
    public function update(Request $request, NomineeCategory $nomineeCategory)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:nominee_categories,slug,' . $nomineeCategory->id,
            'color' => 'nullable|string|max:7',
        ]);

        $nomineeCategory->update([
            'name' => $request->name,
            'slug' => $request->slug ?? Str::slug($request->name),
            'color' => $request->color ?? $nomineeCategory->color,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('nominee-categories.index')
                         ->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified nominee category from storage.
     */
    public function destroy(NomineeCategory $nomineeCategory)
    {
        $nomineeCategory->delete();
        return redirect()->route('nominee-categories.index')
                         ->with('success', 'Category deleted successfully.');
    }
}
