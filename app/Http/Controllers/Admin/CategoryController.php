<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index(): View
    {
        return view('admin.categories.index', [
            'categories' => Category::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:80', 'unique:categories,name'],
        ]);

        $category = Category::create([
            ...$validated,
            'is_active' => true,
        ]);

        AuditLog::record($request->user(), 'category.created', $category, [
            'name' => $category->name,
        ], $request->ip());

        return back()->with('status', 'Categoría creada correctamente.');
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $before = $category->only(['name', 'is_active']);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:80', Rule::unique('categories', 'name')->ignore($category)],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $category->update([
            'name' => $validated['name'],
            'is_active' => $request->boolean('is_active'),
        ]);

        AuditLog::record($request->user(), 'category.updated', $category, [
            'before' => $before,
            'after' => $category->only(['name', 'is_active']),
        ], $request->ip());

        return back()->with('status', 'Categoría actualizada correctamente.');
    }
}
