<?php

namespace App\Http\Controllers\Coupon;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;

use App\Models\Coupon;
use App\Models\Plan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class CouponController extends Controller
{
    //
    public function validateCoupon(Request $request)
    {

        $request->validate([
            'code' => 'required',
            'package_id' => 'required|exists:packages,id'
        ]);

        $coupon = Coupon::where('code', $request->code)
            ->where('is_active', 1)
            ->where(function ($q) {
                $q->whereNull('valid_until')
                ->orWhere('valid_until', '>=', now());
            })
            ->first();


        if (!$coupon) {
            Log::warning('❌ Cupón no válido o expirado', [
                'code' => $request->code
            ]);

            return response()->json([
                'valid' => false,
                'message' => 'Cupón no válido o expirado'
            ], 422);
        }

        $package = Package::findOrFail($request->package_id);

        $price = $package->local_price;

        // validar si aplica al plan
        $applies = $coupon->packages()
            ->where('packages.id', $package->id)
            ->exists();

        if (!$applies) {

            return response()->json([
                'valid' => false,
                'message' => 'Este cupón no aplica a este plan'
            ], 422);
        }

        // calcular descuento
        $discount = match ($coupon->discount_type) {
            'percentage' => round($price * ($coupon->discount_value / 100), 2),
            'fixed' => min($coupon->discount_value, $price),
            default => 0
        };

        $final = max($price - $discount, 0);

        Log::info('✅ Cupón validado correctamente');

        return response()->json([
            'valid' => true,
            'coupon' => $coupon,
            'price' => $price,
            'discount_amount' => $discount,
            'final_amount' => $final
        ]);
    }
     // LISTAR COUPONS
    public function index()
    {
        return response()->json([
            'data' => Coupon::with('packages')->get()
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
            'package_ids' => 'required|array',
            'package_ids.*' => 'exists:packages,id',
        ]);

        try {

            $coupon = DB::transaction(function () use ($validated) {

                $packageIds = $validated['package_ids'];
                unset($validated['package_ids']);

                $coupon = Coupon::create($validated);

                $coupon->packages()->sync($packageIds);

                return $coupon;
            });

            return response()->json([
                'message' => 'Cupón creado correctamente',
                'data' => $coupon->load('packages')
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
            'data' => Coupon::with('packages')->get()
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
