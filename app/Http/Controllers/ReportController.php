<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->month ?? now()->format('Y-m');

        $summary = [
            'total_borrowings_this_month' => Borrowing::where('borrowed_at', 'like', "{$month}%")->count(),
            'total_returned_on_time' => Borrowing::where('borrowed_at', 'like', "{$month}%")
    ->whereNotNull('returned_at')
    ->where('points_earned', '>', 0)
    ->count(),
            'total_overdue' => Borrowing::where('status', 'terlambat')->count(),
            'total_points_issued' => (int) User::sum('points'),
        ];

        $popularBooks = Book::query()
            ->select('books.id', 'books.title', 'books.author')
            ->join('book_copies', 'books.id', '=', 'book_copies.book_id')
            ->join('borrowings', 'book_copies.id', '=', 'borrowings.book_copy_id')
            ->selectRaw('count(borrowings.id) as borrow_count')
            ->groupBy('books.id', 'books.title', 'books.author')
            ->orderByDesc('borrow_count')
            ->take(5)
            ->get();

        $activeReaders = User::role('user')
            ->orderByDesc('points')
            ->take(5)
            ->get(['name', 'points']);

        $categoryBreakdown = DB::table('books')
            ->join('categories', 'books.category_id', '=', 'categories.id')
            ->select('categories.name')
            ->selectRaw('count(books.id) as total_books')
            ->groupBy('categories.name')
            ->orderByDesc('total_books')
            ->get();

        return Inertia::render('Reports/Index', [
            'summary' => $summary,
            'popularBooks' => $popularBooks,
            'activeReaders' => $activeReaders,
            'categoryBreakdown' => $categoryBreakdown,
            'selectedMonth' => $month,
        ]);
    }

    public function export(Request $request)
    {
        $month = $request->month ?? now()->format('Y-m');

        $borrowings = Borrowing::with(['user', 'bookCopy.book'])
            ->where('borrowed_at', 'like', "{$month}%")
            ->orderBy('borrowed_at')
            ->get();

        $filename = "laporan-peminjaman-{$month}.csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($borrowings) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Nama Siswa', 'Judul Buku', 'Kode Eksemplar', 'Tanggal Pinjam', 'Jatuh Tempo', 'Tanggal Kembali', 'Status', 'Poin']);

            foreach ($borrowings as $b) {
                fputcsv($file, [
                    $b->user->name,
                    $b->bookCopy->book->title,
                    $b->bookCopy->copy_code,
                    $b->borrowed_at,
                    $b->due_date,
                    $b->returned_at ?? '-',
                    $b->status,
                    $b->points_earned,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}