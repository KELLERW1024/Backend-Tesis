<?php

namespace App\Http\Controllers\Admin;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        $users = User::orderBy('id', 'desc')->get();

        return response()->json([
            'status' => 'success',
            'data' => $users
        ], 200);
    }

    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $user
        ], 200);
    }

    public function store(StoreUserRequest $request)
    {
        $user = User::create($request->validated());
  
        return response()->json([
            'status' => 'success',
            'message' => 'Usuario creado correctamente',
            'data' => $user
        ], 201);
    }

    public function update(UpdateUserRequest $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        $data = array_filter($request->validated(), fn($value) => !is_null($value));

        $user->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Usuario actualizado correctamente',
            'data' => $user
        ], 200);
    }

}
