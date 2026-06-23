<?php

namespace App\Http\Controllers\Coupon;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Coupon;
use Illuminate\Support\Facades\DB;


class CouponController extends Controller
{
    //
     // LISTAR COUPONS
    public function index()
    {
        return response()->json([
            'data' => Coupon::with('plans')->get()
        ]);
    }

        // CREAR COUPON
    public function saveCoupon(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code',
            'description' => 'nullable|string|max:255',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'max_uses_per_user' => 'nullable|integer|min:1',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after_or_equal:valid_from',
            'first_purchase_only' => 'boolean',
            'is_active' => 'boolean',

            // 🔥 IMPORTANTE
            'plan_ids' => 'required|array',
            'plan_ids.*' => 'exists:plans,id',
        ]);

        try {

            $coupon = DB::transaction(function () use ($validated) {

                $planIds = $validated['plan_ids'];
                unset($validated['plan_ids']);

                $coupon = Coupon::create($validated);

                $coupon->plans()->sync($planIds);

                return $coupon;
            });

            return response()->json([
                'message' => 'Cupón creado correctamente',
                'data' => $coupon->load('plans')
            ], 201);

        } catch (\Throwable $e) {

            return response()->json([
                'message' => 'Error al crear el cupón',
                'error' => $e->getMessage()
            ], 500);
        }
    }
        // MOSTRAR UNO
    public function show($id)
    {
         return response()->json([
            'data' => Coupon::with('plans')->get()
        ]);
    }
    // ACTUALIZAR
    public function update(Request $request, $id)
    {
        $coupon = Coupon::findOrFail($id);

        $validated = $request->validate([
            'code' => 'sometimes|string|max:50|unique:coupons,code,' . $id,
            'description' => 'nullable|string|max:255',
            'discount_type' => 'sometimes|in:percentage,fixed',
            'discount_value' => 'sometimes|numeric|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'max_uses_per_user' => 'nullable|integer|min:1',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date',
            'first_purchase_only' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $coupon->update($validated);

        return response()->json([
            'message' => 'Cupón actualizado',
            'data' => $coupon
        ]);
    }

     // ELIMINAR
    public function destroy($id)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->delete();

        return response()->json([
            'message' => 'Cupón eliminado'
        ]);
    }
}
