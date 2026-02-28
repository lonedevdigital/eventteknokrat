<?php

namespace App\Http\Middleware;

use App\Models\Event;
use Closure;
use Illuminate\Http\Request;

class EnsureEventRoleAccess
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        $userRole = Event::normalizeRole($user->role ?? 'superuser');

        // superuser boleh semua
        if ($userRole === 'superuser') {
            return $next($request);
        }

        // ambil event dari route model binding / parameter
        $event = $request->route('event');
        if (!$event instanceof Event) {
            // kalau paramnya id doang
            $eventId = $request->route('event');
            $event = Event::find($eventId);
        }

        if (!$event) {
            abort(404);
        }

        // owner_role null dianggap hanya admin
        if (!$event->owner_role) {
            abort(403, 'Event ini hanya bisa diakses Admin.');
        }

        if (Event::normalizeRole($event->owner_role) !== $userRole) {
            abort(403, 'Kamu tidak punya akses ke event ini.');
        }

        return $next($request);
    }
}
