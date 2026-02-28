<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

class LogActivity extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', "id");
    }

    public static function record($request, $action = null, $user = null)
    {
        try {
            $route = Route::currentRouteName();
            $controller = Route::currentRouteAction();
            $url = URL::current();
            $ip = request()->ip();
            $method = request()->method();
            $headers = request()->headers->all();

            // Ambil semua data dari request (object, array, file, dsb.)
            $content = $request instanceof \Illuminate\Http\Request
                ? $request->all()
                : (is_array($request) ? $request : json_decode(json_encode($request), true));

            // Pastikan bisa diserialisasi JSON dengan aman
            $safeContent = json_encode($content, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            return static::create([
                'ip'         => $ip,
                'method'     => $method,
                'headers'    => json_encode($headers, JSON_UNESCAPED_UNICODE),
                'route'      => $route,
                'controller' => $controller,
                'content'    => $safeContent,
                'url'        => $url,
                'action'     => $action,
                'user_id'    => $user,
            ]);
        } catch (\Throwable $e) {
            return null;
        }
    }
}
