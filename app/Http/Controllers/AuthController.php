<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Request;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $data = $request->validated();
        $data['password'] = bcrypt($data['password']);
        $user = User::create($data);
        $token = $user->createToken('restToken')->plainTextToken;

        return response()->json([
            'token' => $token
        ], 200);
    }

    public function login(LoginRequest $request)
    {
        $data = $request->validated();
        if(array_key_exists('email', $data))
        {
            $user = User::where('email', $data['email'])->first();
            if(!$user)
            {
                return response()->json([
                    'message' => 'Пользователь не найден'
                ], 404);
            }
            else
            {
                switch ($user->role_id) {
                    case 1:
                    case 2:
                        if (!$user || !Hash::check($data['password'], $user->password)) {
                            return response()->json([
                                'message' => 'Почта и пароль не совпадают'
                            ], 401);
                        } else {
//                            $token = $user->createToken('restToken', ['*'], Carbon::now()->addDays(1))->plainTextToken;
                            $token = $user->createToken('restToken')->plainTextToken;
                            return response()->json([
                                'token' => $token
                            ], 200);
                        }
                    case 3:
                        return response()->json([
                            'message' => 'Доступен вход только по PIN'
                        ], 401);
                }
            }
            return response()->json([
                'message' => 'Почта и пароль не совпадают'
            ], 401);
        }

        if(array_key_exists('pin_code', $data))
        {
            $user = User::where('pin_code', $data['pin_code'])->first();
            if(!$user)
            {
                return response()->json([
                    'message' => 'PIN не найден!'
                ], 401);
            }
            else
            {
                $token = $user->createToken('restToken', ['*'], Carbon::now()->addDays(1))->plainTextToken;

                return response()->json([
                    'token' => $token
                ], 200);
            }
        }

//        $user = User::where('email', $data['email'])->firstOrFail();
//        if(!$user)
//        {
//            return response()->json([
//                'message' => 'Пользователь не найден'
//            ], 404);
//        }
//        switch($user->role_id)
//        {
//            case 1:
//            case 2:
//                if(!Hash::check($data['password'], $user->password))
//                {
//                    return response()->json([
//                        'message' => 'Почта и пароль не совпадают'
//                    ], 401);
//                }
//                elseif (array_key_exists('pin_code', $data) && $data['pin_code'] != $user->pin_code)
//                {
//                    return response()->json([
//                        'message' => 'Почта и PIN не совпадают'
//                    ], 401);
//                }
//                else
//                {
//                    $token = $user->createToken('restToken', ['*'], Carbon::now()->addDays(1))->plainTextToken;
//
//                    return response()->json([
//                        'user' => $user,
//                        'token' => $token,
//                    ], 200);
//                }
//            case 3:
//                if(array_key_exists('password', $data))
//                {
//                    return response()->json([
//                        'message' => 'Доступен вход только по PIN'
//                    ], 401);
//                }
//                elseif($data['pin_code'] != $user->pin_code)
//                {
//                    return response()->json([
//                        'message' => 'Почта и PIN не совпадают'
//                    ], 401);
//                }
//                else
//                {
//                    $token = $user->createToken('restToken')->plainTextToken;
//
//                    return response()->json([
//                        'user' => $user,
//                        'token' => $token,
//                    ], 200);
//                }
//        }

    }
    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();
        return response()->json([
            'message' => 'Успешный выход'
        ], 200);
    }
}
