<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\PlanResource;
use Illuminate\Http\Request;
use App\Models\Plan;

class PlanesController extends Controller
{
      public function index()
    {
        // Obtener todos los planes
         $plans = Plan::query()
            ->select([
                'id',
                'name',
                'code',
                'description',
                'price',
                'billing_cycle',
                'duration_days',
                'max_sections',
                'max_messages',
                'max_exports'
            ])
            ->where('is_active', true)
            //->orderBy('price', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $plans
        ]);
    }

    public function obtenerCapitulosPlan( Request $request ){
        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $userId = auth()->id();
        $planId = $request->get('idPlan');
        

        $plan = Plan::with([
            'sections' => function ($query) {
                    $query->where('is_active', true)
                        ->orderBy('order_index');
                },
            'sections.questions'
        ])->findOrFail($planId);

        // Adjuntar progress manualmente
        $plan->sections->map(function ($section) use ($userId) {

            $section->progress = \App\Models\ConversationSectionProgress::where('user_id', $userId)
                ->where('section_id', $section->id)
                ->first();

            return $section;
        });
        

        return new PlanResource($plan);

    }

    public function getPlanId(Request $request)
    {
        $idPlan = $request->get('idPlan');

        $plan = Plan::where('id', $idPlan)
            ->where('is_active', true)
            ->firstOrFail();

        return response()->json($plan);
    }

    // public function existPlanUsers()
}
