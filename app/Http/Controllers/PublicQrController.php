<?php

namespace App\Http\Controllers;

use App\Models\EventQrToken;
use Illuminate\Contracts\View\View;

class PublicQrController extends Controller
{
    public function show(string $qr_token): View
    {
        $qr = EventQrToken::query()
            ->with('event')
            ->where('qr_token', $qr_token)
            ->first();

        if (!$qr || now()->greaterThan($qr->expires_at)) {
            return view('qr.expired');
        }

        return view('qr.show', [
            'qrToken' => $qr_token,
            'qr' => $qr,
            'event' => $qr->event,
        ]);
    }
}
