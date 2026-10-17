<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
class StaffOnly
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if (!$user || !in_array($user->role, ['admin', 'staff'])) {
            abort(403, 'Halaman ini hanya untuk Admin dan Staff.');
        }
        return $next($request);
    }
}
