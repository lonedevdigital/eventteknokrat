<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\ParticipantScore;


class ScoreController extends Controller
{
    public function index(Event $event, Request $request)
    {
        $isBranchPj = $event->branches()->where('pj_user_id', auth()->id())->exists();

        // Pastikan hanya admin/PJ yang bisa akses
        if (!auth()->user()->isSuperUser() && !auth()->user()->isKemahasiswaan() && !auth()->user()->isBaak() && !auth()->user()->isPenanggungJawab() && !$isBranchPj) {
            abort(403);
        }

        $event->load(['branches']);

        $branchId = $request->filled('branch_id') ? (int) $request->query('branch_id') : null;
        if ($branchId && ! $event->branches->contains('id', $branchId)) {
            $branchId = null;
        }

        $registrantsQuery = EventRegistration::payers($event->id)
            ->with(['user', 'mahasiswa', 'branch', 'documents', 'scores']);
        if ($branchId) {
            $registrantsQuery->where('branch_id', $branchId);
        }
        $registrants = $registrantsQuery->orderBy('registered_at')->get();

        if ($event->branches->isEmpty()) {
            $criteria = $event->assessmentCriteria()->whereNull('branch_id')->get();
        } elseif ($branchId) {
            $criteria = $event->assessmentCriteria()->where('branch_id', $branchId)->get();
        } else {
            $criteria = $event->assessmentCriteria()->get();
        }

        return view('admin.lomba.penilaian', compact('event', 'registrants', 'criteria', 'branchId'));
    }

    public function store(Request $request, Event $event, EventRegistration $registration)
    {
        if ((int) $registration->event_id !== (int) $event->id) {
            abort(404);
        }

        $validated = $request->validate([
            'scores' => 'required|array',
            'scores.*' => 'numeric|min:0|max:100',
        ]);

        foreach ($validated['scores'] as $criterionId => $nilai) {
            ParticipantScore::updateOrCreate(
                [
                    'assessment_criterion_id' => $criterionId,
                    'event_registration_id' => $registration->id,
                ],
                [
                    'nilai' => $nilai,
                    'scored_by' => auth()->id(),
                ]
            );
        }

        return back()->with('success', 'Nilai berhasil disimpan.');
    }

    public function leaderboard(Event $event, Request $request)
    {
        $isBranchPj = $event->branches()->where('pj_user_id', auth()->id())->exists();

        // Pastikan hanya admin/PJ yang bisa akses
        if (!auth()->user()->isSuperUser() && !auth()->user()->isKemahasiswaan() && !auth()->user()->isBaak() && !auth()->user()->isPenanggungJawab() && !$isBranchPj) {
            abort(403);
        }

        $event->load(['branches']);

        $branchId = $request->filled('branch_id') ? (int) $request->query('branch_id') : null;
        if ($branchId && ! $event->branches->contains('id', $branchId)) {
            $branchId = null;
        }

        $registrantsQuery = EventRegistration::payers($event->id)
            ->with(['user', 'mahasiswa', 'branch', 'scores.criterion']);
        if ($branchId) {
            $registrantsQuery->where('branch_id', $branchId);
        }
        $registrants = $registrantsQuery->get();

        // Calculate total scores
        $registrants->each(function ($reg) {
            $total = 0;
            foreach ($reg->scores as $score) {
                $bobot = $score->criterion->bobot ?? 0;
                $total += ($score->nilai * $bobot) / 100;
            }
            $reg->total_score = $total;
        });

        // Sort by total score descending
        $registrants = $registrants->sortByDesc('total_score')->values();

        return view('admin.lomba.leaderboard', compact('event', 'registrants', 'branchId'));
    }
}
