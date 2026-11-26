<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index()
    {
        return view('admin.categories.index', [
            'categories' => Category::withCount('posts')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'min:2', 'max:40', $this->uniqueName()],
            'description' => ['nullable', 'string', 'max:200'],
        ]);

        // a web address for it, made from the name ("Web Design" becomes "web-design")
        $base = Str::slug($data['name']) ?: 'category';
        $slug = $base;
        $i    = 2;

        while (Category::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }

        $category = Category::forceCreate($this->onlyExistingColumns([
            'name'        => trim($data['name']),
            'slug'        => $slug,
            'description' => $data['description'] ?? '',
            'icon'        => '📁',
        ]));

        ActivityLog::record('category.created', 'Category "' . $category->name . '" was added', $category);

        return back()->with('success', 'Category "' . $category->name . '" added.');
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'min:2', 'max:40', $this->uniqueName($category)],
            'description' => ['nullable', 'string', 'max:200'],
        ]);

        // the slug stays as it is, so existing links to the category keep working
        $category->forceFill($this->onlyExistingColumns([
            'name'        => trim($data['name']),
            'description' => $data['description'] ?? '',
        ]))->save();

        ActivityLog::record('category.updated', 'Category "' . $category->name . '" was edited', $category);

        return back()->with('success', 'Category saved.');
    }

    public function destroy(Request $request, Category $category)
    {
        if (Category::count() <= 1) {
            return back()->withErrors(['action' => 'You can\'t delete the last category, because every post needs one.']);
        }

        $postCount = Post::where('category_id', $category->id)->count();

        $data = $request->validate([
            'move_to' => [Rule::requiredIf($postCount > 0), 'nullable', 'exists:categories,id', Rule::notIn([$category->id])],
        ], [
            'move_to.required' => 'Choose a category to move its ' . $postCount . ' ' . Str::plural('post', $postCount) . ' to.',
        ]);

        $name = $category->name;

        DB::transaction(function () use ($category, $data, $postCount) {
            if ($postCount > 0) {
                // moved quietly, without changing each post's "updated" date
                Post::where('category_id', $category->id)->toBase()->update(['category_id' => $data['move_to']]);
            }

            $category->delete();
        });

        ActivityLog::record('category.deleted', 'Category "' . $name . '" was deleted' . ($postCount ? ' and its ' . $postCount . ' posts were moved' : ''));

        return back()->with('success', 'Category "' . $name . '" deleted.');
    }

    /** The name must be new, ignoring upper and lower case. */
    private function uniqueName(?Category $ignore = null): \Closure
    {
        return function ($attribute, $value, $fail) use ($ignore) {
            $exists = Category::when($ignore, fn ($q) => $q->where('id', '!=', $ignore->id))
                ->whereRaw('lower(name) = ?', [mb_strtolower(trim($value))])
                ->exists();

            if ($exists) {
                $fail('A category with that name already exists.');
            }
        };
    }

    /** Keep only the columns your categories table really has, so this works with your existing table. */
    private function onlyExistingColumns(array $values): array
    {
        return array_filter(
            $values,
            fn ($value, $column) => Schema::hasColumn('categories', $column),
            ARRAY_FILTER_USE_BOTH
        );
    }
}