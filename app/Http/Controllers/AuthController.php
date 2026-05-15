<?php

namespace App\Http\Controllers;

use App\Models\User;
use Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Mail\ResetPasswordLinkMail;
use App\Mail\ResetPasswordCodeMail;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function login (){
        $credentials = request(['email', 'password']);

        if (! $token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
 
        return $this->respondWithToken($token);
    }
    protected function respondWithToken($token)
    {
        // $permissions = auth("api")->user()->getAllPermissions()->map(function($perm) {
        //     return $perm->name;
        // });
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            "user" => [
                "id" => auth('api')->user()->id,
                "name" => auth('api')->user()->name,
                "last_name" => auth('api')->user()->last_name,
                "email"=> auth('api')->user()->email,
                //"roles" => auth('api')->user()->getRoleNames(),
                //"permissions" => $permissions,
            ],
        ]);
    }


    public function register() {
  
        $validator = Validator::make(request()->all(), [
            'name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8',
            'role_id' => 'required|exists:roles,id'
        ]);
        $existingUser = User::where('email', request()->email)->first();

        if ($existingUser) {
            return response()->json([
                'email_exists' => 'OK'
            ], 200);
        }
 
        if($validator->fails()){
            return response()->json(
                $validator->errors()->toJson(), 400
            );
        }
 
        $user = User::create([
        'name' => request()->name,
        'last_name' => request()->last_name,
        'email' => request()->email,
        'password' => Hash::make(request()->password),
        'role_id' =>  request()->role_id,
        'is_active' => true
    ]);
 
        return response()->json($user, 201);
    }
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $existing = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if ($existing && now()->diffInMinutes($existing->created_at) < 1) {
            return response()->json([
                'error' => 'Espera antes de solicitar otro correo'
            ], 429);
        }

        $token = Str::random(60);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => Hash::make($token),
                'created_at' => now()
            ]
        );

        $link = config('app.frontend_url') . "/reset-password?token={$token}&email={$request->email}";

        try {
            Mail::to($request->email)->send(new ResetPasswordLinkMail($link));
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'No se pudo enviar el correo'
            ], 500);
        }

        return response()->json([
            "message" => "Se ha enviado un enlace de recuperación al correo"
        ]);
    }
    // ===========================================
    public function forgotPasswordCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);
        
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'error' => '400',
                'message' => 'El correo no está registrado'
            ], 200);
        }

        $existing = DB::table('password_reset_codes')
            ->where('email', $request->email)
            ->first();

        if ($existing) {
             $createdAt = Carbon::parse($existing->created_at)
                ->timezone(config('app.timezone')); // convertir a la zona de Laravel

            $secondsPassed = $createdAt->diffInSeconds(Carbon::now());

            if ($secondsPassed < 60) {
                $waitSeconds = 60 - $secondsPassed;
                return response()->json([
                    'success' => false,
                    'error' => '401',
                    'message' => 'Espera antes de solicitar otro correo',
                    'wait_seconds' => (int) $waitSeconds
                ], 200);
            }
        }
        // Generar código de 6 dígitos
        $code = mt_rand(100000, 999999);

        DB::table('password_reset_codes')->updateOrInsert(
            ['email' => $request->email],
            [
                'code' => Hash::make($code), // guardar hash del código
                'created_at' => now()
            ]
        );

        // Enviar código por correo
        try {
            Mail::to($request->email)->send(new ResetPasswordCodeMail($code));
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'No se pudo enviar el correo'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Se ha enviado un código de verificación al correo'
        ]);
    }
    // Verificacion de codigo
    public function verifyCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required'
        ]);

        $record = DB::table('password_reset_codes')
            ->where('email', $request->email)
            ->first();

        if (!$record) {
            return response()->json([
                "message" => "No existe una solicitud de recuperación para este correo"
            ], 400);
        }

        // Verificar expiración (5 minutos)
        $createdAt = Carbon::parse($record->created_at);
        if (now()->diffInMinutes($createdAt) > 5) {
            return response()->json([
                "message" => "El código ha expirado"
            ], 400);
        }
         
        // Verificar que el código ingresado coincide con el hash en la DB
        if (!Hash::check($request->code, $record->code)) {
            return response()->json([
                "message" => "Código incorrecto"
            ], 400);
        }

        return response()->json([
            "message" => "Código válido"
        ]);
    }

    // RESET CON CODIGO
    public function resetPasswordCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6',
            'code' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                "code" => 401,
                "errors" => $validator->errors()
            ], 200); // o 422 si quieres usar status HTTP real
        }

        $record = DB::table('password_reset_codes')
            ->where('email', $request->email)
            ->first();

        // Verificar que el código ingresado coincide con el hash en la DB
        if (!Hash::check($request->code, $record->code)) {
            return response()->json([
                "code" => 400,
                "message" => "Código incorrecto"
            ], 200);
        }

        // Actualizar contraseña
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                "code" => 400,
                "message" => "El correo no está registrado"
            ], 200); // o 404
        }
        
        $user->password = Hash::make($request->password);
        $user->save();

        // Eliminar código usado
        DB::table('password_reset_codes')
            ->where('email', $request->email)
            ->delete();

        return response()->json([
            "code" => 200 , 
            "message" => "Contraseña actualizada"
        ]);
    }

    // 
    public function resetPasswordLink(Request $request)
    {
            $request->validate([
            'email' => 'required|email|exists:users,email',
            'token' => 'required',
            'password' => 'required|min:6|confirmed'
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$record) {
            return response()->json(['error' => 'Solicitud inválida'], 400);
        }

        $createdAt = Carbon::parse($record->created_at);
        if (now()->diffInMinutes($createdAt) > 60) {
            return response()->json(['error' => 'El token ha expirado'], 400);
        }

        if (!Hash::check($request->token, $record->token)) {
            return response()->json(['error' => 'Token inválido'], 400);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        // Opcional: revocar sesiones/tokens de API
        // $user->tokens()->delete();

        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        return response()->json([
            'message' => 'Contraseña actualizada correctamente. Ahora puedes iniciar sesión con la nueva contraseña.'
        ]);
    } 
}
