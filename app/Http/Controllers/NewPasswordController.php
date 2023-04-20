<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\ForgotPwdRequest;
use App\Http\Requests\Auth\PinReset;
use App\Http\Requests\Auth\ResetPwd;
use App\Mail\User\PasswordMail;
use App\Models\ResetPin;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

class NewPasswordController extends Controller
{

    public function forgot(ForgotPwdRequest $request)
    {
        $data = $request->validated();
        $user = User::where('email', $data['email'])->first();
        if($user)
        {
            $pin_code = random_int(100000, 999999);
            while(ResetPin::where('pin_code', '=', $pin_code)->first() != null)
            {
                $pin_code = random_int(100000, 999999);
            }
            Mail::to($data['email'])->send(new PasswordMail($pin_code));
            ResetPin::create([
                'pin_code' => $pin_code,
                'expires_at' => Carbon::now()->addMinutes(15),
                'email' => $data['email']
            ]);
            return response()->json([
                'message' => 'Письмо отправлено'
            ], 200);
//            $status = Password::sendResetLink(
//                $request->only('email')
//            );
//
//            if($status == Password::RESET_LINK_SENT)
//            {
//                return [
//                    'status' => __($status)
//                ];
//            }
//            else
//            {
//                return [
//                    'email' => [trans($status)],
//                ];
//            };
        }
        else
        {
            return response()->json([
                'message' => 'Пользователь не найден'
            ], 404);
        }
    }

    public function getreset(PinReset $request) {
        $data = $request->validated();
        $reset_pin = ResetPin::where('pin_code', '=', $data['pin_code'])->first();
        if($reset_pin)
        {
            if($reset_pin->expires_at > Carbon::now()) {
                $user = User::where('email', '=', $reset_pin->email)->first();
                if ($user) {
                    return response()->json([], 200);
                } else {
                    return response()->json([
                        'message' => 'Пользователь не найден'
                    ], 404);
                }
            }
            else
            {
                return response()->json([
                    'message' => 'Срок действия PIN истёк'
                ], 405);
            }
        }
        else
        {
            return response()->json([
                'message' => 'PIN не найден или истёк срок действия'
            ], 404);
        }
    }

    public function reset(ResetPwd $request)
    {
        $data = $request->validated();
        $data['password'] = bcrypt($data['password']);
        $reset_pin = ResetPin::where('pin_code', '=', $data['pin_code'])->first();
        if($reset_pin) {
            if ($reset_pin->expires_at > Carbon::now()) {
                $user = User::whereEmail($reset_pin->email)->first();
                if ($user) {
                    $user->update(['password' => $data['password']]);
                    $reset_pin->truncate();
                    return response()->json([], 200);
                } else {
                    return response()->json([
                        'message' => 'Пользователь не найден'
                    ], 404);
                }
            } else {
                return response()->json([
                    'message' => 'У PIN истёк срок действия. Повторите сброс пароля'
                ], 405);
            }
        }
        else {
            return response()->json([
                'message' => 'PIN не найден'
            ], 404);
        }

//        $status = Password::reset(
//            $data->only('email', 'password', 'password_confirmation', 'token'),
//            function (User $user, string $password) {
//                $user->forceFill([
//                    'password' => Hash::make($password)
//                ])->setRememberToken(Str::random(60));
//
//                $user->save();
//
//                event(new PasswordReset($user));
//            }
//        );
//        if($status != Password::PASSWORD_RESET)
//        {
//            return [
//                'status' => [trans($status)],
//            ];
//        }
//        return [
//            'status' => __($status)
//        ];
    }
}
