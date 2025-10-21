<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VotingCategory;
use Illuminate\Support\Str;

class VotingCategoryController extends Controller
{
    /**
     * Store a newly created voting category via AJAX or form submission.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:voting_categories,name',
            'color' => 'nullable|string|max:20',
        ]);

        // Create the new category
        $category = VotingCategory::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'color' => $validated['color'] ?? '#8B5CF6', // Default purple tone
            'is_active' => true,
            'sort_order' => VotingCategory::max('sort_order') + 1,
        ]);

        // Return JSON response for AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Category created successfully!',
                'category' => $category,
            ]);
        }

        // Fallback redirect for normal form submission
        return redirect()->back()->with('success', 'Category created successfully!');
    }
}
