<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    
    // Permite el acceso solo si el role_id del usuario coincide con alguno de los roles permitidos.
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = auth('api')->user();

        if (!$user) {
            return response()->json(['message' => 'No autenticado'], 401);
        }

        // Si el role_id del usuario no está entre los permitidos
        if (!in_array((string)$user->role_id, $roles)) {
            return response()->json([
                'success' => false,
                'message' => 'Acceso denegado: no cuentas con los permisos requeridos.'
            ], 403);
        }

        return $next($request);
    }
}