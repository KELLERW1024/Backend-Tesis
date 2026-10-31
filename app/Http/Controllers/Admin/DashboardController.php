<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payments;
use App\Models\User;
use App\Models\Package; 
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function getAdminStats()
    {
        // 1. Cálculos generales
        $totalRevenue = Payments::where('status', 'completed')->sum('final_amount');
        $totalSales = Payments::where('status', 'completed')->count();

        $totalPayments = Payments::count();
        $refundedCount = Payments::where('status', 'refunded')->count();
        $refundRate = $totalPayments > 0 ? round(($refundedCount / $totalPayments) * 100, 2) : 0;

        $totalUsers = User::count();
        $conversionRate = $totalUsers > 0 ? round(($totalSales / $totalUsers) * 100, 2) : 0;

        // 2. DATOS REALES PARA EL GRÁFICO DE DONA 
        $packages = Package::where('is_active', 1)->get(); 
        
        $labels = [];
        $series = [];

        foreach ($packages as $package) {
            // Guardamos el nombre de cada uno de tus paquetes reales
            $labels[] = $package->name;
            
            $ventasDelPaquete = Payments::where('status', 'completed')
                ->whereHas('subscription', function($query) use ($package) {
                    $query->where('package_id', $package->id);
                })->count();
                
            $series[] = $ventasDelPaquete;
        }

        $chartPaquetes = [
            'series' => $series,
            'labels' => $labels
        ];

        // 3. Enviamos los datos a Angular
        return response()->json([
            'data' => [
                'total_revenue'   => (float) $totalRevenue,
                'total_sales'     => $totalSales,
                'conversion_rate' => $conversionRate,
                'refund_rate'     => $refundRate,
                'chart_paquetes'  => $chartPaquetes
            ]
        ], 200);
    }
}