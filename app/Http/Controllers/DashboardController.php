<?php

namespace App\Http\Controllers;

use App\Models\Badge;
use App\Models\Book;
use App\Models\Borrowing;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('superadmin')) {
            return Inertia::render('Dashboard/SuperAdmin', [
                'stats' => [
                    'total_books' => Book::count(),
                    'active_borrowings' => Borrowing::whereNull('returned_at')->count(),
                    'total_users' => \App\Models\User::role('user')->count(),
                ],
            ]);
        }

        if ($user->hasRole('admin')) {
            return Inertia::render('Dashboard/Admin', [
                'stats' => [
                    'active_borrowings' => Borrowing::whereNull('returned_at')->count(),
                    'due_today' => Borrowing::whereNull('returned_at')->whereDate('due_date', today())->count(),
                    'overdue' => Borrowing::where('status', 'terlambat')->whereNull('returned_at')->count(),
                ],
            ]);
        }

        // Siswa
        $borrowings = $user->borrowings()->with('bookCopy.book')->latest('borrowed_at')->take(5)->get();
        $badges = Badge::orderBy('min_points')->get();
        $nextBadge = $badges->firstWhere('min_points', '>', $user->points);

        return Inertia::render('Dashboard/User', [
            'stats' => [
                'points' => $user->points,
                'active_borrowings' => $user->borrowings()->whereNull('returned_at')->count(),
                'total_books_read' => $user->borrowings()->whereNotNull('returned_at')->count(),
            ],
            'recentBorrowings' => $borrowings,
            'badges' => $badges,
            'currentBadge' => $badges->filter(fn ($b) => $b->min_points <= $user->points)->last(),
            'nextBadge' => $nextBadge,
        ]);
    }
}