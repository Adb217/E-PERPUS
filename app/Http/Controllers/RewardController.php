<?php

namespace App\Http\Controllers;

use App\Models\Badge;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class RewardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $badges = Badge::orderBy('min_points')->get();

        $leaderboard = User::role('user')
            ->orderByDesc('points')
            ->take(10)
            ->get(['id', 'name', 'points']);

        $pointLogs = $user->pointLogs()
            ->with('borrowing.bookCopy.book')
            ->latest()
            ->take(10)
            ->get();

        return Inertia::render('Rewards/Index', [
            'badges' => $badges,
            'userPoints' => $user->points,
            'leaderboard' => $leaderboard,
            'pointLogs' => $pointLogs,
        ]);
    }
}