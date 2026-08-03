<?php

namespace App\Http\Controllers;

use App\Models\BookCopy;
use App\Models\ConditionReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ConditionReportController extends Controller
{
    public function index()
    {
        $reports = ConditionReport::with(['bookCopy.book', 'reporter', 'reviewer'])
            ->latest()
            ->paginate(15);

        return Inertia::render('ConditionReports/Index', [
            'reports' => $reports,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'book_copy_id' => 'required|exists:book_copies,id',
            'condition' => 'required|in:rusak,hilang',
            'note' => 'nullable|string|max:500',
        ]);

        $copy = BookCopy::findOrFail($validated['book_copy_id']);

        if ($copy->condition !== 'baik') {
            return back()->withErrors(['book_copy_id' => 'Eksemplar ini sudah punya laporan kondisi sebelumnya.']);
        }

        ConditionReport::create([
            ...$validated,
            'reported_by' => Auth::id(),
        ]);

        return back()->with('success', 'Laporan kondisi berhasil dikirim, menunggu validasi superadmin.');
    }

    public function approve(ConditionReport $report)
    {
        if ($report->status !== 'pending') {
            return back()->withErrors(['report' => 'Laporan ini sudah diproses sebelumnya.']);
        }

        $report->bookCopy->update([
            'condition' => $report->condition,
            'status' => 'dipinjam', // dipakai sebagai penanda "tidak tersedia dipinjam"
        ]);

        $report->update([
            'status' => 'approved',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Laporan disetujui, kondisi eksemplar diperbarui.');
    }

    public function reject(Request $request, ConditionReport $report)
    {
        if ($report->status !== 'pending') {
            return back()->withErrors(['report' => 'Laporan ini sudah diproses sebelumnya.']);
        }

        $report->update([
            'status' => 'rejected',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'review_note' => $request->review_note,
        ]);

        return back()->with('success', 'Laporan ditolak.');
    }
}