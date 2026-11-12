<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payments;
use App\Models\User;
use App\Models\Package;
use App\Models\UserSubscription;
use App\Models\Plan;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function getAdminStats()
    {

        $totalProcessed = Payments::where('status', 'completed')->sum('final_amount');
        $pendingPayments = Payments::where('status', 'pending')->count();
        $activeSubscriptions = UserSubscription::where('status', 'active')
            ->whereHas('payments', fn ($q) => $q->where('status', 'completed'))
            ->count();
        $totalUsers = User::count(); 

        return response()->json([
            'data' => [
                'total_processed'      => (float) $totalProcessed,
                'pending_payments'     => $pendingPayments,
                'active_subscriptions' => $activeSubscriptions,
                'total_users'          => $totalUsers,
            ]
        ], 200);
    }

    public function getMonthlyEarnings()
    {
        $now = Carbon::now();

        // Total procesado (pagos aprobados) por mes del año actual
        $totals = Payments::where('status', 'completed')
            ->whereYear('created_at', $now->year)
            ->selectRaw('MONTH(created_at) as month, SUM(final_amount) as total')
            ->groupBy('month')
            ->pluck('total', 'month');

        $months = collect(range(1, 12))->map(fn ($m) => round((float) ($totals[$m] ?? 0), 2));

        $currentTotal = $months[$now->month - 1];

        // El mes anterior puede ser diciembre del año pasado
        $previous = $now->copy()->startOfMonth()->subMonth();
        $previousTotal = (float) Payments::where('status', 'completed')
            ->whereYear('created_at', $previous->year)
            ->whereMonth('created_at', $previous->month)
            ->sum('final_amount');

        $changePct = $previousTotal > 0
            ? round(($currentTotal - $previousTotal) * 100 / $previousTotal, 1)
            : null;

        return response()->json([
            'data' => [
                'year'           => $now->year,
                'current_month'  => $now->month,
                'months'         => $months,
                'current_total'  => $currentTotal,
                'previous_total' => round($previousTotal, 2),
                'change_pct'     => $changePct,
            ]
        ], 200);
    }

    public function getPlanPerformance()
    {
        // Planes incluidos en cada suscripción con pago aprobado.
        // Si un paquete incluye varios planes, el monto se reparte en partes iguales.
        $rows = DB::table('payments as p')
            ->join('conversations as c', 'c.subscription_id', '=', 'p.subscription_id')
            ->join('user_plan as up', 'up.id', '=', 'c.user_plan_id')
            ->where('p.status', 'completed')
            ->select('p.id as payment_id', 'p.subscription_id', 'p.final_amount', 'up.plan_id')
            ->distinct()
            ->get();

        $plansPerPayment = $rows->groupBy('payment_id')->map->count();

        $stats = $rows->groupBy('plan_id')->map(fn ($items) => [
            'subscriptions' => $items->pluck('subscription_id')->unique()->count(),
            'revenue'       => $items->sum(fn ($r) => $r->final_amount / $plansPerPayment[$r->payment_id]),
        ]);

        $totalRevenue = $stats->sum('revenue');

        $data = Plan::where('is_active', 1)
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(function ($plan) use ($stats, $totalRevenue) {
                $revenue = $stats[$plan->id]['revenue'] ?? 0;

                return [
                    'id'            => $plan->id,
                    'name'          => $plan->name,
                    'subscriptions' => $stats[$plan->id]['subscriptions'] ?? 0,
                    'revenue'       => round($revenue, 2),
                    'share'         => $totalRevenue > 0 ? round($revenue * 100 / $totalRevenue, 1) : 0,
                ];
            })
            ->sortByDesc('revenue')
            ->values();

        return response()->json(['data' => $data], 200);
    }
}