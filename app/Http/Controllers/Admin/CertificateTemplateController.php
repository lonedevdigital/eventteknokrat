<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CertificateTemplate;
use App\Models\Event;
use Illuminate\Http\Request;

class CertificateTemplateController extends Controller
{
    /**
     * List semua template yang tersedia untuk event (berdasarkan role)
     */
    public function index(Event $event)
    {
        $items = CertificateTemplate::where('event_id', $event->id)
            ->orderByRaw("CASE WHEN role='default' THEN 0 ELSE 1 END")
            ->orderBy('role')
            ->get(['id','event_id','role','name','canvas_width','canvas_height','updated_at']);

        return response()->json([
            'success' => true,
            'templates' => $items,
        ]);
    }

    /**
     * Ambil template by role.
     * Kalau role tidak ada, fallback ke default.
     */
    public function show(Event $event, Request $request)
    {
        $role = strtolower(trim($request->query('role', 'default'))) ?: 'default';

        $tpl = CertificateTemplate::where('event_id', $event->id)
            ->where('role', $role)
            ->first();

        if (!$tpl && $role !== 'default') {
            $tpl = CertificateTemplate::where('event_id', $event->id)
                ->where('role', 'default')
                ->first();
        }

        if (!$tpl) {
            $tpl = CertificateTemplate::create([
                'event_id' => $event->id,
                'role' => 'default',
                'name' => 'Default',
                'canvas_width' => 2000,
                'canvas_height' => 1414,
                'template_json' => null,
            ]);
        }

        return response()->json([
            'success' => true,
            'requested_role' => $role,
            'template' => [
                'id' => $tpl->id,
                'event_id' => $tpl->event_id,
                'role' => $tpl->role,
                'name' => $tpl->name,
                'canvas_width' => $tpl->canvas_width,
                'canvas_height' => $tpl->canvas_height,
                'template_json' => $tpl->template_json ? json_decode($tpl->template_json, true) : null,
            ],
        ]);
    }

    /**
     * Simpan template by role (upsert event_id + role)
     */
    public function store(Request $request, Event $event)
    {
        $validated = $request->validate([
            'role' => 'required|string|max:50',
            'name' => 'nullable|string|max:100',
            'canvas_width' => 'required|integer|min:300|max:10000',
            'canvas_height' => 'required|integer|min:300|max:10000',
            'template_json' => 'required|array',
        ]);

        $role = strtolower(trim($validated['role'])) ?: 'default';

        $tpl = CertificateTemplate::updateOrCreate(
            ['event_id' => $event->id, 'role' => $role],
            [
                'name' => $validated['name'] ?? ucfirst($role),
                'canvas_width' => $validated['canvas_width'],
                'canvas_height' => $validated['canvas_height'],
                'template_json' => json_encode($validated['template_json']),
            ]
        );

        return response()->json([
            'success' => true,
            'id' => $tpl->id,
            'role' => $tpl->role,
        ]);
    }
}
