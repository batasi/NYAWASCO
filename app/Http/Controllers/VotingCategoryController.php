<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VotingCategory;
use Illuminate\Support\Str;

class VotingCategoryController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:voting_categories,name',
            'color' => 'nullable|string|max:20',
        ]);

        $category = VotingCategory::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'color' => $validated['color'] ?? '#8B5CF6',
            'is_active' => true,
            'sort_order' => (VotingCategory::max('sort_order') ?? 0) + 1,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully!',
            'category' => $category,
        ]);
    }
}
