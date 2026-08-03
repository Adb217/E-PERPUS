<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use App\Models\BookCopy;
use App\Models\PointLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class BorrowingController extends Controller
{
    public function index()
    {
        $activeBorrowings = Borrowing::with(['user', 'bookCopy.book'])
            ->whereNull('returned_at')
            ->latest('borrowed_at')
            ->take(20)
            ->get();

        return Inertia::render('Sirkulasi/Index', [
            'activeBorrowings' => $activeBorrowings,
        ]);
    }

    public function searchSiswa(Request $request)
    {
        $users = User::role('user')
            ->when($request->q, fn ($q) => $q->where(function ($sub) use ($request) {
                $sub->where('name', 'ilike', "%{$request->q}%")
                    ->orWhere('email', 'ilike', "%{$request->q}%");
            }))
            ->limit(8)
            ->get(['id', 'name', 'email']);

        return response()->json($users);
    }

    public function searchCopy(Request $request)
{
    $copies = BookCopy::with('book')
        ->where('status', 'tersedia')
        ->where('condition', 'baik')
        ->when($request->q, fn ($q) => $q->where('copy_code', 'ilike', "%{$request->q}%")
            ->orWhereHas('book', fn ($bq) => $bq->where('title', 'ilike', "%{$request->q}%")))
        ->limit(8)
        ->get();

    return response()->json($copies);
}

    public function checkout(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'book_copy_id' => 'required|exists:book_copies,id',
        ]);

        $copy = BookCopy::findOrFail($request->book_copy_id);

        if ($copy->status !== 'tersedia') {
            return back()->withErrors(['book_copy_id' => 'Eksemplar ini sedang dipinjam.']);
        }

        Borrowing::create([
            'book_copy_id' => $copy->id,
            'user_id' => $request->user_id,
            'handled_by' => Auth::id(),
            'borrowed_at' => now(),
            'due_date' => now()->addDays(7),
            'status' => 'dipinjam',
        ]);

        $copy->update(['status' => 'dipinjam']);

        return back()->with('success', 'Peminjaman berhasil dicatat.');
    }

    public function checkin(Request $request)
    {
        $request->validate(['copy_code' => 'required|string']);

        $copy = BookCopy::where('copy_code', $request->copy_code)->first();

        if (! $copy) {
            return back()->withErrors(['copy_code' => 'Kode eksemplar tidak ditemukan.']);
        }

        $borrowing = Borrowing::where('book_copy_id', $copy->id)
            ->whereNull('returned_at')
            ->latest('borrowed_at')
            ->first();

        if (! $borrowing) {
            return back()->withErrors(['copy_code' => 'Tidak ada peminjaman aktif untuk buku ini.']);
        }

        $onTime = now()->lte($borrowing->due_date);
        $points = $onTime ? 10 : 0;

        // Status SELALU jadi 'dikembalikan' begitu buku fisik balik,
        // gak peduli telat atau nggak. Info telat/nggak cukup dibaca dari points_earned.
        $borrowing->update([
            'returned_at' => now(),
            'status' => 'dikembalikan',
            'points_earned' => $points,
        ]);

        $copy->update(['status' => 'tersedia']);

        if ($points > 0) {
            $borrowing->user->increment('points', $points);
            PointLog::create([
                'user_id' => $borrowing->user_id,
                'borrowing_id' => $borrowing->id,
                'points' => $points,
                'reason' => 'Mengembalikan buku tepat waktu',
            ]);
        }

        $message = $onTime
            ? "Pengembalian berhasil dicatat. +{$points} poin untuk siswa."
            : 'Pengembalian dicatat (terlambat, tidak dapat poin).';

        return back()->with('success', $message);
    }
}