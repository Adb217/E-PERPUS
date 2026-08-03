<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class MyBorrowingController extends Controller
{
    public function index(Request $request)
    {
        $borrowings = Auth::user()->borrowings()
            ->with('bookCopy.book.category')
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest('borrowed_at')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('MyBorrowings/Index', [
            'borrowings' => $borrowings,
            'filters' => $request->only(['status']),
        ]);
    }
}