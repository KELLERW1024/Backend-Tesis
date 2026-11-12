<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// Módulo "Tokens por usuario": consumo de IA de cada usuario por plan (tabla user_token_quota)
class TokenUsageController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'search'   => 'nullable|string|max:100',
            'plan_id'  => 'nullable|integer',
            'status'   => 'nullable|in:active,expired',
            'sort'     => 'nullable|in:asc,desc',
            'page'     => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        $search = trim($validated['search'] ?? '');

        $usage = DB::table('user_token_quota as q')
            ->join('users as u', 'u.id', '=', 'q.user_id')
            ->join('user_subscriptions as s', 's.id', '=', 'q.subscription_id')
            ->leftJoin('plans as p', 'p.id', '=', 'q.plan_id')
            ->when($search !== '', function ($query) use ($search) {
                $like = "%{$search}%";
                $query->where(function ($q) use ($like) {
                    $q->where('u.name', 'like', $like)
                        ->orWhere('u.last_name', 'like', $like)
                        ->orWhere('u.email', 'like', $like)
                        ->orWhereRaw("CONCAT(u.name, ' ', COALESCE(u.last_name, '')) LIKE ?", [$like]);
                });
            })
            ->when($validated['plan_id'] ?? null, fn ($query, $planId) => $query->where('q.plan_id', $planId))
            ->when($validated['status'] ?? null, fn ($query, $status) => $query->where('s.status', $status))
            ->select([
                'q.id',
                'u.id as user_id',
                'u.name',
                'u.last_name',
                'u.email',
                'q.plan_id',
                'p.name as plan_name',
                's.status as subscription_status',
                'q.tokens_used',
            ])
            ->orderBy('q.tokens_used', $validated['sort'] ?? 'desc')
            ->orderBy('q.id')
            ->paginate($validated['per_page'] ?? 10);

        return response()->json([
            'status' => 'success',
            'data'   => $usage->items(),
            'meta'   => [
                'total'        => $usage->total(),
                'current_page' => $usage->currentPage(),
                'per_page'     => $usage->perPage(),
            ],
        ], 200);
    }
}
