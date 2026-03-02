<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::where('user_id', $request->user()->id)->get();
        return CategoryResource::collection($categories);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'icon' => 'nullable|string',
            'color' => 'nullable|string',
        ]);

        $category = Category::create([
            'user_id' => $request->user()->id,
            'name' => $validated['name'],
            'type' => $validated['type'],
            'icon' => $validated['icon'] ?? '📁',
            'color' => $validated['color'] ?? '#64748b',
        ]);

        return new CategoryResource($category);
    }

    public function show(Category $category)
    {
        $this->authorize('view', $category);
        return new CategoryResource($category);
    }

    public function update(Request $request, Category $category)
    {
        $this->authorize('update', $category);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'icon' => 'nullable|string',
            'color' => 'nullable|string',
        ]);

        $category->update($validated);

        return new CategoryResource($category);
    }

    public function destroy(Category $category)
    {
        $this->authorize('delete', $category);
        $category->delete();

        return response()->json(['message' => 'Category deleted successfully']);
    }

    protected function authorize($action, $model)
    {
        if ($model->user_id !== auth()->id()) {
            abort(403);
        }
    }
}
