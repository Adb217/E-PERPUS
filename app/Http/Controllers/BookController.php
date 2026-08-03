<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;

class BookController extends Controller
{
    public function index(Request $request)
{
    $isSearching = $request->filled('search') || $request->filled('category');

    $categories = Category::orderBy('name')->get(['id', 'name', 'slug']);

    if ($isSearching) {
        $books = Book::query()
            ->with('category')
            ->withCount([
                'copies as available_copies_count' => fn ($q) => $q->where('status', 'tersedia')->where('condition', 'baik'),
                'copies as total_copies_count',
            ])
            ->when($request->search, function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('title', 'ilike', "%{$request->search}%")
                      ->orWhere('author', 'ilike', "%{$request->search}%");
                });
            })
            ->when($request->category, function ($query) use ($request) {
                $query->whereHas('category', fn ($q) => $q->where('slug', $request->category));
            })
            ->orderBy('title')
            ->paginate(12)
            ->withQueryString();

        return Inertia::render('Catalog/Index', [
            'mode' => 'search',
            'books' => $books,
            'categories' => $categories,
            'filters' => $request->only(['search', 'category']),
        ]);
    }

    // Mode browse: slider per kategori
    $categorySections = $categories->map(function ($category) {
        $books = Book::where('category_id', $category->id)
            ->with('category')
            ->withCount([
                'copies as available_copies_count' => fn ($q) => $q->where('status', 'tersedia')->where('condition', 'baik'),
                'copies as total_copies_count',
            ])
            ->latest()
            ->take(10)
            ->get();

        return [
            'id' => $category->id,
            'name' => $category->name,
            'slug' => $category->slug,
            'books' => $books,
        ];
    })->filter(fn ($section) => $section['books']->count() > 0)->values();

    return Inertia::render('Catalog/Index', [
        'mode' => 'browse',
        'categorySections' => $categorySections,
        'categories' => $categories,
        'filters' => [],
    ]);
}
    public function create()
{
    return Inertia::render('Books/Create', [
        'categories' => Category::orderBy('name')->get(['id', 'name']),
    ]);
}

public function store(Request $request)
{
    $validated = $request->validate([
        'category_id' => 'nullable|exists:categories,id',
        'new_category' => 'nullable|string|max:255',
        'title' => 'required|string|max:255',
        'author' => 'required|string|max:255',
        'publisher' => 'nullable|string|max:255',
        'publish_year' => 'nullable|integer|min:1900|max:' . date('Y'),
        'isbn' => 'nullable|string|max:50',
        'synopsis' => 'nullable|string',
        'page_count' => 'nullable|integer|min:1',
        'shelf_location' => 'nullable|string|max:100',
        'language' => 'required|string|max:50',
        'copies_count' => 'required|integer|min:1|max:100',
    ]);

    if (empty($validated['category_id']) && empty($validated['new_category'])) {
        return back()->withErrors(['category_id' => 'Pilih kategori atau buat kategori baru.']);
    }

    if (! empty($validated['new_category'])) {
        $category = Category::firstOrCreate(
            ['slug' => \Illuminate\Support\Str::slug($validated['new_category'])],
            ['name' => $validated['new_category']]
        );
        $validated['category_id'] = $category->id;
    }

    $book = Book::create(collect($validated)->except(['new_category', 'copies_count'])->toArray());

    $bookNumber = str_pad($book->id, 4, '0', STR_PAD_LEFT);
    for ($i = 1; $i <= $validated['copies_count']; $i++) {
        $copyNumber = str_pad($i, 2, '0', STR_PAD_LEFT);
        $book->copies()->create(['copy_code' => "BK-{$bookNumber}-{$copyNumber}"]);
    }

    return redirect()->route('catalog.index')->with('success', 'Buku berhasil ditambahkan.');
}

public function edit(Book $book)
{
    return Inertia::render('Books/Edit', [
        'book' => $book->load('copies'),
        'categories' => Category::orderBy('name')->get(['id', 'name']),
    ]);
}

public function update(Request $request, Book $book)
{
    $validated = $request->validate([
        'category_id' => 'required|exists:categories,id',
        'title' => 'required|string|max:255',
        'author' => 'required|string|max:255',
        'publisher' => 'nullable|string|max:255',
        'publish_year' => 'nullable|integer|min:1900|max:' . date('Y'),
        'isbn' => 'nullable|string|max:50',
        'synopsis' => 'nullable|string',
        'page_count' => 'nullable|integer|min:1',
        'shelf_location' => 'nullable|string|max:100',
        'language' => 'required|string|max:50',
    ]);

    $book->update($validated);

    return redirect()->route('catalog.index')->with('success', 'Buku berhasil diperbarui.');
}

public function destroy(Book $book)
{
    if ($book->copies()->where('status', 'dipinjam')->exists()) {
        return back()->withErrors(['book' => 'Tidak bisa menghapus buku, masih ada eksemplar yang dipinjam.']);
    }

    $book->delete();

    return redirect()->route('catalog.index')->with('success', 'Buku berhasil dihapus.');
}

public function addCopy(Request $request, Book $book)
{
    $count = $book->copies()->count();
    $bookNumber = str_pad($book->id, 4, '0', STR_PAD_LEFT);
    $copyNumber = str_pad($count + 1, 2, '0', STR_PAD_LEFT);

    $book->copies()->create(['copy_code' => "BK-{$bookNumber}-{$copyNumber}"]);

    return back()->with('success', 'Eksemplar baru ditambahkan.');
}

public function show(Book $book)
{
    $book->load(['category', 'copies']);

    return Inertia::render('Books/Show', [
        'book' => $book,
    ]);
}
}