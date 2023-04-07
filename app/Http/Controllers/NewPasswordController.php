<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\ForgotPwdRequest;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Nette\Schema\ValidationException;

class NewPasswordController extends Controller
{

    public function forgot(ForgotPwdRequest $request)
    {
        $data = $request->validated();
        $user = User::where('email', $data['email'])->first();
        if($user)
        {
            $status = Password::sendResetLink(
                $request->only('email')
            );

            if($status == Password::RESET_LINK_SENT)
            {
                return [
                    'status' => __($status)
                ];
            }
            else
            {
                return [
                    'email' => [trans($status)],
                ];
            };
        }
        else
        {
            return response()->json([
                'message' => 'Пользователь не найден'
            ], 404);
        }
    }
    public function reset(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', 'max:254']
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );
        if($status != Password::PASSWORD_RESET)
        {
            return [
                'email' => [trans($status)],
            ];
        }
        return [
            'status' => __($status)
        ];
    }
}
