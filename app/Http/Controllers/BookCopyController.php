<?php

namespace App\Http\Controllers;

use App\Models\BookCopy;
use Illuminate\Http\Request;

class BookCopyController extends Controller
{
    public function update(Request $request, BookCopy $copy)
    {
        $request->validate([
            'condition' => 'required|in:baik,rusak,hilang',
        ]);

        // Kalo ditandain hilang/rusak, otomatis gak bisa dipinjam lagi
        $status = $request->condition === 'baik' && $copy->status !== 'dipinjam' ? 'tersedia' : $copy->status;
        if ($request->condition !== 'baik') {
            $status = 'dipinjam'; // pakai status 'dipinjam' sebagai penanda "tidak tersedia" sementara ini
        }

        $copy->update(['condition' => $request->condition]);

        return back()->with('success', 'Kondisi eksemplar diperbarui.');
    }

    public function destroy(BookCopy $copy)
    {
        if ($copy->status === 'dipinjam') {
            return back()->withErrors(['copy' => 'Eksemplar sedang dipinjam, tidak bisa dihapus.']);
        }

        $copy->delete();

        return back()->with('success', 'Eksemplar dihapus.');
    }
}